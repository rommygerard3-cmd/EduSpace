<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Assessment;
use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\ConductNote;
use App\Models\Convocation;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\FeeCategory;
use App\Models\Grade;
use App\Models\Guardian;
use App\Models\Incident;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PreparationSheet;
use App\Models\Role;
use App\Models\Schedule;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use App\Services\InvoiceService;
use App\Services\MatriculeGenerator;
use App\Services\NotificationService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (! env('EDUSPACE_DEMO', false)) {
            return;
        }

        $school = School::where('code', 'EDUSPACE')->first();
        $year = $school?->defaultAcademicYear();
        $class = $year ? SchoolClass::where('academic_year_id', $year->id)->first() : null;

        if (! $school || ! $year || ! $class) {
            return;
        }

        $password = env('EDUSPACE_DEMO_PASSWORD') ?: Str::password(14, symbols: false);
        $generator = app(MatriculeGenerator::class);

        $teacherUser = User::firstOrCreate(
            ['matricule' => 'ENS-2026-0001'],
            [
                'school_id' => $school->id,
                'name' => 'Jean Mukendi',
                'email' => 'jean.mukendi@eduspace.local',
                'password' => Hash::make($password),
                'is_active' => true,
                'activated_at' => now(),
                'must_change_password' => false,
            ],
        );
        $teacherUser->assignRole(Role::where('name', Role::ENSEIGNANT)->firstOrFail());

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $teacherUser->id],
            [
                'school_id' => $school->id,
                'employee_number' => 'EMP-2026-0001',
                'first_name' => 'Jean',
                'last_name' => 'Mukendi',
                'gender' => 'M',
                'phone' => '+243 810 000 001',
                'email' => 'jean.mukendi@eduspace.local',
            ],
        );

        $studentUser = User::firstOrCreate(
            ['matricule' => 'MAT-2026-0001'],
            [
                'school_id' => $school->id,
                'name' => 'Aris Kalu',
                'email' => 'aris.kalu@eduspace.local',
                'password' => Hash::make($password),
                'is_active' => true,
                'activated_at' => now(),
                'must_change_password' => false,
            ],
        );
        $studentUser->assignRole(Role::where('name', Role::ELEVE)->firstOrFail());

        $student = Student::firstOrCreate(
            ['user_id' => $studentUser->id],
            [
                'school_id' => $school->id,
                'matricule' => $studentUser->matricule,
                'first_name' => 'Aris',
                'last_name' => 'Kalu',
                'gender' => 'M',
                'birth_date' => '2009-05-14',
                'birth_place' => 'Kinshasa',
                'status' => 'active',
            ],
        );

        $guardian = Guardian::firstOrCreate(
            ['phone' => '+243 820 000 001'],
            [
                'school_id' => $school->id,
                'first_name' => 'Marie',
                'last_name' => 'Kalu',
                'relationship' => 'Mère',
                'email' => 'marie.kalu@example.local',
                'address' => 'Kinshasa',
            ],
        );
        $student->guardians()->syncWithoutDetaching([$guardian->id]);

        Enrollment::firstOrCreate(
            ['student_id' => $student->id, 'academic_year_id' => $year->id],
            ['school_class_id' => $class->id, 'roll_number' => 1, 'status' => 'enrolled', 'enrolled_at' => $year->start_date],
        );

        $subjectCodes = ['MATH', 'FRAN', 'ANGL', 'PHYS', 'ELEC', 'INFO'];
        $subjects = Subject::whereIn('code', $subjectCodes)->get()->keyBy('code');

        foreach ($subjectCodes as $index => $code) {
            $subject = $subjects[$code] ?? null;

            if (! $subject) {
                continue;
            }

            ClassSubject::firstOrCreate(
                ['school_class_id' => $class->id, 'subject_id' => $subject->id, 'academic_year_id' => $year->id],
                ['teacher_id' => $teacher->id, 'coefficient' => $subject->coefficient],
            );

            $startHour = 8 + intdiv($index, 5) * 2;

            Schedule::firstOrCreate(
                ['school_class_id' => $class->id, 'day_of_week' => ($index % 5) + 1, 'start_time' => sprintf('%02d:00:00', $startHour)],
                [
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'academic_year_id' => $year->id,
                    'end_time' => sprintf('%02d:40:00', $startHour + 1),
                    'room' => $class->room,
                ],
            );
        }

        $term = Term::where('academic_year_id', $year->id)->where('is_current', true)->first()
            ?? Term::where('academic_year_id', $year->id)->orderBy('sequence')->first();

        if (! $term) {
            return;
        }

        foreach (['MATH' => 'Devoir 1', 'FRAN' => 'Interrogation 1'] as $code => $title) {
            $subject = $subjects[$code] ?? null;

            if (! $subject) {
                continue;
            }

            $assessment = Assessment::firstOrCreate(
                [
                    'school_class_id' => $class->id,
                    'subject_id' => $subject->id,
                    'term_id' => $term->id,
                    'title' => $title,
                ],
                [
                    'teacher_id' => $teacher->id,
                    'created_by' => $teacherUser->id,
                    'type' => $code === 'MATH' ? 'devoir' : 'interrogation',
                    'date' => now()->subDays(7)->toDateString(),
                    'max_score' => 20,
                    'coefficient' => 1,
                    'status' => 'published',
                    'published_at' => now()->subDays(3),
                ],
            );

            Grade::updateOrCreate(
                ['assessment_id' => $assessment->id, 'student_id' => $student->id],
                [
                    'score' => $code === 'MATH' ? 16 : 14,
                    'status' => 'published',
                    'entered_by' => $teacherUser->id,
                    'published_at' => now()->subDays(3),
                ],
            );
        }

        AttendanceRecord::firstOrCreate(
            ['student_id' => $student->id, 'school_class_id' => $class->id, 'date' => now()->toDateString(), 'session' => '1'],
            [
                'subject_id' => $subjects['MATH']?->id,
                'teacher_id' => $teacher->id,
                'status' => 'present',
                'recorded_by' => $teacherUser->id,
            ],
        );

        $category = FeeCategory::firstOrCreate(
            ['academic_year_id' => $year->id, 'name' => 'Minerval'],
            [
                'school_id' => $school->id,
                'description' => 'Frais de scolarité annuels',
                'amount' => 250,
                'due_date' => $year->start_date,
                'is_mandatory' => true,
            ],
        );

        Fee::firstOrCreate(
            ['student_id' => $student->id, 'fee_category_id' => $category->id, 'academic_year_id' => $year->id],
            ['amount' => 250, 'due_date' => $year->start_date, 'status' => 'partial'],
        );

        $invoice = app(InvoiceService::class)->ensureForStudent($student, $year);

        if ($invoice && $invoice->payments()->count() === 0) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $student->id,
                'amount' => 100,
                'method' => 'cash',
                'paid_at' => now()->subDays(2),
                'recorded_by' => $teacherUser->id,
                'status' => 'valid',
            ]);
            $invoice->refreshStatus();
        }

        ConductNote::firstOrCreate(
            ['student_id' => $student->id, 'term_id' => $term->id],
            ['score' => 18, 'comment' => 'Élève discipliné et appliqué.', 'recorded_by' => $teacherUser->id],
        );

        PreparationSheet::firstOrCreate(
            [
                'teacher_id' => $teacher->id,
                'school_class_id' => $class->id,
                'subject_id' => $subjects['MATH']?->id,
                'date' => now()->toDateString(),
            ],
            [
                'academic_year_id' => $year->id,
                'term_id' => $term->id,
                'topic' => 'Les équations du premier degré',
                'objectives' => 'Résoudre une équation du premier degré à une inconnue.',
                'prerequisites' => 'Opérations sur les nombres relatifs.',
                'materials' => 'Tableau, craie, fiches d’exercices.',
                'steps' => 'Rappel, démonstration, exercices guidés, synthèse.',
                'evaluation_methods' => 'Exercices individuels et correction collective.',
                'status' => 'submitted',
                'submitted_at' => now()->subDay(),
            ],
        );

        Convocation::firstOrCreate(
            ['student_id' => $student->id, 'reason' => 'Suivi de la ponctualité'],
            [
                'created_by' => $teacherUser->id,
                'meeting_at' => now()->addDays(3)->setTime(10, 0),
                'recipient' => $guardian->fullName(),
                'status' => 'sent',
            ],
        );

        Incident::firstOrCreate(
            ['student_id' => $student->id, 'occurred_at' => now()->subDays(5)->setTime(9, 0)],
            [
                'reported_by' => $teacherUser->id,
                'type' => 'Retard répété',
                'description' => 'Trois retards constatés au cours de la semaine.',
                'sanction' => 'Avertissement écrit',
                'status' => 'open',
            ],
        );

        app(NotificationService::class)->toStudent(
            $student,
            'welcome',
            'Bienvenue sur EduSpace',
            'Votre espace personnel est prêt. Consultez vos notes, votre horaire et vos paiements.',
        );

        $credentials = "Comptes de démonstration EduSpace\n"
            ."Enseignant : ENS-2026-0001 / {$password}\n"
            ."Élève : MAT-2026-0001 / {$password}\n";
        Storage::disk('local')->put('eduspace-demo.txt', $credentials);
    }
}
