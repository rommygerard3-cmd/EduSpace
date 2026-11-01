<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentService
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly InvoiceService $invoiceService,
    ) {}

    /**
     * @param  array{amount: float|string, method: string, reference?: ?string, paid_at?: ?string, notes?: ?string}  $data
     */
    public function record(Invoice $invoice, array $data, User $recordedBy): Payment
    {
        return DB::transaction(function () use ($invoice, $data, $recordedBy) {
            $invoice = Invoice::whereKey($invoice->id)->lockForUpdate()->firstOrFail();

            if ($invoice->status === 'cancelled') {
                throw ValidationException::withMessages(['amount' => 'Cette facture est annulée.']);
            }

            $amount = round((float) $data['amount'], 2);

            if ($amount <= 0) {
                throw ValidationException::withMessages(['amount' => 'Le montant doit être supérieur à zéro.']);
            }

            if ($amount > $invoice->balance()) {
                throw ValidationException::withMessages(['amount' => 'Le paiement dépasse le solde restant de '.number_format($invoice->balance(), 2).' '.config('app.currency', 'USD').'.']);
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'student_id' => $invoice->student_id,
                'amount' => $amount,
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
                'recorded_by' => $recordedBy->id,
                'status' => 'valid',
                'notes' => $data['notes'] ?? null,
            ]);

            $invoice->refreshStatus();
            $this->invoiceService->syncFeeStatuses($invoice);

            $this->auditLogger->log('payment.recorded', $payment, [], [
                'invoice' => $invoice->number,
                'amount' => $amount,
                'method' => $payment->method,
            ]);

            return $payment;
        });
    }

    public function cancel(Payment $payment, User $user): void
    {
        DB::transaction(function () use ($payment, $user) {
            $payment->status = 'cancelled';
            $payment->save();

            $invoice = $payment->invoice;
            $invoice->refreshStatus();
            $this->invoiceService->syncFeeStatuses($invoice);

            $this->auditLogger->log('payment.cancelled', $payment, ['status' => 'valid'], ['status' => 'cancelled']);
        });
    }
}
