<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\PreparationSheet;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Services\AcademicResults;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrefectController extends Controller
{
    public function __construct(private readonly AcademicResults $results) {}

    public function dashboard(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $term = $year?->currentTerm();

        $stats = [
            'teachers' => Teacher::where('school_id', $school?->id)->count(),
            'classes' => $year ? SchoolClass::where('academic_year_id', $year->id)->count() : 0,
            'schedules' => $year ? Schedule::where('academic_year_id', $year->id)->count() : 0,
            'pending_preparations' => PreparationSheet::whereHas('academicYear', fn ($query) => $query->where('school_id', $school?->id))->where('status', 'submitted')->count(),
            'attendance' => $this->results->attendanceStats(),
        ];

        $classes = $year
            ? SchoolClass::where('academic_year_id', $year->id)->withCount('enrollments')->orderBy('name')->get()
            : collect();

        $classStats = $term ? $classes->map(fn (SchoolClass $class) => [
            'class' => $class,
            'average' => $this->classAverage($class, $term),
            'success_rate' => $this->results->successRate($class, $term),
            'attendance' => $this->results->attendanceStats($class),
        ]) : collect();

        return view('prefect.dashboard', compact('year', 'term', 'stats', 'classStats'));
    }

    public function schedules(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $classes = $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect();
        $teachers = Teacher::where('school_id', $school?->id)->orderBy('last_name')->get();
        $selectedClass = $request->filled('class') ? $classes->firstWhere('id', $request->integer('class')) : null;
        $selectedTeacher = $request->filled('teacher') ? $teachers->firstWhere('id', $request->integer('teacher')) : null;

        $schedules = $year
            ? Schedule::where('academic_year_id', $year->id)
                ->with(['schoolClass', 'subject', 'teacher'])
                ->when($selectedClass, fn ($query) => $query->where('school_class_id', $selectedClass->id))
                ->when($selectedTeacher, fn ($query) => $query->where('teacher_id', $selectedTeacher->id))
                ->orderBy('day_of_week')
                ->orderBy('start_time')
                ->get()
                ->groupBy('day_of_week')
            : collect();

        return view('prefect.schedules', compact('schedules', 'classes', 'teachers', 'selectedClass', 'selectedTeacher', 'year'));
    }

    public function statistics(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $term = $year?->currentTerm();
        $classes = $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect();

        $classStats = $term ? $classes->map(fn (SchoolClass $class) => [
            'class' => $class,
            'average' => $this->classAverage($class, $term),
            'success_rate' => $this->results->successRate($class, $term),
            'attendance' => $this->results->attendanceStats($class),
            'students' => $class->activeStudents()->count(),
        ]) : collect();

        $absencesByClass = AttendanceRecord::where('status', 'absent')
            ->when($year, fn ($query) => $query->whereHas('schoolClass', fn ($class) => $class->where('academic_year_id', $year->id)))
            ->selectRaw('school_class_id, count(*) as total')
            ->groupBy('school_class_id')
            ->with('schoolClass')
            ->get();

        return view('prefect.statistics', compact('year', 'term', 'classStats', 'absencesByClass'));
    }

    public function reports(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $term = $year?->currentTerm();

        $preparations = PreparationSheet::whereHas('academicYear', fn ($query) => $query->where('school_id', $school?->id))
            ->with(['teacher', 'schoolClass', 'subject'])
            ->latest('date')
            ->limit(50)
            ->get();

        return view('prefect.reports', compact('year', 'term', 'preparations'));
    }

    private function classAverage(SchoolClass $class, $term): ?float
    {
        $averages = $this->results->classAverages($class, $term)->pluck('average')->filter();

        return $averages->isEmpty() ? null : round($averages->avg(), 2);
    }
}
