<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class ReferenceDataSeeder extends Seeder
{
    public function run(): void
    {
        $levels = [
            ['name' => '7ème année', 'code' => '7E', 'position' => 1],
            ['name' => '8ème année', 'code' => '8E', 'position' => 2],
            ['name' => '1ère année', 'code' => '1E', 'position' => 3],
            ['name' => '2ème année', 'code' => '2E', 'position' => 4],
            ['name' => '3ème année', 'code' => '3E', 'position' => 5],
            ['name' => '4ème année', 'code' => '4E', 'position' => 6],
        ];

        foreach ($levels as $level) {
            Level::firstOrCreate(['code' => $level['code']], $level);
        }

        $sections = [
            ['name' => 'Scientifique', 'code' => 'SCI'],
            ['name' => 'Commerciale', 'code' => 'COM'],
            ['name' => 'Technique', 'code' => 'TEC'],
            ['name' => 'Littéraire', 'code' => 'LIT'],
        ];

        foreach ($sections as $section) {
            Section::firstOrCreate(['code' => $section['code']], $section);
        }

        $subjects = [
            ['code' => 'MATH', 'name' => 'Mathématiques', 'coefficient' => 4],
            ['code' => 'FRAN', 'name' => 'Français', 'coefficient' => 4],
            ['code' => 'ANGL', 'name' => 'Anglais', 'coefficient' => 3],
            ['code' => 'PHYS', 'name' => 'Physique', 'coefficient' => 3],
            ['code' => 'CHIM', 'name' => 'Chimie', 'coefficient' => 2],
            ['code' => 'BIOL', 'name' => 'Biologie', 'coefficient' => 2],
            ['code' => 'HIST', 'name' => 'Histoire', 'coefficient' => 2],
            ['code' => 'GEOG', 'name' => 'Géographie', 'coefficient' => 2],
            ['code' => 'CIVI', 'name' => 'Éducation civique', 'coefficient' => 1],
            ['code' => 'INFO', 'name' => 'Informatique', 'coefficient' => 2],
            ['code' => 'ELEC', 'name' => 'Électronique', 'coefficient' => 3],
            ['code' => 'COMPTA', 'name' => 'Comptabilité', 'coefficient' => 3],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(['code' => $subject['code']], $subject);
        }

        $school = School::where('code', 'EDUSPACE')->first();

        if (! $school) {
            return;
        }

        $year = AcademicYear::where('school_id', $school->id)->where('is_current', true)->first();

        if (! $year) {
            return;
        }

        $level = Level::where('code', '1E')->first();
        $section = Section::where('code', 'TEC')->first();

        if ($level && $section) {
            SchoolClass::firstOrCreate(
                ['school_id' => $school->id, 'academic_year_id' => $year->id, 'name' => '1ère Électronique'],
                ['level_id' => $level->id, 'section_id' => $section->id, 'room' => 'Salle 1', 'capacity' => 40],
            );
        }
    }
}
