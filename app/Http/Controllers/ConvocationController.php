<?php

namespace App\Http\Controllers;

use App\Models\Convocation;
use App\Models\Student;
use App\Services\AuditLogger;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ConvocationController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly NotificationService $notifications,
    ) {}

    public function index(Request $request): View
    {
        $convocations = Convocation::query()
            ->whereHas('student', fn ($query) => $query->where('school_id', $request->user()->school_id))
            ->with(['student', 'creator'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->whereHas('student', fn ($student) => $student->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('matricule', 'like', $term));
            })
            ->latest('meeting_at')
            ->paginate(20)
            ->withQueryString();

        return view('discipline.convocations.index', compact('convocations'));
    }

    public function create(Request $request): View
    {
        return view('discipline.convocations.create', [
            'students' => $this->students($request),
            'convocation' => new Convocation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateConvocation($request);
        $student = Student::findOrFail($validated['student_id']);

        $convocation = Convocation::create([
            ...$validated,
            'created_by' => $request->user()->id,
            'status' => 'pending',
        ]);

        if (in_array($convocation->recipient, ['student', 'both'], true)) {
            $notification = $this->notifications->toStudent(
                $student,
                'convocation',
                'Convocation disciplinaire',
                'Vous êtes convoqué(e) le '.$convocation->meeting_at->format('d/m/Y à H:i').'. Motif : '.$convocation->reason,
                ['convocation_id' => $convocation->id],
            );

            if ($notification) {
                $convocation->update(['status' => 'sent']);
            }
        }

        $this->auditLogger->log('convocation.created', $convocation);

        return redirect()->route('discipline.convocations.index')->with('success', 'Convocation enregistrée.');
    }

    public function edit(Request $request, Convocation $convocation): View
    {
        abort_unless($convocation->student?->school_id === $request->user()->school_id, 404);

        return view('discipline.convocations.edit', [
            'students' => $this->students($request),
            'convocation' => $convocation,
        ]);
    }

    public function update(Request $request, Convocation $convocation): RedirectResponse
    {
        abort_unless($convocation->student?->school_id === $request->user()->school_id, 404);

        $convocation->update($this->validateConvocation($request));
        $this->auditLogger->log('convocation.updated', $convocation);

        return redirect()->route('discipline.convocations.index')->with('success', 'Convocation mise à jour.');
    }

    public function destroy(Request $request, Convocation $convocation): RedirectResponse
    {
        abort_unless($convocation->student?->school_id === $request->user()->school_id, 404);

        $convocation->update(['status' => 'cancelled']);
        $this->auditLogger->log('convocation.cancelled', $convocation);

        return redirect()->route('discipline.convocations.index')->with('success', 'Convocation annulée.');
    }

    private function validateConvocation(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $request->user()->school_id)],
            'reason' => ['required', 'string', 'max:2000'],
            'meeting_at' => ['required', 'date'],
            'recipient' => ['required', Rule::in(['parent', 'student', 'both'])],
            'status' => ['nullable', Rule::in(['pending', 'sent', 'acknowledged', 'cancelled'])],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function students(Request $request)
    {
        return Student::where('school_id', $request->user()->school_id)->where('status', 'active')->orderBy('last_name')->get();
    }
}
