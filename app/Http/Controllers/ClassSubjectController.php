<?php

namespace App\Http\Controllers;

use App\Models\ClassSubject;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClassSubjectController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function store(Request $request, SchoolClass $class): RedirectResponse
    {
        abort_unless($class->school_id === $request->user()->school_id, 404);

        $validated = $request->validate([
            'subject_id' => ['required', Rule::exists('subjects', 'id')],
            'teacher_id' => ['required', Rule::exists('teachers', 'id')->where('school_id', $request->user()->school_id)],
            'coefficient' => ['required', 'numeric', 'min:0.5', 'max:20'],
        ]);

        ClassSubject::updateOrCreate(
            [
                'school_class_id' => $class->id,
                'subject_id' => $validated['subject_id'],
                'academic_year_id' => $class->academic_year_id,
            ],
            [
                'teacher_id' => $validated['teacher_id'],
                'coefficient' => $validated['coefficient'],
            ],
        );

        $this->auditLogger->log('class_subject.assigned', $class, [], [
            'subject_id' => $validated['subject_id'],
            'teacher_id' => $validated['teacher_id'],
        ]);

        return back()->with('success', 'MatiÃ¨re et enseignant affectÃ©s Ã  la classe.');
    }

    public function destroy(Request $request, ClassSubject $classSubject): RedirectResponse
    {
        $class = $classSubject->schoolClass;
        abort_unless($class && $class->school_id === $request->user()->school_id, 404);

        if ($classSubject->schedules()->exists() || $classSubject->preparationSheets()->exists()) {
            return back()->withErrors(['class_subject' => 'Cette affectation est utilisÃ©e par un horaire ou une prÃ©paration.']);
        }

        $this->auditLogger->log('class_subject.removed', $classSubject);
        $classSubject->delete();

        return back()->with('success', 'Affectation supprimÃ©e.');
    }
}

