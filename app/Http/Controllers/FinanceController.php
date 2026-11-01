<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Student;
use App\Services\InvoiceService;
use App\Services\PaymentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class FinanceController extends Controller
{
    public function __construct(
        private readonly PaymentService $payments,
        private readonly InvoiceService $invoices,
    ) {}

    public function dashboard(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        $invoices = Invoice::when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->where('status', '!=', 'cancelled')
            ->get();

        $paid = Payment::when($year, fn ($query) => $query->whereHas('invoice', fn ($invoice) => $invoice->where('academic_year_id', $year->id)))
            ->where('status', 'valid')
            ->sum('amount');

        $invoiced = (float) $invoices->sum('total');
        $paid = (float) $paid;

        return view('finance.dashboard', [
            'year' => $year,
            'invoiced' => round($invoiced, 2),
            'paid' => round($paid, 2),
            'balance' => round($invoiced - $paid, 2),
            'expected_fees' => (float) Fee::when($year, fn ($query) => $query->where('academic_year_id', $year->id))->sum('amount'),
            'recentPayments' => Payment::with(['student', 'invoice'])->latest('paid_at')->limit(10)->get(),
            'unpaidInvoices' => Invoice::with('student')->whereIn('status', ['issued', 'partial'])->latest()->limit(10)->get(),
        ]);
    }

    public function invoices(Request $request): View
    {
        $school = $request->user()->school;
        $year = $school?->defaultAcademicYear();

        $invoices = Invoice::query()
            ->whereHas('student', fn ($query) => $query->where('school_id', $school?->id))
            ->when($year, fn ($query) => $query->where('academic_year_id', $year->id))
            ->with(['student', 'payments'])
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->string('q').'%';
                $query->where(fn ($inner) => $inner->where('number', 'like', $term)
                    ->orWhereHas('student', fn ($student) => $student->where('first_name', 'like', $term)->orWhere('last_name', 'like', $term)->orWhere('matricule', 'like', $term)));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->latest('issued_at')
            ->paginate(30)
            ->withQueryString();

        return view('finance.invoices.index', compact('invoices', 'year'));
    }

    public function showInvoice(Request $request, Invoice $invoice): View
    {
        abort_unless($invoice->student?->school_id === $request->user()->school_id, 404);

        $invoice->load(['student.currentEnrollment.schoolClass', 'items', 'payments.recordedBy', 'academicYear']);

        return view('finance.invoices.show', compact('invoice'));
    }

    public function storePayment(Request $request, Invoice $invoice): RedirectResponse
    {
        abort_unless($invoice->student?->school_id === $request->user()->school_id, 404);

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'method' => ['required', Rule::in(['cash', 'bank', 'mobile_money', 'cheque', 'other'])],
            'reference' => ['nullable', 'string', 'max:100'],
            'paid_at' => ['required', 'date', 'before_or_equal:now'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->payments->record($invoice, $validated, $request->user());

        return redirect()->route('finance.invoices.show', $invoice)->with('success', 'Paiement enregistré.');
    }

    public function cancelPayment(Request $request, Payment $payment): RedirectResponse
    {
        abort_unless($payment->invoice?->student?->school_id === $request->user()->school_id, 404);

        if ($payment->status === 'cancelled') {
            return back()->withErrors(['payment' => 'Ce paiement est déjà annulé.']);
        }

        $this->payments->cancel($payment, $request->user());

        return back()->with('success', 'Paiement annulé. Le solde de la facture a été recalculé.');
    }

    public function receipt(Request $request, Payment $payment): Response
    {
        abort_unless($payment->invoice?->student?->school_id === $request->user()->school_id, 404);

        $payment->load(['invoice.items', 'invoice.student', 'invoice.academicYear', 'recordedBy', 'student']);

        $pdf = Pdf::loadView('pdf.receipt', [
            'payment' => $payment,
            'invoice' => $payment->invoice,
            'school' => $request->user()->school,
        ])->setPaper('a5');

        return $pdf->download('recu-'.$payment->id.'.pdf');
    }

    public function generateInvoice(Request $request, Student $student): RedirectResponse
    {
        abort_unless($student->school_id === $request->user()->school_id, 404);

        $year = $request->user()->school?->defaultAcademicYear();
        abort_unless($year, 422, 'Aucune année scolaire active.');

        $invoice = $this->invoices->ensureForStudent($student, $year);

        if (! $invoice) {
            return back()->withErrors(['invoice' => 'Aucun frais n’est enregistré pour cet élève sur l’année active.']);
        }

        return redirect()->route('finance.invoices.show', $invoice)->with('success', 'Facture générée.');
    }
}
