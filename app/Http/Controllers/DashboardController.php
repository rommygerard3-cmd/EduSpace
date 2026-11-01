<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PreparationSheet;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Services\AcademicResults;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(private readonly AcademicResults $results) {}

    public function index(Request $request): RedirectResponse|View
    {
        $user = $request->user();

        return match (true) {
            $user->hasRole(Role::ELEVE) => redirect()->route('student.dashboard'),
            $user->hasRole(Role::ENSEIGNANT) => redirect()->route('teacher.dashboard'),
            $user->hasRole(Role::PREFET) => redirect()->route('prefect.dashboard'),
            $user->hasRole(Role::DIRECTEUR_DISCIPLINE) => redirect()->route('discipline.dashboard'),
            $user->hasRole(Role::COMPTABILITE) => redirect()->route('finance.dashboard'),
            default => $this->directionDashboard($request),
        };
    }

    public function directionDashboard(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        $stats = [
            'students' => Student::where('school_id', $school?->id)->where('status', 'active')->count(),
            'teachers' => Teacher::where('school_id', $school?->id)->count(),
            'classes' => $year ? SchoolClass::where('academic_year_id', $year->id)->count() : 0,
            'attendance' => $this->results->attendanceStats(),
        ];

        $finance = [
            'invoiced' => (float) Invoice::when($year, fn ($query) => $query->where('academic_year_id', $year->id))->where('status', '!=', 'cancelled')->sum('total'),
            'paid' => (float) Payment::when($year, fn ($query) => $query->whereHas('invoice', fn ($invoice) => $invoice->where('academic_year_id', $year->id)))->where('status', 'valid')->sum('amount'),
        ];
        $finance['balance'] = round($finance['invoiced'] - $finance['paid'], 2);

        $classes = $year
            ? SchoolClass::where('academic_year_id', $year->id)->withCount('enrollments')->orderBy('name')->get()
            : collect();

        return view('dashboard.direction', [
            'year' => $year,
            'stats' => $stats,
            'finance' => $finance,
            'classes' => $classes,
            'pendingPreparations' => PreparationSheet::where('status', 'submitted')->count(),
            'recentAudit' => AuditLog::with('user')->latest('created_at')->limit(8)->get(),
            'absences' => AttendanceRecord::where('status', 'absent')->when($year, fn ($query) => $query->whereHas('schoolClass', fn ($class) => $class->where('academic_year_id', $year->id)))->count(),
        ]);
    }

    public function financeDashboard(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        $invoices = Invoice::when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->where('status', '!=', 'cancelled')
            ->get();

        $paid = Payment::when($year, fn ($query) => $query->whereHas('invoice', fn ($invoice) => $invoice->where('academic_year_id', $year->id)))
            ->where('status', 'valid')
            ->sum('amount');

        return view('dashboard.finance', [
            'year' => $year,
            'invoiced' => round((float) $invoices->sum('total'), 2),
            'paid' => round((float) $paid, 2),
            'balance' => round((float) $invoices->sum('total') - (float) $paid, 2),
            'recentPayments' => Payment::with(['student', 'invoice'])->latest('paid_at')->limit(10)->get(),
            'unpaidInvoices' => Invoice::with('student')->whereIn('status', ['issued', 'partial'])->latest()->limit(10)->get(),
        ]);
    }

    public function academicYearOptions(Request $request): array
    {
        $school = $request->user()->school;

        return AcademicYear::where('school_id', $school?->id)->orderByDesc('start_date')->pluck('name', 'id')->all();
    }
}
