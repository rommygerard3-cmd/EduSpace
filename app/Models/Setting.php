<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    protected $fillable = ['school_id', 'key', 'value', 'group'];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public static function getValue(string $key, mixed $default = null, ?int $schoolId = null): mixed
    {
        $setting = static::where('school_id', $schoolId)->where('key', $key)->first();

        return $setting?->value ?? $default;
    }
}
