<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClassSubject;
use App\Models\Enrollment;
use App\Models\SchoolClass;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $classes = SchoolClass::whereIn('id', ClassSubject::where('teacher_id', $teacher->id)->pluck('school_class_id'))
            ->orderBy('name')
            ->get();

        $selectedClass = $request->filled('class_id')
            ? $classes->firstWhere('id', $request->integer('class_id'))
            : $classes->first();

        $date = $request->input('date', now()->toDateString());
        $session = $request->input('session', 'morning');
        $students = collect();
        $records = collect();

        if ($selectedClass) {
            $students = Enrollment::where('school_class_id', $selectedClass->id)
                ->where('academic_year_id', $selectedClass->academic_year_id)
                ->where('status', 'enrolled')
                ->with('student')
                ->orderBy('roll_number')
                ->get();

            $records = AttendanceRecord::where('school_class_id', $selectedClass->id)
                ->where('date', $date)
                ->where('session', $session)
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance.index', [
            'classes' => $classes,
            'selectedClass' => $selectedClass,
            'date' => $date,
            'session' => $session,
            'students' => $students,
            'records' => $records,
            'statuses' => AttendanceRecord::STATUSES,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $validated = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'date' => ['required', 'date', 'before_or_equal:today'],
            'session' => ['required', Rule::in(['morning', 'afternoon'])],
            'statuses' => ['required', 'array'],
            'statuses.*' => ['required', Rule::in(array_keys(AttendanceRecord::STATUSES))],
            'observations' => ['nullable', 'array'],
            'observations.*' => ['nullable', 'string', 'max:255'],
        ]);

        $class = SchoolClass::findOrFail($validated['school_class_id']);
        $assignment = ClassSubject::where('teacher_id', $teacher->id)
            ->where('school_class_id', $class->id)
            ->first();

        abort_unless($assignment, 403, 'Vous n’enseignez pas dans cette classe.');

        $studentIds = Enrollment::where('school_class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->where('status', 'enrolled')
            ->pluck('student_id')
            ->all();

        DB::transaction(function () use ($validated, $class, $teacher, $assignment, $request, $studentIds) {
            foreach ($studentIds as $studentId) {
                $status = $validated['statuses'][$studentId] ?? 'present';

                AttendanceRecord::updateOrCreate(
                    [
                        'school_class_id' => $class->id,
                        'student_id' => $studentId,
                        'date' => $validated['date'],
                        'session' => $validated['session'],
                    ],
                    [
                        'subject_id' => $assignment->subject_id,
                        'teacher_id' => $teacher->id,
                        'status' => $status,
                        'observation' => $validated['observations'][$studentId] ?? null,
                        'recorded_by' => $request->user()->id,
                    ],
                );
            }
        });

        $this->auditLogger->log('attendance.saved', $class, [], ['date' => $validated['date'], 'session' => $validated['session']]);

        return redirect()->route('teacher.attendance.index', [
            'class_id' => $class->id,
            'date' => $validated['date'],
            'session' => $validated['session'],
        ])->with('success', 'Présences enregistrées.');
    }

    public function history(Request $request): View
    {
        $teacher = $request->user()->teacher;
        abort_unless($teacher, 403);

        $records = AttendanceRecord::where('teacher_id', $teacher->id)
            ->with(['schoolClass', 'student', 'subject'])
            ->latest('date')
            ->limit(300)
            ->get()
            ->groupBy(fn (AttendanceRecord $record) => $record->date->format('Y-m-d').'|'.$record->session.'|'.$record->school_class_id);

        return view('teacher.attendance.history', compact('records'));
    }
}
