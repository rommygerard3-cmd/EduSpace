<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreparationSheet extends Model
{
    use HasFactory;

    public const STATUSES = [
        'draft' => 'Brouillon',
        'submitted' => 'Soumise',
        'reviewed' => 'Examinée',
        'approved' => 'Approuvée',
        'revision' => 'À corriger',
    ];

    protected $fillable = [
        'teacher_id',
        'school_class_id',
        'subject_id',
        'class_subject_id',
        'academic_year_id',
        'term_id',
        'date',
        'topic',
        'objectives',
        'prerequisites',
        'materials',
        'steps',
        'evaluation_methods',
        'status',
        'reviewer_id',
        'review_notes',
        'submitted_at',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
        ];
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function classSubject(): BelongsTo
    {
        return $this->belongsTo(ClassSubject::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}
