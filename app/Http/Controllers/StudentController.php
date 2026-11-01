<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Enrollment;
use App\Models\Guardian;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use App\Services\ActivationService;
use App\Services\AuditLogger;
use App\Services\MatriculeGenerator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function __construct(
        private readonly MatriculeGenerator $matriculeGenerator,
        private readonly ActivationService $activationService,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function index(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        $students = Student::query()
            ->where('school_id', $school?->id)
            ->with(['user', 'enrollments.schoolClass'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('matricule', 'like', $term));
            })
            ->when($request->filled('class'), fn ($query) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment->where('school_class_id', $request->integer('class'))))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.students.index', [
            'students' => $students,
            'classes' => $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect(),
            'year' => $year,
        ]);
    }

    public function create(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        return view('admin.students.create', [
            'student' => new Student,
            'year' => $year,
            'classes' => $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'matricule' => ['nullable', 'string', 'max:50', Rule::unique('students', 'matricule'), Rule::unique('users', 'matricule')],
            'gender' => ['nullable', 'in:M,F'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'create_account' => ['nullable', 'boolean'],
            'email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'guardian_first_name' => ['nullable', 'string', 'max:255'],
            'guardian_last_name' => ['nullable', 'string', 'max:255'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $class = SchoolClass::findOrFail($validated['school_class_id']);

        abort_unless($year && $class->academic_year_id === $year->id, 422, 'La classe ne correspond pas à l’année scolaire active.');

        $activationCode = null;

        $student = DB::transaction(function () use ($validated, $school, $year, $class, $request, &$activationCode) {
            $matricule = $validated['matricule'] ?: $this->matriculeGenerator->forStudent((int) substr($year->name, 0, 4));

            $student = Student::create([
                'school_id' => $school->id,
                'matricule' => $matricule,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'birth_place' => $validated['birth_place'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => 'active',
            ]);

            if ($request->boolean('create_account')) {
                $user = User::create([
                    'school_id' => $school->id,
                    'name' => $student->fullName(),
                    'email' => $validated['email'] ?? null,
                    'matricule' => $matricule,
                    'phone' => $student->phone,
                    'password' => Hash::make(Str::password(32)),
                    'is_active' => true,
                ]);
                $user->assignRole(Role::where('name', Role::ELEVE)->firstOrFail());
                $student->update(['user_id' => $user->id]);
                $activationCode = $this->activationService->prepare($user);
            }

            $roll = (int) Enrollment::where('school_class_id', $class->id)->max('roll_number') + 1;

            Enrollment::create([
                'student_id' => $student->id,
                'school_class_id' => $class->id,
                'academic_year_id' => $year->id,
                'roll_number' => $roll,
                'status' => 'enrolled',
                'enrolled_at' => now(),
            ]);

            if (! empty($validated['guardian_last_name'])) {
                $guardian = Guardian::create([
                    'school_id' => $school->id,
                    'first_name' => $validated['guardian_first_name'] ?? '',
                    'last_name' => $validated['guardian_last_name'],
                    'relationship' => $validated['guardian_relationship'] ?? null,
                    'phone' => $validated['guardian_phone'] ?? null,
                ]);
                $student->guardians()->attach($guardian->id);
            }

            $this->auditLogger->log('student.created', $student, [], ['matricule' => $matricule, 'class' => $class->name]);

            return $student;
        });

        $message = "Élève {$student->fullName()} inscrit avec le matricule {$student->matricule}.";

        if ($activationCode) {
            $message .= " Code d’activation à usage unique : {$activationCode}.";
        }

        return redirect()->route('admin.students.show', $student)->with('success', $message);
    }

    public function show(Student $student): View
    {
        $student->load([
            'user', 'guardians', 'enrollments.schoolClass.level', 'enrollments.academicYear',
            'invoices.payments', 'grades.assessment.subject', 'attendanceRecords',
            'conductNotes.term', 'convocations', 'incidents',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Request $request, Student $student): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $enrollment = $year ? $student->enrollments()->where('academic_year_id', $year->id)->first() : null;

        return view('admin.students.edit', [
            'student' => $student->load(['user', 'guardians']),
            'year' => $year,
            'classes' => $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect(),
            'currentEnrollment' => $enrollment,
        ]);
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:M,F'],
            'birth_date' => ['nullable', 'date'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($student->user_id)],
            'status' => ['required', 'in:active,archived'],
        ]);

        $old = $student->toArray();

        DB::transaction(function () use ($student, $validated, $request) {
            $student->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'] ?? null,
                'birth_date' => $validated['birth_date'] ?? null,
                'birth_place' => $validated['birth_place'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'status' => $validated['status'],
            ]);

            if ($student->user) {
                $student->user->update([
                    'name' => $student->fullName(),
                    'email' => $validated['email'] ?? null,
                    'phone' => $student->phone,
                    'is_active' => $validated['status'] === 'active',
                ]);
            }

            if (! empty($validated['school_class_id'])) {
                $year = $request->user()->school?->defaultAcademicYear();
                $class = SchoolClass::find($validated['school_class_id']);

                if ($year && $class && $class->academic_year_id === $year->id) {
                    $enrollment = Enrollment::firstOrNew([
                        'student_id' => $student->id,
                        'academic_year_id' => $year->id,
                    ]);
                    $enrollment->school_class_id = $class->id;
                    $enrollment->roll_number = $enrollment->roll_number ?: ((int) Enrollment::where('school_class_id', $class->id)->max('roll_number') + 1);
                    $enrollment->status = 'enrolled';
                    $enrollment->enrolled_at = $enrollment->enrolled_at ?: now();
                    $enrollment->save();
                }
            }
        });

        $this->auditLogger->log('student.updated', $student, $old, $student->fresh()->toArray());

        return redirect()->route('admin.students.show', $student)->with('success', 'Élève mis à jour.');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $student->update(['status' => 'archived']);
        $student->user?->update(['is_active' => false]);
        $this->auditLogger->log('student.archived', $student);

        return redirect()->route('admin.students.index')->with('success', 'Élève archivé. Les données historiques sont conservées.');
    }

    public function archive(Student $student): RedirectResponse
    {
        return $this->destroy($student);
    }

    public function restore(Student $student): RedirectResponse
    {
        $student->update(['status' => 'active']);
        $student->user?->update(['is_active' => true]);
        $this->auditLogger->log('student.restored', $student);

        return back()->with('success', 'Élève réactivé.');
    }

    public function createAccount(Student $student): RedirectResponse
    {
        if ($student->user) {
            return back()->withErrors(['student' => 'Cet élève possède déjà un compte.']);
        }

        $code = DB::transaction(function () use ($student) {
            $user = User::create([
                'school_id' => $student->school_id,
                'name' => $student->fullName(),
                'matricule' => $student->matricule,
                'phone' => $student->phone,
                'password' => Hash::make(Str::password(32)),
                'is_active' => true,
            ]);
            $user->assignRole(Role::where('name', Role::ELEVE)->firstOrFail());
            $student->update(['user_id' => $user->id]);

            return $this->activationService->prepare($user);
        });

        $this->auditLogger->log('student.account_created', $student);

        return back()->with('success', "Compte élève créé. Code d’activation à usage unique : {$code}")->with('activation_code', $code);
    }
}
