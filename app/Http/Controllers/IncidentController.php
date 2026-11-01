<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\Student;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class IncidentController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $incidents = Incident::query()
            ->whereHas('student', fn ($query) => $query->where('school_id', $request->user()->school_id))
            ->with(['student', 'reporter'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->whereHas('student', fn ($student) => $student->where('first_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('matricule', 'like', $term));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('occurred_at')
            ->paginate(20)
            ->withQueryString();

        return view('discipline.incidents.index', compact('incidents'));
    }

    public function create(Request $request): View
    {
        return view('discipline.incidents.create', [
            'students' => $this->students($request),
            'incident' => new Incident,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateIncident($request);

        $incident = Incident::create([
            ...$validated,
            'reported_by' => $request->user()->id,
        ]);

        $this->auditLogger->log('incident.created', $incident);

        return redirect()->route('discipline.incidents.index')->with('success', 'Incident enregistré.');
    }

    public function edit(Request $request, Incident $incident): View
    {
        abort_unless($incident->student?->school_id === $request->user()->school_id, 404);

        return view('discipline.incidents.edit', [
            'students' => $this->students($request),
            'incident' => $incident,
        ]);
    }

    public function update(Request $request, Incident $incident): RedirectResponse
    {
        abort_unless($incident->student?->school_id === $request->user()->school_id, 404);

        $incident->update($this->validateIncident($request));
        $this->auditLogger->log('incident.updated', $incident);

        return redirect()->route('discipline.incidents.index')->with('success', 'Incident mis à jour.');
    }

    public function destroy(Request $request, Incident $incident): RedirectResponse
    {
        abort_unless($incident->student?->school_id === $request->user()->school_id, 404);

        $incident->update(['status' => 'closed']);
        $this->auditLogger->log('incident.closed', $incident);

        return redirect()->route('discipline.incidents.index')->with('success', 'Incident clôturé. L’historique est conservé.');
    }

    private function validateIncident(Request $request): array
    {
        return $request->validate([
            'student_id' => ['required', Rule::exists('students', 'id')->where('school_id', $request->user()->school_id)],
            'occurred_at' => ['required', 'date', 'before_or_equal:now'],
            'type' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:5000'],
            'sanction' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['open', 'resolved', 'closed'])],
        ]);
    }

    private function students(Request $request)
    {
        return Student::where('school_id', $request->user()->school_id)->where('status', 'active')->orderBy('last_name')->get();
    }
}
