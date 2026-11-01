<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TermController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $years = AcademicYear::query()
            ->where('school_id', $request->user()->school_id)
            ->with(['terms' => fn ($query) => $query->orderBy('sequence')])
            ->orderByDesc('start_date')
            ->get();

        return view('admin.terms.index', compact('years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', Rule::exists('academic_years', 'id')->where('school_id', $request->user()->school_id)],
            'name' => ['required', 'string', 'max:50', Rule::unique('terms', 'name')->where('academic_year_id', $request->integer('academic_year_id'))],
            'sequence' => ['required', 'integer', 'min:1', 'max:10', Rule::unique('terms', 'sequence')->where('academic_year_id', $request->integer('academic_year_id'))],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $term = DB::transaction(function () use ($validated, $request) {
            if ($request->boolean('is_current')) {
                $this->clearCurrent($request);
            }

            $term = Term::create([
                'academic_year_id' => $validated['academic_year_id'],
                'name' => $validated['name'],
                'sequence' => $validated['sequence'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $request->boolean('is_current'),
            ]);

            $this->auditLogger->log('term.created', $term);

            return $term;
        });

        return back()->with('success', "Trimestre {$term->name} créé.");
    }

    public function update(Request $request, Term $term): RedirectResponse
    {
        $year = $term->academicYear;
        abort_unless($year && $year->school_id === $request->user()->school_id, 404);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50', Rule::unique('terms', 'name')->where('academic_year_id', $term->academic_year_id)->ignore($term->id)],
            'sequence' => ['required', 'integer', 'min:1', 'max:10', Rule::unique('terms', 'sequence')->where('academic_year_id', $term->academic_year_id)->ignore($term->id)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $old = $term->toArray();

        DB::transaction(function () use ($term, $validated, $request) {
            if ($request->boolean('is_current')) {
                $this->clearCurrent($request);
            }

            $term->update([
                'name' => $validated['name'],
                'sequence' => $validated['sequence'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $request->boolean('is_current'),
            ]);

            $this->auditLogger->log('term.updated', $term, $old, $term->fresh()->toArray());
        });

        return back()->with('success', 'Trimestre mis à jour.');
    }

    public function destroy(Request $request, Term $term): RedirectResponse
    {
        $year = $term->academicYear;
        abort_unless($year && $year->school_id === $request->user()->school_id, 404);

        if ($term->assessments()->exists() || $term->conductNotes()->exists() || $term->preparationSheets()->exists()) {
            return back()->withErrors(['term' => 'Ce trimestre contient des évaluations ou des données pédagogiques et ne peut pas être supprimé.']);
        }

        $this->auditLogger->log('term.deleted', $term);
        $term->delete();

        return back()->with('success', 'Trimestre supprimé.');
    }

    private function clearCurrent(Request $request): void
    {
        $yearIds = AcademicYear::where('school_id', $request->user()->school_id)->pluck('id');
        Term::whereIn('academic_year_id', $yearIds)->update(['is_current' => false]);
    }
}
