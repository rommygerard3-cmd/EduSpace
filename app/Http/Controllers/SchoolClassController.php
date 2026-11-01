<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Level;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $years = AcademicYear::where('school_id', $request->user()->school_id)->orderByDesc('start_date')->get();
        $year = $request->filled('year')
            ? $years->firstWhere('id', $request->integer('year'))
            : $years->firstWhere('is_current', true) ?? $years->first();

        $classes = $year
            ? SchoolClass::where('academic_year_id', $year->id)
                ->with(['level', 'section'])
                ->withCount(['enrollments', 'classSubjects'])
                ->orderBy('name')
                ->get()
            : collect();

        return view('admin.classes.index', compact('years', 'year', 'classes'));
    }

    public function create(Request $request): View
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune annÃ©e scolaire active.');

        return view('admin.classes.create', [
            'year' => $year,
            'levels' => Level::orderBy('position')->get(),
            'sections' => Section::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune annÃ©e scolaire active.');

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('school_classes', 'name')->where('academic_year_id', $year->id)],
            'level_id' => ['required', 'exists:levels,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'room' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $class = SchoolClass::create([
            'school_id' => $request->user()->school_id,
            'academic_year_id' => $year->id,
            'level_id' => $validated['level_id'],
            'section_id' => $validated['section_id'] ?? null,
            'name' => $validated['name'],
            'room' => $validated['room'] ?? null,
            'capacity' => $validated['capacity'],
        ]);

        $this->auditLogger->log('school_class.created', $class);

        return redirect()->route('admin.classes.show', $class)->with('success', 'Classe crÃ©Ã©e.');
    }

    public function show(Request $request, SchoolClass $class): View
    {
        abort_unless($class->school_id === $request->user()->school_id, 404);

        $class->load([
            'level', 'section', 'academicYear',
            'enrollments' => fn ($query) => $query->with('student.user')->orderBy('roll_number'),
            'classSubjects.subject', 'classSubjects.teacher',
            'schedules.subject', 'schedules.teacher',
        ]);

        return view('admin.classes.show', [
            'schoolClass' => $class,
            'subjects' => Subject::orderBy('name')->get(),
            'teachers' => Teacher::where('school_id', $request->user()->school_id)->orderBy('last_name')->get(),
        ]);
    }

    public function edit(Request $request, SchoolClass $class): View
    {
        abort_unless($class->school_id === $request->user()->school_id, 404);

        return view('admin.classes.edit', [
            'schoolClass' => $class->load(['level', 'section', 'academicYear']),
            'levels' => Level::orderBy('position')->get(),
            'sections' => Section::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SchoolClass $class): RedirectResponse
    {
        abort_unless($class->school_id === $request->user()->school_id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('school_classes', 'name')->where('academic_year_id', $class->academic_year_id)->ignore($class->id)],
            'level_id' => ['required', 'exists:levels,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'room' => ['nullable', 'string', 'max:50'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
        ]);

        $old = $class->toArray();
        $class->update($validated);
        $this->auditLogger->log('school_class.updated', $class, $old, $class->fresh()->toArray());

        return redirect()->route('admin.classes.show', $class)->with('success', 'Classe mise Ã  jour.');
    }

    public function destroy(Request $request, SchoolClass $class): RedirectResponse
    {
        abort_unless($class->school_id === $request->user()->school_id, 404);

        if ($class->enrollments()->exists() || $class->assessments()->exists()) {
            return back()->withErrors(['class' => 'Cette classe contient des Ã©lÃ¨ves ou des Ã©valuations et ne peut pas Ãªtre supprimÃ©e.']);
        }

        $this->auditLogger->log('school_class.deleted', $class);
        $class->delete();

        return redirect()->route('admin.classes.index')->with('success', 'Classe supprimÃ©e.');
    }
}

