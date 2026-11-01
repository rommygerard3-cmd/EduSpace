<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'matricule',
        'first_name',
        'last_name',
        'gender',
        'birth_date',
        'birth_place',
        'phone',
        'address',
        'photo_path',
        'status',
    ];

    protected function casts(): array
    {
        return ['birth_date' => 'date'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function schoolClasses(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'enrollments')
            ->withPivot(['academic_year_id', 'roll_number', 'status', 'enrolled_at'])
            ->withTimestamps();
    }

    public function guardians(): BelongsToMany
    {
        return $this->belongsToMany(Guardian::class, 'guardian_student')->withTimestamps();
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function conductNotes(): HasMany
    {
        return $this->hasMany(ConductNote::class);
    }

    public function convocations(): HasMany
    {
        return $this->hasMany(Convocation::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function fullName(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function currentEnrollment(): ?Enrollment
    {
        $year = $this->school?->defaultAcademicYear();

        if (! $year) {
            return null;
        }

        return $this->enrollments()->where('academic_year_id', $year->id)->with('schoolClass')->first();
    }

    public function currentClass(): ?SchoolClass
    {
        return $this->currentEnrollment()?->schoolClass;
    }

    public function publishedGrades()
    {
        return $this->grades()->where('status', 'published')->with(['assessment.subject', 'assessment.term']);
    }

    public function totalPaid(): float
    {
        return (float) $this->payments()->where('status', 'valid')->sum('amount');
    }

    public function totalInvoiced(): float
    {
        return (float) $this->invoices()->where('status', '!=', 'cancelled')->sum('total');
    }

    public function balance(): float
    {
        return round($this->totalInvoiced() - $this->totalPaid(), 2);
    }
}
