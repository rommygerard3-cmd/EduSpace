<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Grade;
use App\Models\Invoice;
use App\Models\Schedule;
use App\Models\Term;
use App\Services\AcademicResults;
use App\Services\ReportCardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class StudentPortalController extends Controller
{
    public function __construct(
        private readonly AcademicResults $results,
        private readonly ReportCardService $reportCards,
    ) {}

    public function dashboard(Request $request): View
    {
        $student = $this->student($request);
        $year = $student->school?->defaultAcademicYear();
        $term = $year?->currentTerm();
        $class = $student->currentClass();

        $results = $term ? $this->results->forStudent($student, $term) : null;

        return view('student.dashboard', [
            'student' => $student,
            'year' => $year,
            'term' => $term,
            'class' => $class,
            'results' => $results,
            'nextSchedules' => $class
                ? Schedule::where('school_class_id', $class->id)->with(['subject', 'teacher'])->orderBy('day_of_week')->orderBy('start_time')->limit(5)->get()
                : collect(),
            'invoices' => Invoice::where('student_id', $student->id)->when($year, fn ($query) => $query->where('academic_year_id', $year->id))->with('payments')->get(),
            'recentAbsences' => AttendanceRecord::where('student_id', $student->id)->whereIn('status', ['absent', 'late'])->latest('date')->limit(5)->get(),
        ]);
    }

    public function profile(Request $request): View
    {
        $student = $this->student($request)->load(['guardians', 'currentEnrollment.schoolClass']);

        return view('student.profile', compact('student'));
    }

    public function schedule(Request $request): View
    {
        $student = $this->student($request);
        $class = $student->currentClass();

        $schedules = $class
            ? Schedule::where('school_class_id', $class->id)->with(['subject', 'teacher'])->orderBy('day_of_week')->orderBy('start_time')->get()->groupBy('day_of_week')
            : collect();

        return view('student.schedule', compact('student', 'class', 'schedules'));
    }

    public function subjects(Request $request): View
    {
        $student = $this->student($request);
        $class = $student->currentClass();

        return view('student.subjects', [
            'student' => $student,
            'class' => $class,
            'classSubjects' => $class?->classSubjects()->with(['subject', 'teacher'])->get() ?? collect(),
        ]);
    }

    public function grades(Request $request): View
    {
        $student = $this->student($request);
        $year = $student->school?->defaultAcademicYear();
        $terms = $year?->terms()->orderBy('sequence')->get() ?? collect();

        $selectedTerm = $request->filled('term')
            ? $terms->firstWhere('id', $request->integer('term'))
            : $terms->firstWhere('is_current', true) ?? $terms->first();

        $data = $selectedTerm ? $this->reportCards->data($student, $selectedTerm) : null;

        return view('student.grades', compact('student', 'terms', 'selectedTerm', 'data'));
    }

    public function reportCards(Request $request): View
    {
        $student = $this->student($request);
        $year = $student->school?->defaultAcademicYear();
        $terms = $year?->terms()->orderBy('sequence')->get() ?? collect();

        $reports = $terms->map(fn (Term $term) => $this->reportCards->data($student, $term));

        return view('student.report-cards.index', compact('student', 'year', 'reports'));
    }

    public function reportCard(Request $request, Term $term): View
    {
        $student = $this->student($request);
        $this->authorizeTerm($student, $term);

        return view('student.report-cards.show', [
            'data' => $this->reportCards->data($student, $term),
        ]);
    }

    public function reportCardPdf(Request $request, Term $term): Response
    {
        $student = $this->student($request);
        $this->authorizeTerm($student, $term);

        $data = $this->reportCards->data($student, $term);

        $pdf = Pdf::loadView('pdf.report-card', $data)->setPaper('a4');

        return $pdf->download('bulletin-'.$student->matricule.'-'.$term->sequence.'.pdf');
    }

    public function payments(Request $request): View
    {
        $student = $this->student($request);
        $year = $student->school?->defaultAcademicYear();

        $invoices = Invoice::where('student_id', $student->id)
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->with(['items', 'payments'])
            ->latest('issued_at')
            ->get();

        return view('student.payments', compact('student', 'year', 'invoices'));
    }

    private function student(Request $request)
    {
        $student = $request->user()->student;
        abort_unless($student, 403, 'Aucun profil élève associé à ce compte.');

        return $student;
    }

    private function authorizeTerm($student, Term $term): void
    {
        $hasEnrollment = $student->enrollments()->where('academic_year_id', $term->academic_year_id)->exists();
        abort_unless($hasEnrollment, 404);
    }
}
