<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'phone',
        'email',
        'logo_path',
        'principal_name',
        'settings',
    ];

    protected function casts(): array
    {
        return ['settings' => 'array'];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function feeCategories(): HasMany
    {
        return $this->hasMany(FeeCategory::class);
    }

    public function currentAcademicYear(): ?AcademicYear
    {
        return $this->academicYears()->where('is_current', true)->first();
    }

    public function defaultAcademicYear(): ?AcademicYear
    {
        return $this->currentAcademicYear() ?? $this->academicYears()->latest('start_date')->first();
    }
}
