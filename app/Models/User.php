<?php

namespace App\Models;

use App\Models\Concerns\HasRoles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'school_id',
        'name',
        'email',
        'matricule',
        'phone',
        'photo_path',
        'password',
        'is_active',
        'activation_code_hash',
        'activation_expires_at',
        'activated_at',
        'must_change_password',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'activation_code_hash',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'activation_expires_at' => 'datetime',
            'activated_at' => 'datetime',
            'must_change_password' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class);
    }

    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class)->latest();
    }

    public function unreadNotificationsCount(): int
    {
        return $this->appNotifications()->whereNull('read_at')->count();
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function isStudent(): bool
    {
        return $this->hasRole(Role::ELEVE);
    }

    public function isTeacher(): bool
    {
        return $this->hasRole(Role::ENSEIGNANT);
    }

    public function isPrefect(): bool
    {
        return $this->hasRole(Role::PREFET);
    }

    public function isDisciplineDirector(): bool
    {
        return $this->hasRole(Role::DIRECTEUR_DISCIPLINE);
    }

    public function isDirection(): bool
    {
        return $this->hasRole([Role::DIRECTION, Role::ADMINISTRATEUR_GENERAL]);
    }

    public function dashboardRoute(): string
    {
        return match (true) {
            $this->isStudent() => route('student.dashboard'),
            $this->isTeacher() => route('teacher.dashboard'),
            $this->isPrefect() => route('prefect.dashboard'),
            $this->isDisciplineDirector() => route('discipline.dashboard'),
            default => route('dashboard'),
        };
    }
}
