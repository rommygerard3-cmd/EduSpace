<?php

namespace App\Http\Controllers;

use App\Models\ConductNote;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConductNoteController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $terms = Term::whereHas('academicYear', fn ($query) => $query->where('school_id', $request->user()->school_id))
            ->with('academicYear')
            ->orderByDesc('start_date')
            ->get();

        $selectedTerm = $request->filled('term')
            ? $terms->firstWhere('id', $request->integer('term'))
            : $terms->first();

        $classes = SchoolClass::where('school_id', $request->user()->school_id)->orderBy('name')->get();
        $selectedClass = $request->filled('class') ? $classes->firstWhere('id', $request->integer('class')) : null;

        $notes = ConductNote::query()
            ->whereHas('student', fn ($query) => $query->where('school_id', $request->user()->school_id))
            ->when($selectedTerm, fn ($query) => $query->where('term_id', $selectedTerm->id))
            ->when($selectedClass, fn ($query) => $query->whereHas('student.enrollments', fn ($enrollment) => $enrollment->where('school_class_id', $selectedClass->id)))
            ->with(['student', 'term', 'recorder'])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        $students = Student::where('school_id', $request->user()->school_id)
            ->where('status', 'active')
            ->when($selectedClass, fn ($query) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment->where('school_class_id', $selectedClass->id)))
            ->orderBy('last_name')
            ->get();

        return view('discipline.conduct.index', [
            'notes' => $notes,
            'terms' => $terms,
            'classes' => $classes,
            'selectedTerm' => $selectedTerm,
            'selectedClass' => $selectedClass,
            'students' => $students,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $request->user()->school_id)],
            'term_id' => ['required', Rule::exists('terms', 'id')->whereIn('academic_year_id', $request->user()->school?->academicYears()->pluck('id')->all() ?? [])],
            'score' => ['required', 'numeric', 'min:0', 'max:20'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $note = ConductNote::updateOrCreate(
            ['student_id' => $validated['student_id'], 'term_id' => $validated['term_id']],
            [
                'score' => $validated['score'],
                'comment' => $validated['comment'] ?? null,
                'recorded_by' => $request->user()->id,
            ],
        );

        $this->auditLogger->log('conduct_note.saved', $note);

        return back()->with('success', 'Note de conduite enregistrée.');
    }
}
