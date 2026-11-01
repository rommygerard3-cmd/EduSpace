<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Convocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'created_by',
        'reason',
        'meeting_at',
        'recipient',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return ['meeting_at' => 'datetime'];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
