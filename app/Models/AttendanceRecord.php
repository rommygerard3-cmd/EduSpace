<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    public const STATUSES = [
        'present' => 'Présent',
        'absent' => 'Absent',
        'late' => 'En retard',
        'excused' => 'Excusé',
    ];

    protected $fillable = [
        'school_class_id',
        'subject_id',
        'teacher_id',
        'student_id',
        'date',
        'session',
        'status',
        'observation',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return ['date' => 'date'];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
