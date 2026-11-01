<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Teacher;
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

class TeacherController extends Controller
{
    public function __construct(
        private readonly MatriculeGenerator $matriculeGenerator,
        private readonly ActivationService $activationService,
        private readonly AuditLogger $auditLogger,
    ) {}

    public function index(Request $request): View
    {
        $teachers = Teacher::query()
            ->where('school_id', $request->user()->school_id)
            ->with(['user', 'classSubjects.schoolClass', 'classSubjects.subject'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('employee_number', 'like', $term));
            })
            ->orderBy('last_name')
            ->paginate(20)
            ->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create(): View
    {
        return view('admin.teachers.create', ['teacher' => new Teacher]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:M,F'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'employee_number' => ['nullable', 'string', 'max:50', 'unique:teachers,employee_number'],
            'create_account' => ['nullable', 'boolean'],
            'matricule' => ['nullable', 'string', 'max:50', 'unique:users,matricule'],
            'account_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
        ]);

        $activationCode = null;

        $teacher = DB::transaction(function () use ($validated, $request, &$activationCode) {
            $teacher = Teacher::create([
                'school_id' => $request->user()->school_id,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'hire_date' => $validated['hire_date'] ?? null,
                'employee_number' => $validated['employee_number'] ?? null,
            ]);

            if ($request->boolean('create_account')) {
                $matricule = $validated['matricule'] ?: $this->matriculeGenerator->forUser('ENS');

                $user = User::create([
                    'school_id' => $request->user()->school_id,
                    'name' => $teacher->fullName(),
                    'email' => $validated['account_email'] ?? $validated['email'] ?? null,
                    'matricule' => $matricule,
                    'phone' => $teacher->phone,
                    'password' => Hash::make(Str::password(32)),
                    'is_active' => true,
                ]);
                $user->assignRole(Role::where('name', Role::ENSEIGNANT)->firstOrFail());
                $teacher->update(['user_id' => $user->id]);
                $activationCode = $this->activationService->prepare($user);
            }

            $this->auditLogger->log('teacher.created', $teacher);

            return $teacher;
        });

        $message = "Enseignant {$teacher->fullName()} créé.";
        if ($activationCode) {
            $message .= " Code d’activation à usage unique : {$activationCode}.";
        }

        return redirect()->route('admin.teachers.show', $teacher)->with('success', $message);
    }

    public function show(Teacher $teacher): View
    {
        $teacher->load([
            'user', 'classSubjects.schoolClass', 'classSubjects.subject',
            'schedules.schoolClass', 'schedules.subject', 'assessments.subject',
            'preparationSheets',
        ]);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher): View
    {
        return view('admin.teachers.edit', ['teacher' => $teacher->load('user')]);
    }

    public function update(Request $request, Teacher $teacher): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'gender' => ['nullable', 'in:M,F'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'employee_number' => ['nullable', 'string', 'max:50', Rule::unique('teachers', 'employee_number')->ignore($teacher->id)],
            'account_email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->user_id)],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $old = $teacher->toArray();

        DB::transaction(function () use ($teacher, $validated, $request) {
            $teacher->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'gender' => $validated['gender'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'hire_date' => $validated['hire_date'] ?? null,
                'employee_number' => $validated['employee_number'] ?? null,
            ]);

            if ($teacher->user) {
                $teacher->user->update([
                    'name' => $teacher->fullName(),
                    'email' => $validated['account_email'] ?? $validated['email'] ?? null,
                    'phone' => $teacher->phone,
                    'is_active' => $request->boolean('is_active', $teacher->user->is_active),
                ]);
            }
        });

        $this->auditLogger->log('teacher.updated', $teacher, $old, $teacher->fresh()->toArray());

        return redirect()->route('admin.teachers.show', $teacher)->with('success', 'Enseignant mis à jour.');
    }

    public function destroy(Teacher $teacher): RedirectResponse
    {
        $teacher->user?->update(['is_active' => false]);
        $this->auditLogger->log('teacher.deactivated', $teacher);

        return redirect()->route('admin.teachers.index')->with('success', 'Compte enseignant désactivé. Les données historiques sont conservées.');
    }

    public function createAccount(Teacher $teacher): RedirectResponse
    {
        if ($teacher->user) {
            return back()->withErrors(['teacher' => 'Cet enseignant possède déjà un compte.']);
        }

        $code = DB::transaction(function () use ($teacher) {
            $user = User::create([
                'school_id' => $teacher->school_id,
                'name' => $teacher->fullName(),
                'email' => $teacher->email,
                'matricule' => $this->matriculeGenerator->forUser('ENS'),
                'phone' => $teacher->phone,
                'password' => Hash::make(Str::password(32)),
                'is_active' => true,
            ]);
            $user->assignRole(Role::where('name', Role::ENSEIGNANT)->firstOrFail());
            $teacher->update(['user_id' => $user->id]);

            return $this->activationService->prepare($user);
        });

        $this->auditLogger->log('teacher.account_created', $teacher);

        return back()->with('success', "Compte enseignant créé. Code d’activation à usage unique : {$code}")->with('activation_code', $code);
    }
}
