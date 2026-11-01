<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\AuditLogger;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function __construct(private readonly AuditLogger $auditLogger) {}

    public function index(Request $request): View
    {
        $years = AcademicYear::query()
            ->where('school_id', $request->user()->school_id)
            ->with(['terms', 'schoolClasses'])
            ->withCount('enrollments')
            ->orderByDesc('start_date')
            ->get();

        return view('admin.academic-years.index', compact('years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', Rule::unique('academic_years', 'name')->where('school_id', $request->user()->school_id)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_current' => ['nullable', 'boolean'],
        ]);

        $year = DB::transaction(function () use ($validated, $request) {
            $makeCurrent = $request->boolean('is_current') || ! AcademicYear::where('school_id', $request->user()->school_id)->where('is_current', true)->exists();

            if ($makeCurrent) {
                AcademicYear::where('school_id', $request->user()->school_id)->update(['is_current' => false]);
            }

            $year = AcademicYear::create([
                'school_id' => $request->user()->school_id,
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
                'is_current' => $makeCurrent,
            ]);

            $this->createDefaultTerms($year, $makeCurrent);
            $this->auditLogger->log('academic_year.created', $year);

            return $year;
        });

        return redirect()->route('admin.academic-years.index')->with('success', "Année {$year->name} créée avec trois trimestres.");
    }

    public function setCurrent(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        abort_unless($academicYear->school_id === $request->user()->school_id, 404);
        abort_if($academicYear->isClosed(), 422, 'Une année clôturée ne peut pas redevenir active.');

        DB::transaction(function () use ($academicYear, $request) {
            AcademicYear::where('school_id', $request->user()->school_id)->update(['is_current' => false]);
            $academicYear->update(['is_current' => true]);

            Term::whereIn('academic_year_id', AcademicYear::where('school_id', $request->user()->school_id)->pluck('id'))->update(['is_current' => false]);
            $firstTerm = $academicYear->terms()->orderBy('sequence')->first();
            $firstTerm?->update(['is_current' => true]);

            $this->auditLogger->log('academic_year.current', $academicYear);
        });

        return back()->with('success', "L’année {$academicYear->name} est maintenant active.");
    }

    public function destroy(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        abort_unless($academicYear->school_id === $request->user()->school_id, 404);

        if ($academicYear->schoolClasses()->exists() || $academicYear->enrollments()->exists()) {
            return back()->withErrors(['academic_year' => 'Cette année contient des classes ou des inscriptions et ne peut pas être supprimée.']);
        }

        DB::transaction(function () use ($academicYear) {
            $academicYear->terms()->delete();
            $this->auditLogger->log('academic_year.deleted', $academicYear);
            $academicYear->delete();
        });

        return back()->with('success', 'Année supprimée.');
    }

    private function createDefaultTerms(AcademicYear $year, bool $makeCurrent): void
    {
        $start = Carbon::parse($year->start_date);
        $end = Carbon::parse($year->end_date);
        $totalDays = max(3, $start->diffInDays($end));
        $segment = (int) floor($totalDays / 3);

        for ($index = 1; $index <= 3; $index++) {
            $termStart = $start->copy()->addDays(($index - 1) * $segment);
            $termEnd = $index === 3 ? $end->copy() : $start->copy()->addDays($index * $segment - 1);

            $year->terms()->create([
                'name' => $index === 1 ? '1er trimestre' : "{$index}e trimestre",
                'sequence' => $index,
                'start_date' => $termStart,
                'end_date' => $termEnd,
                'is_current' => $makeCurrent && $index === 1,
            ]);
        }
    }
}
