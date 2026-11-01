<?php

namespace App\Services;

use App\Models\Assessment;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use Illuminate\Support\Collection;

class AcademicResults
{
    /**
     * @return array{student: Student, term: Term, subjects: Collection, average: ?float, total: float, total_coefficients: float, rank: ?int, mention: string}
     */
    public function forStudent(Student $student, Term $term): array
    {
        $enrollment = $student->enrollments()->where('academic_year_id', $term->academic_year_id)->with('schoolClass')->first();

        $subjects = collect();

        if ($enrollment) {
            $subjects = $this->subjectResults($student, $term, $enrollment->schoolClass);
        }

        $total = round($subjects->sum('weighted_average'), 2);
        $coefficients = round($subjects->sum('coefficient'), 2);
        $average = $coefficients > 0 ? round($total / $coefficients, 2) : null;

        $rank = $enrollment ? $this->rankFor($student, $term, $enrollment->schoolClass) : null;

        return [
            'student' => $student,
            'term' => $term,
            'enrollment' => $enrollment,
            'subjects' => $subjects,
            'average' => $average,
            'total' => $total,
            'total_coefficients' => $coefficients,
            'rank' => $rank,
            'mention' => $average === null ? 'Non calculable' : $this->mention($average),
        ];
    }

    public function subjectResults(Student $student, Term $term, SchoolClass $schoolClass): Collection
    {
        return $schoolClass->classSubjects()
            ->where('academic_year_id', $term->academic_year_id)
            ->with(['subject', 'teacher'])
            ->get()
            ->map(function ($classSubject) use ($student, $term) {
                $grades = Grade::query()
                    ->where('student_id', $student->id)
                    ->where('status', 'published')
                    ->whereHas('assessment', function ($query) use ($classSubject, $term) {
                        $query->where('subject_id', $classSubject->subject_id)
                            ->where('school_class_id', $classSubject->school_class_id)
                            ->where('term_id', $term->id);
                    })
                    ->with('assessment')
                    ->get();

                $weighted = 0.0;
                $weights = 0.0;

                foreach ($grades as $grade) {
                    if ($grade->score === null || (float) $grade->assessment->max_score === 0.0) {
                        continue;
                    }

                    $normalized = ((float) $grade->score / (float) $grade->assessment->max_score) * 20;
                    $weight = (float) ($grade->assessment->coefficient ?: 1);
                    $weighted += $normalized * $weight;
                    $weights += $weight;
                }

                $average = $weights > 0 ? round($weighted / $weights, 2) : null;
                $coefficient = (float) ($classSubject->coefficient ?? $classSubject->subject->coefficient ?? 1);

                return [
                    'subject' => $classSubject->subject,
                    'teacher' => $classSubject->teacher,
                    'grades' => $grades,
                    'average' => $average,
                    'coefficient' => $coefficient,
                    'weighted_average' => $average === null ? 0.0 : round($average * $coefficient, 2),
                    'has_grades' => $grades->isNotEmpty(),
                ];
            });
    }

    public function rankFor(Student $student, Term $term, SchoolClass $schoolClass): ?int
    {
        $averages = $this->classAverages($schoolClass, $term);

        $position = $averages->search(fn ($row) => $row['student_id'] === $student->id);

        return $position === false ? null : $position + 1;
    }

    public function classAverages(SchoolClass $schoolClass, Term $term): Collection
    {
        $students = $schoolClass->activeStudents()->get();

        return $students->map(function (Student $student) use ($term) {
            $results = $this->subjectResults($student, $term, $schoolClass);
            $total = $results->sum('weighted_average');
            $coefficients = $results->sum('coefficient');

            return [
                'student_id' => $student->id,
                'student' => $student,
                'average' => $coefficients > 0 ? round($total / $coefficients, 2) : null,
                'has_grades' => $results->contains('has_grades', true),
            ];
        })->sortByDesc(fn ($row) => $row['average'] ?? -1)->values();
    }

    public function mention(?float $average): string
    {
        return match (true) {
            $average === null => 'Non calculable',
            $average >= 16 => 'Très bien',
            $average >= 14 => 'Bien',
            $average >= 12 => 'Assez bien',
            $average >= 10 => 'Passable',
            default => 'Insuffisant',
        };
    }

    public function successRate(SchoolClass $schoolClass, Term $term): ?float
    {
        $averages = $this->classAverages($schoolClass, $term)->filter(fn ($row) => $row['average'] !== null);

        if ($averages->isEmpty()) {
            return null;
        }

        $passed = $averages->where('average', '>=', 10)->count();

        return round(($passed / $averages->count()) * 100, 2);
    }

    public function attendanceStats(?SchoolClass $schoolClass = null): array
    {
        $query = \App\Models\AttendanceRecord::query();

        if ($schoolClass) {
            $query->where('school_class_id', $schoolClass->id);
        }

        $total = (clone $query)->count();
        $present = (clone $query)->where('status', 'present')->count();
        $absences = (clone $query)->where('status', 'absent')->count();
        $late = (clone $query)->where('status', 'late')->count();

        return [
            'total' => $total,
            'present' => $present,
            'absences' => $absences,
            'late' => $late,
            'rate' => $total > 0 ? round(($present / $total) * 100, 2) : null,
        ];
    }
}
