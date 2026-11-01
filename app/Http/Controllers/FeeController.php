<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\Invoice;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FeeController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $classes = $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect();
        $categories = $year ? FeeCategory::where('academic_year_id', $year->id)->orderBy('name')->get() : collect();
        $selectedClass = $request->filled('class') ? $classes->firstWhere('id', $request->integer('class')) : null;

        $fees = Fee::query()
            ->where('academic_year_id', $year?->id)
            ->with(['student', 'category'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->whereHas('student', fn ($student) => $student->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('matricule', 'like', $term));
            })
            ->when($selectedClass, fn ($query) => $query->whereHas('student.enrollments', fn ($enrollment) => $enrollment->where('school_class_id', $selectedClass->id)))
            ->when($request->filled('category'), fn ($query) => $query->where('fee_category_id', $request->integer('category')))
            ->orderBy('due_date')
            ->paginate(40)
            ->withQueryString();

        return view('admin.fees.index', [
            'fees' => $fees,
            'classes' => $classes,
            'categories' => $categories,
            'selectedClass' => $selectedClass,
            'year' => $year,
            'total' => (float) Fee::where('academic_year_id', $year?->id)->sum('amount'),
        ]);
    }

    public function create(Request $request): View
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        return view('admin.fees.create', [
            'students' => Student::where('school_id', $request->user()->school_id)->where('status', 'active')->orderBy('last_name')->get(),
            'categories' => FeeCategory::where('academic_year_id', $year->id)->orderBy('name')->get(),
            'classes' => SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get(),
            'year' => $year,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        $validated = $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $request->user()->school_id)],
            'fee_category_id' => ['required', Rule::exists('fee_categories', 'id')->where('academic_year_id', $year->id)],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $invoiceExists = Invoice::where('student_id', $validated['student_id'])->where('academic_year_id', $year->id)->where('status', '!=', 'cancelled')->exists();

        if ($invoiceExists) {
            return back()->withErrors(['student_id' => 'Une facture existe déjà pour cet élève. Modifiez la facture avant d’ajouter de nouveaux frais.'])->withInput();
        }

        $fee = Fee::updateOrCreate(
            [
                'student_id' => $validated['student_id'],
                'fee_category_id' => $validated['fee_category_id'],
                'academic_year_id' => $year->id,
            ],
            [
                'amount' => $validated['amount'],
                'due_date' => $validated['due_date'] ?? null,
                'status' => 'pending',
            ],
        );

        $this->auditLogger->log('fee.saved', $fee);

        return redirect()->route('admin.fees.index')->with('success', 'Frais enregistré.');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        $validated = $request->validate([
            'school_class_id' => ['required', Rule::exists('school_classes', 'id')->where('academic_year_id', $year->id)],
            'fee_category_id' => ['required', Rule::exists('fee_categories', 'id')->where('academic_year_id', $year->id)],
            'amount' => ['required', 'numeric', 'min:0'],
            'due_date' => ['nullable', 'date'],
        ]);

        $studentIds = Enrollment::where('school_class_id', $validated['school_class_id'])
            ->where('academic_year_id', $year->id)
            ->where('status', 'enrolled')
            ->pluck('student_id');

        $created = 0;
        $skipped = 0;

        DB::transaction(function () use ($validated, $year, $studentIds, &$created, &$skipped) {
            foreach ($studentIds as $studentId) {
                if (Invoice::where('student_id', $studentId)->where('academic_year_id', $year->id)->where('status', '!=', 'cancelled')->exists()) {
                    $skipped++;

                    continue;
                }

                Fee::updateOrCreate(
                    ['student_id' => $studentId, 'fee_category_id' => $validated['fee_category_id'], 'academic_year_id' => $year->id],
                    ['amount' => $validated['amount'], 'due_date' => $validated['due_date'] ?? null, 'status' => 'pending'],
                );
                $created++;
            }
        });

        $this->auditLogger->log('fee.bulk_saved', null, [], ['count' => $created, 'skipped' => $skipped, 'category_id' => $validated['fee_category_id']]);

        return redirect()->route('admin.fees.index')->with('success', "{$created} frais enregistrés. {$skipped} élèves ignorés car ils ont déjà une facture.");
    }

    public function destroy(Request $request, Fee $fee): RedirectResponse
    {
        abort_unless($fee->student?->school_id === $request->user()->school_id, 404);

        $invoiceExists = Invoice::where('student_id', $fee->student_id)->where('academic_year_id', $fee->academic_year_id)->where('status', '!=', 'cancelled')->exists();

        if ($invoiceExists) {
            return back()->withErrors(['fee' => 'Ce frais est déjà facturé et ne peut pas être supprimé.']);
        }

        $this->auditLogger->log('fee.deleted', $fee);
        $fee->delete();

        return back()->with('success', 'Frais supprimé.');
    }
}
