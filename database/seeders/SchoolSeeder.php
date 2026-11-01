<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\Term;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['code' => 'EDUSPACE'],
            [
                'name' => 'Complexe Scolaire EduSpace',
                'address' => 'Avenue de l’Éducation, Kinshasa',
                'phone' => '+243 000 000 000',
                'email' => 'contact@eduspace.local',
                'principal_name' => 'La Direction',
                'settings' => ['currency' => 'USD', 'pass_mark' => 10],
            ],
        );

        $year = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026-2027'],
            [
                'start_date' => '2026-09-01',
                'end_date' => '2027-06-30',
                'is_current' => true,
            ],
        );

        $terms = [
            ['name' => 'Trimestre 1', 'sequence' => 1, 'start_date' => '2026-09-01', 'end_date' => '2026-12-20', 'is_current' => false],
            ['name' => 'Trimestre 2', 'sequence' => 2, 'start_date' => '2027-01-05', 'end_date' => '2027-03-31', 'is_current' => true],
            ['name' => 'Trimestre 3', 'sequence' => 3, 'start_date' => '2027-04-01', 'end_date' => '2027-06-30', 'is_current' => false],
        ];

        foreach ($terms as $term) {
            Term::updateOrCreate(
                ['academic_year_id' => $year->id, 'sequence' => $term['sequence']],
                $term,
            );
        }
    }
}
