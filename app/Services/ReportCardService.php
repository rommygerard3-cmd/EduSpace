<?php

namespace App\Services;

use App\Models\ConductNote;
use App\Models\Setting;
use App\Models\Student;
use App\Models\Term;

class ReportCardService
{
    public function __construct(private readonly AcademicResults $results) {}

    /**
     * @return array<string, mixed>
     */
    public function data(Student $student, Term $term): array
    {
        $data = $this->results->forStudent($student, $term);
        $enrollment = $data['enrollment'];
        $conduct = ConductNote::where('student_id', $student->id)->where('term_id', $term->id)->first();
        $passMark = (float) Setting::getValue('pass_mark', 10, $student->school_id);

        $decision = match (true) {
            $data['average'] === null => 'Non calculable',
            $data['average'] >= $passMark => 'Réussite',
            default => 'Échec',
        };

        return [
            ...$data,
            'school' => $student->school,
            'school_class' => $enrollment?->schoolClass,
            'academic_year' => $term->academicYear,
            'conduct' => $conduct,
            'pass_mark' => $passMark,
            'decision' => $decision,
            'generated_at' => now(),
        ];
    }
}
