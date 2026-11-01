<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Fee;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function ensureForStudent(Student $student, AcademicYear $year): ?Invoice
    {
        $fees = Fee::query()
            ->where('student_id', $student->id)
            ->where('academic_year_id', $year->id)
            ->where('status', '!=', 'cancelled')
            ->with('category')
            ->get();

        if ($fees->isEmpty()) {
            return null;
        }

        return DB::transaction(function () use ($student, $year, $fees) {
            $existing = Invoice::query()
                ->where('student_id', $student->id)
                ->where('academic_year_id', $year->id)
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($existing) {
                return $existing;
            }

            $invoice = Invoice::create([
                'student_id' => $student->id,
                'academic_year_id' => $year->id,
                'number' => $this->nextNumber($year),
                'total' => round($fees->sum('amount'), 2),
                'status' => 'issued',
                'issued_at' => now(),
                'due_date' => $fees->pluck('due_date')->filter()->min(),
            ]);

            foreach ($fees as $fee) {
                $invoice->items()->create([
                    'fee_category_id' => $fee->fee_category_id,
                    'description' => $fee->category?->name ?? 'Frais scolaire',
                    'amount' => $fee->amount,
                ]);
            }

            return $invoice;
        });
    }

    public function nextNumber(AcademicYear $year): string
    {
        $prefix = 'INV-'.substr($year->name, 0, 4).'-';

        $last = Invoice::where('number', 'like', $prefix.'%')->orderByDesc('number')->value('number');
        $sequence = 1;

        if ($last && preg_match('/(\d+)$/', $last, $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix.str_pad((string) $sequence, 6, '0', STR_PAD_LEFT);
    }

    public function syncFeeStatuses(Invoice $invoice): void
    {
        $paid = $invoice->paidTotal();
        $remaining = max(0, (float) $invoice->total - $paid);

        $fees = Fee::query()
            ->where('student_id', $invoice->student_id)
            ->where('academic_year_id', $invoice->academic_year_id)
            ->get();

        foreach ($fees as $fee) {
            $fee->status = match (true) {
                $remaining <= 0 => 'paid',
                $paid > 0 => 'partial',
                default => 'pending',
            };
            $fee->save();
        }
    }
}
