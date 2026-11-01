<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class GradeController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function edit(Request $request, Assessment $assessment): View
    {
        $this->authorizeTeacher($request, $assessment);

        $students = Enrollment::where('school_class_id', $assessment->school_class_id)
            ->where('academic_year_id', $assessment->schoolClass->academic_year_id)
            ->where('status', 'enrolled')
            ->with('student')
            ->orderBy('roll_number')
            ->get();

        $grades = $assessment->grades()->get()->keyBy('student_id');

        return view('teacher.grades.edit', [
            'assessment' => $assessment->load(['schoolClass', 'subject', 'term']),
            'students' => $students,
            'grades' => $grades,
        ]);
    }

    public function update(Request $request, Assessment $assessment): RedirectResponse
    {
        $this->authorizeTeacher($request, $assessment);

        $validated = $request->validate([
            'scores' => ['required', 'array'],
            'scores.*' => ['nullable', 'numeric', 'min:0', 'max:'.$assessment->max_score],
            'comments' => ['nullable', 'array'],
            'comments.*' => ['nullable', 'string', 'max:1000'],
        ]);

        $studentIds = Enrollment::where('school_class_id', $assessment->school_class_id)
            ->where('academic_year_id', $assessment->schoolClass->academic_year_id)
            ->where('status', 'enrolled')
            ->pluck('student_id')
            ->all();

        DB::transaction(function () use ($assessment, $validated, $request, $studentIds) {
            foreach ($studentIds as $studentId) {
                $score = $validated['scores'][$studentId] ?? null;
                $comment = $validated['comments'][$studentId] ?? null;

                if ($score === null || $score === '') {
                    Grade::where('assessment_id', $assessment->id)->where('student_id', $studentId)->delete();

                    continue;
                }

                Grade::updateOrCreate(
                    ['assessment_id' => $assessment->id, 'student_id' => $studentId],
                    [
                        'score' => $score,
                        'comment' => $comment,
                        'status' => $assessment->isPublished() ? 'published' : 'draft',
                        'entered_by' => $request->user()->id,
                        'published_at' => $assessment->isPublished() ? now() : null,
                    ],
                );
            }
        });

        $this->auditLogger->log('grades.updated', $assessment);

        return redirect()->route('teacher.assessments.index')->with('success', 'Notes enregistrées.');
    }

    private function authorizeTeacher(Request $request, Assessment $assessment): void
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher && $assessment->teacher_id === $teacher->id, 403);
    }
}
