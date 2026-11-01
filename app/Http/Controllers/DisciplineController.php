<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use App\Models\AttendanceRecord;
use App\Models\ConductNote;
use App\Models\Convocation;
use App\Models\Incident;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DisciplineController extends Controller
{
    public function dashboard(Request $request): View
    {
        $schoolId = $request->user()->school_id;
        $year = $request->user()->school?->defaultAcademicYear();

        $students = Student::where('school_id', $schoolId)->where('status', 'active');

        $stats = [
            'students' => (clone $students)->count(),
            'open_incidents' => Incident::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))->where('status', 'open')->count(),
            'convocations' => Convocation::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))->whereIn('status', ['pending', 'sent'])->count(),
            'absences' => AttendanceRecord::whereHas('schoolClass', fn ($query) => $query->where('school_id', $schoolId))
                ->when($year, fn ($query) => $query->whereHas('schoolClass', fn ($class) => $class->where('academic_year_id', $year->id)))
                ->where('status', 'absent')
                ->count(),
        ];

        $recentIncidents = Incident::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))
            ->with('student')
            ->latest('occurred_at')
            ->limit(8)
            ->get();

        $recentConvocations = Convocation::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))
            ->with('student')
            ->latest('meeting_at')
            ->limit(8)
            ->get();

        return view('discipline.dashboard', compact('stats', 'recentIncidents', 'recentConvocations'));
    }

    public function students(Request $request): View
    {
        $schoolId = $request->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $selectedClass = $request->filled('class') ? $classes->firstWhere('id', $request->integer('class')) : null;

        $students = Student::where('school_id', $schoolId)
            ->where('status', 'active')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('matricule', 'like', $term));
            })
            ->when($selectedClass, fn ($query) => $query->whereHas('enrollments', fn ($enrollment) => $enrollment->where('school_class_id', $selectedClass->id)))
            ->withCount(['incidents', 'convocations'])
            ->orderBy('last_name')
            ->paginate(25)
            ->withQueryString();

        return view('discipline.students', compact('students', 'classes', 'selectedClass'));
    }

    public function attendance(Request $request): View
    {
        $schoolId = $request->user()->school_id;
        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name')->get();
        $selectedClass = $request->filled('class') ? $classes->firstWhere('id', $request->integer('class')) : null;
        $date = $request->input('date');

        $records = AttendanceRecord::whereHas('schoolClass', fn ($query) => $query->where('school_id', $schoolId))
            ->with(['student', 'schoolClass', 'subject'])
            ->whereIn('status', ['absent', 'late', 'excused'])
            ->when($selectedClass, fn ($query) => $query->where('school_class_id', $selectedClass->id))
            ->when($date, fn ($query) => $query->whereDate('date', $date))
            ->latest('date')
            ->paginate(40)
            ->withQueryString();

        return view('discipline.attendance', compact('records', 'classes', 'selectedClass', 'date'));
    }

    public function notifications(Request $request): View
    {
        $schoolId = $request->user()->school_id;

        $convocations = Convocation::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))
            ->with('student')
            ->latest()
            ->limit(20)
            ->get();

        $incidents = Incident::whereHas('student', fn ($query) => $query->where('school_id', $schoolId))
            ->with('student')
            ->latest()
            ->limit(20)
            ->get();

        $notifications = AppNotification::whereHas('user.student', fn ($query) => $query->where('school_id', $schoolId))
            ->whereIn('type', ['convocation', 'incident', 'discipline'])
            ->with('user')
            ->latest()
            ->limit(30)
            ->get();

        return view('discipline.notifications', compact('convocations', 'incidents', 'notifications'));
    }
}
