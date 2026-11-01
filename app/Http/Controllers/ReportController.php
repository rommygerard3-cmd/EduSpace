<?php

namespace App\Http\Controllers;

use App\Models\Convocation;
use App\Models\Incident;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AcademicResults;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function __construct(private readonly AcademicResults $results) {}

    public function index(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $term = $year?->currentTerm();

        $classes = $year ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get() : collect();

        $classStats = $term ? $classes->map(function (SchoolClass $class) use ($term) {
            $averages = $this->results->classAverages($class, $term)->pluck('average')->filter();

            return [
                'class' => $class,
                'students' => $class->activeStudents()->count(),
                'average' => $averages->isEmpty() ? null : round($averages->avg(), 2),
                'success_rate' => $this->results->successRate($class, $term),
                'attendance' => $this->results->attendanceStats($class),
            ];
        }) : collect();

        $invoiced = (float) Invoice::when($year, fn ($query) => $query->where('academic_year_id', $year->id))->where('status', '!=', 'cancelled')->sum('total');
        $paid = (float) Payment::when($year, fn ($query) => $query->whereHas('invoice', fn ($invoice) => $invoice->where('academic_year_id', $year->id)))->where('status', 'valid')->sum('amount');

        return view('admin.reports.index', [
            'year' => $year,
            'term' => $term,
            'classStats' => $classStats,
            'finance' => ['invoiced' => $invoiced, 'paid' => $paid, 'balance' => round($invoiced - $paid, 2)],
            'discipline' => [
                'incidents' => Incident::whereHas('student', fn ($query) => $query->where('school_id', $school?->id))->count(),
                'convocations' => Convocation::whereHas('student', fn ($query) => $query->where('school_id', $school?->id))->count(),
            ],
            'totals' => [
                'students' => Student::where('school_id', $school?->id)->where('status', 'active')->count(),
                'classes' => $classes->count(),
            ],
        ]);
    }

    public function csv(Request $request): StreamedResponse
    {
        $type = (string) $request->query('type', 'results');

        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();
        $term = $year?->currentTerm();

        $handle = fopen('php://temp', 'w');

        if ($type === 'attendance') {
            fputcsv($handle, ['type', 'class', 'attendance_rate']);

            $classes = $year
                ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get()
                : collect();

            foreach ($classes as $class) {
                $stats = $this->results->attendanceStats($class);

                fputcsv($handle, ['attendance', $class->name, (string) ($stats['rate'] ?? '')]);
            }
        } else {
            fputcsv($handle, ['type', 'class', 'average']);

            $classes = $year
                ? SchoolClass::where('academic_year_id', $year->id)->orderBy('name')->get()
                : collect();

            foreach ($classes as $class) {
                if ($term) {
                    $averages = $this->results->classAverages($class, $term);
                    $values = $averages->pluck('average')->filter()->values();
                    $average = $values->isNotEmpty() ? round((float) $values->avg(), 2) : null;

                    fputcsv($handle, ['results', $class->name, (string) ($average ?? '')]);
                }
            }
        }

        return response()->stream(
            function () use ($handle) {
                rewind($handle);

                while (! feof($handle)) {
                    echo fread($handle, 8192);
                }

                fclose($handle);
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="eduspace-report.csv"',
            ],
        );
    }
}
