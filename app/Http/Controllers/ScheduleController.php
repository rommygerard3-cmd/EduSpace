<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\Schedule;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ScheduleController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
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

        return view('admin.schedules.index', compact('year', 'classes', 'teachers', 'selectedClass', 'selectedTeacher', 'schedules'));
    }

    public function create(Request $request): View
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        return view('admin.schedules.create', [
            'year' => $year,
            'classes' => SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => Teacher::where('school_id', $request->user()->school_id)->orderBy('last_name')->get(),
            'days' => Schedule::DAYS,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        $validated = $request->validate([
            'school_class_id' => ['required', Rule::exists('school_classes', 'id')->where('academic_year_id', $year->id)],
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', Rule::exists('teachers', 'id')->where('school_id', $request->user()->school_id)],
            'day_of_week' => ['required', 'integer', 'between:1,6'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'room' => ['nullable', 'string', 'max:50'],
        ]);

        $assignment = ClassSubject::where('school_class_id', $validated['school_class_id'])
            ->where('subject_id', $validated['subject_id'])
            ->where('teacher_id', $validated['teacher_id'])
            ->where('academic_year_id', $year->id)
            ->exists();

        if (! $assignment) {
            return back()->withErrors(['teacher_id' => 'Affectez d’abord cette matière et cet enseignant à la classe.'])->withInput();
        }

        $classConflict = Schedule::where('school_class_id', $validated['school_class_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($classConflict) {
            return back()->withErrors(['start_time' => 'Cette classe a déjà un cours sur ce créneau.'])->withInput();
        }

        $teacherConflict = Schedule::where('teacher_id', $validated['teacher_id'])
            ->where('day_of_week', $validated['day_of_week'])
            ->where('start_time', '<', $validated['end_time'])
            ->where('end_time', '>', $validated['start_time'])
            ->exists();

        if ($teacherConflict) {
            return back()->withErrors(['start_time' => 'Cet enseignant a déjà un cours sur ce créneau.'])->withInput();
        }

        $schedule = Schedule::create([
            ...$validated,
            'academic_year_id' => $year->id,
        ]);

        $this->auditLogger->log('schedule.created', $schedule);

        return redirect()->route('admin.schedules.index')->with('success', 'Cours ajouté à l’horaire.');
    }

    public function destroy(Request $request, Schedule $schedule): RedirectResponse
    {
        abort_unless($schedule->schoolClass?->school_id === $request->user()->school_id, 404);

        $this->auditLogger->log('schedule.deleted', $schedule);
        $schedule->delete();

        return back()->with('success', 'Cours retiré de l’horaire.');
    }
}
