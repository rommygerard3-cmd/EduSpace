<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\ClassSubject;
use App\Models\PreparationSheet;
use App\Models\Schedule;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeacherPortalController extends Controller
{
    public function dashboard(Request $request): View
    {
        $teacher = $this->teacher($request);
        $assignments = ClassSubject::where('teacher_id', $teacher->id)->with(['schoolClass', 'subject'])->get();

        return view('teacher.dashboard', [
            'teacher' => $teacher->load('user'),
            'assignments' => $assignments,
            'studentsCount' => $assignments->sum(fn (ClassSubject $assignment) => $assignment->schoolClass->activeStudents()->count()),
            'pendingPreparations' => PreparationSheet::where('teacher_id', $teacher->id)->whereIn('status', ['draft', 'revision'])->count(),
            'recentAssessments' => Assessment::where('teacher_id', $teacher->id)->with(['schoolClass', 'subject'])->latest('date')->limit(6)->get(),
            'schedules' => Schedule::where('teacher_id', $teacher->id)->with(['schoolClass', 'subject'])->orderBy('day_of_week')->orderBy('start_time')->limit(8)->get(),
        ]);
    }

    public function classes(Request $request): View
    {
        $teacher = $this->teacher($request);

        $assignments = ClassSubject::where('teacher_id', $teacher->id)
            ->with(['schoolClass.level', 'schoolClass.section', 'subject'])
            ->get()
            ->groupBy('school_class_id');

        return view('teacher.classes.index', compact('teacher', 'assignments'));
    }

    public function classStudents(Request $request, SchoolClass $schoolClass): View
    {
        $teacher = $this->teacher($request);

        $assigned = ClassSubject::where('teacher_id', $teacher->id)->where('school_class_id', $schoolClass->id)->exists();
        abort_unless($assigned, 403, 'Vous n’enseignez pas dans cette classe.');

        $schoolClass->load(['level', 'section', 'enrollments' => fn ($query) => $query->with('student.user')->orderBy('roll_number')]);

        return view('teacher.classes.show', compact('teacher', 'schoolClass'));
    }

    public function schedule(Request $request): View
    {
        $teacher = $this->teacher($request);

        $schedules = Schedule::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'subject'])
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get()
            ->groupBy('day_of_week');

        return view('teacher.schedule', compact('teacher', 'schedules'));
    }

    public function profile(Request $request): View
    {
        $teacher = $this->teacher($request)->load(['user', 'classSubjects.schoolClass', 'classSubjects.subject']);

        return view('teacher.profile', compact('teacher'));
    }

    private function teacher(Request $request)
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403, 'Aucun profil enseignant associé à ce compte.');

        return $teacher;
    }
}
