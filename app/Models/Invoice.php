<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'number',
        'total',
        'status',
        'issued_at',
        'due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
            'issued_at' => 'datetime',
            'due_date' => 'date',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function paidTotal(): float
    {
        return (float) $this->payments()->where('status', 'valid')->sum('amount');
    }

    public function balance(): float
    {
        return round((float) $this->total - $this->paidTotal(), 2);
    }

    public function refreshStatus(): void
    {
        if ($this->status === 'cancelled') {
            return;
        }

        $paid = $this->paidTotal();

        $this->status = match (true) {
            $paid <= 0 => 'issued',
            $paid < (float) $this->total => 'partial',
            default => 'paid',
        };

        $this->save();
    }
}
