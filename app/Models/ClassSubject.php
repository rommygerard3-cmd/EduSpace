<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassSubject extends Model
{
    use HasFactory;

    protected $table = 'class_subject';

    protected $fillable = [
        'school_class_id',
        'subject_id',
        'teacher_id',
        'academic_year_id',
        'coefficient',
    ];

    protected function casts(): array
    {
        return ['coefficient' => 'decimal:2'];
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

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function preparationSheets(): HasMany
    {
        return $this->hasMany(PreparationSheet::class);
    }
}
