<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\Term;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssessmentController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403, 'Aucun profil enseignant associé à ce compte.');

        $assessments = Assessment::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'subject', 'term'])
            ->withCount('grades')
            ->latest('date')
            ->paginate(20);

        return view('teacher.assessments.index', compact('assessments'));
    }

    public function create(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        return view('teacher.assessments.create', [
            'assignments' => $this->assignments($request),
            'terms' => $this->terms($request),
            'types' => Assessment::TYPES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'class_subject_id' => ['required', 'exists:class_subject,id'],
            'term_id' => ['required', Rule::exists('terms', 'id')->whereIn('academic_year_id', $this->yearIds($request))],
            'type' => ['required', Rule::in(array_keys(Assessment::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:100'],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:10'],
        ]);

        $assignment = ClassSubject::where('id', $validated['class_subject_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assessment = Assessment::create([
            'school_class_id' => $assignment->school_class_id,
            'subject_id' => $assignment->subject_id,
            'term_id' => $validated['term_id'],
            'teacher_id' => $teacher->id,
            'created_by' => $request->user()->id,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
            'max_score' => $validated['max_score'],
            'coefficient' => $validated['coefficient'],
            'status' => 'draft',
        ]);

        $this->auditLogger->log('assessment.created', $assessment);

        return redirect()->route('teacher.grades.edit', $assessment)->with('success', 'Évaluation créée. Saisissez maintenant les notes.');
    }

    public function edit(Request $request, Assessment $assessment): View
    {
        $this->authorizeTeacher($request, $assessment);

        return view('teacher.assessments.edit', [
            'assessment' => $assessment->load(['schoolClass', 'subject', 'term']),
            'assignments' => $this->assignments($request),
            'terms' => $this->terms($request),
            'types' => Assessment::TYPES,
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeTeacher($request, $assessment);

        if ($assessment->status === 'published') {
            return back()->withErrors(['assessment' => 'Une évaluation publiée ne peut plus être modifiée.']);
        }

        $validated = $request->validate([
            'class_subject_id' => ['required', 'exists:class_subject,id'],
            'term_id' => ['required', Rule::exists('terms', 'id')->whereIn('academic_year_id', $this->yearIds($request))],
            'type' => ['required', Rule::in(array_keys(Assessment::TYPES))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date' => ['required', 'date'],
            'max_score' => ['required', 'numeric', 'min:1', 'max:100'],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:10'],
        ]);

        $assignment = ClassSubject::where('id', $validated['class_subject_id'])
            ->where('teacher_id', $request->user()->teacher->id)
            ->firstOrFail();

        $assessment->update([
            'school_class_id' => $assignment->school_class_id,
            'subject_id' => $assignment->subject_id,
            'term_id' => $validated['term_id'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'date' => $validated['date'],
            'max_score' => $validated['max_score'],
            'coefficient' => $validated['coefficient'],
        ]);

        $this->auditLogger->log('assessment.updated', $assessment);

        return redirect()->route('teacher.assessments.index')->with('success', 'Évaluation mise à jour.');
    }

    public function publish(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeTeacher($request, $assessment);

        if ($assessment->grades()->whereNotNull('score')->count() === 0) {
            return back()->withErrors(['assessment' => 'Saisissez au moins une note avant de publier.']);
        }

        DB::transaction(function () use ($assessment) {
            $assessment->update(['status' => 'published', 'published_at' => now()]);
            $assessment->grades()->whereNotNull('score')->update([
                'status' => 'published',
                'published_at' => now(),
            ]);
        });

        $this->auditLogger->log('assessment.published', $assessment);

        return back()->with('success', 'Évaluation et notes publiées. Elles sont désormais visibles par les élèves.');
    }

    private function assignments(Request $request)
    {
        return ClassSubject::where('teacher_id', $request->user()->teacher->id)
            ->whereIn('academic_year_id', $this->yearIds($request))
            ->with(['schoolClass', 'subject'])
            ->get();
    }

    private function terms(Request $request)
    {
        return Term::whereIn('academic_year_id', $this->yearIds($request))->orderBy('sequence')->get();
    }

    private function yearIds(Request $request): array
    {
        return $request->user()->school?->academicYears()->pluck('id')->all() ?? [];
    }

    private function authorizeTeacher(Request $request, Assessment $assessment): void
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher && $assessment->teacher_id === $teacher->id, 403);
    }
}
