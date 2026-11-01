<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\PreparationSheet;
use App\Models\Term;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PreparationController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $preparations = PreparationSheet::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'subject', 'term'])
            ->latest('date')
            ->paginate(20);

        return view('teacher.preparations.index', compact('preparations'));
    }

    public function create(Request $request): View
    {
        return view('teacher.preparations.create', $this->formData($request));
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $validated = $this->validatePreparation($request);
        $assignment = ClassSubject::where('id', $validated['class_subject_id'])->where('teacher_id', $teacher->id)->firstOrFail();

        $preparation = PreparationSheet::create([
            'teacher_id' => $teacher->id,
            'school_class_id' => $assignment->school_class_id,
            'subject_id' => $assignment->subject_id,
            'class_subject_id' => $assignment->id,
            'academic_year_id' => $assignment->academic_year_id,
            ...$validated,
            'status' => 'draft',
        ]);

        $this->auditLogger->log('preparation.created', $preparation);

        return redirect()->route('teacher.preparations.index')->with('success', 'Fiche de préparation enregistrée en brouillon.');
    }

    public function edit(Request $request, PreparationSheet $preparation): View
    {
        $this->authorizeTeacher($request, $preparation);

        return view('teacher.preparations.edit', [
            'preparation' => $preparation,
            ...$this->formData($request),
        ]);
    }

    public function update(Request $request, PreparationSheet $preparation): RedirectResponse
    {
        $this->authorizeTeacher($request, $preparation);

        if (in_array($preparation->status, ['submitted', 'approved'], true)) {
            return back()->withErrors(['preparation' => 'Cette fiche ne peut plus être modifiée dans son état actuel.']);
        }

        $validated = $this->validatePreparation($request);
        $assignment = ClassSubject::where('id', $validated['class_subject_id'])
            ->where('teacher_id', $request->user()->teacher->id)
            ->firstOrFail();

        $preparation->update([
            'school_class_id' => $assignment->school_class_id,
            'subject_id' => $assignment->subject_id,
            'class_subject_id' => $assignment->id,
            'academic_year_id' => $assignment->academic_year_id,
            ...$validated,
        ]);

        $this->auditLogger->log('preparation.updated', $preparation);

        return redirect()->route('teacher.preparations.index')->with('success', 'Fiche mise à jour.');
    }

    public function submit(Request $request, PreparationSheet $preparation): RedirectResponse
    {
        $this->authorizeTeacher($request, $preparation);

        if (! in_array($preparation->status, ['draft', 'revision'], true)) {
            return back()->withErrors(['preparation' => 'Cette fiche ne peut pas être soumise dans son état actuel.']);
        }

        $preparation->update(['status' => 'submitted', 'submitted_at' => now()]);
        $this->auditLogger->log('preparation.submitted', $preparation);

        return back()->with('success', 'Fiche soumise au préfet pour examen.');
    }

    public function reviewIndex(Request $request): View
    {
        $preparations = PreparationSheet::whereHas('academicYear', fn ($query) => $query->where('school_id', $request->user()->school_id))
            ->with(['teacher', 'schoolClass', 'subject', 'term'])
            ->whereIn('status', ['submitted', 'reviewed', 'approved', 'revision'])
            ->latest('submitted_at')
            ->paginate(20);

        return view('prefect.preparations.index', compact('preparations'));
    }

    public function reviewShow(Request $request, PreparationSheet $preparation): View
    {
        $this->authorizeReviewer($request, $preparation);

        return view('prefect.preparations.show', [
            'preparation' => $preparation->load(['teacher', 'schoolClass', 'subject', 'term', 'reviewer']),
        ]);
    }

    public function review(Request $request, PreparationSheet $preparation): RedirectResponse
    {
        $this->authorizeReviewer($request, $preparation);

        $validated = $request->validate([
            'decision' => ['required', Rule::in(['approved', 'revision', 'reviewed'])],
            'review_notes' => ['nullable', 'string', 'max:3000', Rule::requiredIf($request->input('decision') === 'revision')],
        ]);

        $preparation->update([
            'status' => $validated['decision'],
            'reviewer_id' => $request->user()->id,
            'review_notes' => $validated['review_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        $this->auditLogger->log('preparation.reviewed', $preparation, [], ['decision' => $validated['decision']]);

        return redirect()->route('prefect.preparations.index')->with('success', 'Fiche examinée.');
    }

    private function validatePreparation(Request $request): array
    {
        return $request->validate([
            'class_subject_id' => ['required', 'exists:class_subject,id'],
            'term_id' => ['required', 'exists:terms,id'],
            'date' => ['required', 'date'],
            'topic' => ['required', 'string', 'max:255'],
            'objectives' => ['required', 'string', 'max:5000'],
            'prerequisites' => ['nullable', 'string', 'max:3000'],
            'materials' => ['nullable', 'string', 'max:3000'],
            'steps' => ['required', 'string', 'max:10000'],
            'evaluation_methods' => ['nullable', 'string', 'max:3000'],
        ]);
    }

    private function formData(Request $request): array
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        return [
            'assignments' => ClassSubject::where('teacher_id', $teacher->id)->with(['schoolClass', 'subject'])->get(),
            'terms' => Term::whereHas('academicYear', fn ($query) => $query->where('school_id', $request->user()->school_id))->orderBy('sequence')->get(),
        ];
    }

    private function authorizeTeacher(Request $request, PreparationSheet $preparation): void
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher && $preparation->teacher_id === $teacher->id, 403);
    }

    private function authorizeReviewer(Request $request, PreparationSheet $preparation): void
    {
        abort_unless($preparation->academicYear?->school_id === $request->user()->school_id, 404);
    }
}
