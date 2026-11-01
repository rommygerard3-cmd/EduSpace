<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogger
{
    /**
     * @param  array<string, mixed>  $oldValues
     * @param  array<string, mixed>  $newValues
     */
    public function log(string $action, ?Model $model = null, array $oldValues = [], array $newValues = []): AuditLog
    {
        $request = request();

        return AuditLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model?->getMorphClass(),
            'model_id' => $model?->getKey(),
            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array<string, mixed>
     */
    private function sanitize(array $values): array
    {
        unset($values['password'], $values['password_confirmation'], $values['activation_code'], $values['activation_code_hash']);

        return $values;
    }
}
