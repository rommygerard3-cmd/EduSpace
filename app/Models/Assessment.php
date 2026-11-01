<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasFactory;

    public const TYPES = [
        'interrogation' => 'Interrogation',
        'devoir' => 'Devoir',
        'tp' => 'Travaux pratiques',
        'td' => 'Travaux dirigés',
        'examen' => 'Examen',
        'autre' => 'Autre',
    ];

    public const STATUSES = [
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'corrected' => 'Corrigé',
    ];

    protected $fillable = [
        'school_class_id',
        'subject_id',
        'term_id',
        'teacher_id',
        'created_by',
        'type',
        'title',
        'description',
        'date',
        'max_score',
        'coefficient',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'max_score' => 'decimal:2',
            'coefficient' => 'decimal:2',
            'published_at' => 'datetime',
        ];
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
