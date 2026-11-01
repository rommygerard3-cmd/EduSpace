<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'comment',
        'status',
        'entered_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function percentage(): ?float
    {
        if ($this->score === null || (float) $this->assessment->max_score === 0.0) {
            return null;
        }

        return round(((float) $this->score / (float) $this->assessment->max_score) * 100, 2);
    }
}
