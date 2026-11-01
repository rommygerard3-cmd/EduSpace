<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ActivationService
{
    public function prepare(User $user): string
    {
        $code = $this->generateCode();

        $user->forceFill([
            'activation_code_hash' => Hash::make($code),
            'activation_expires_at' => now()->addDays(7),
            'activated_at' => null,
            'must_change_password' => true,
        ])->save();

        return $code;
    }

    public function activate(User $user, string $code, string $password): bool
    {
        if (! $user->activation_code_hash || ! $user->activation_expires_at) {
            return false;
        }

        if ($user->activation_expires_at->isPast()) {
            return false;
        }

        if (! Hash::check($code, $user->activation_code_hash)) {
            return false;
        }

        $user->forceFill([
            'password' => $password,
            'activation_code_hash' => null,
            'activation_expires_at' => null,
            'activated_at' => now(),
            'must_change_password' => false,
            'is_active' => true,
        ])->save();

        return true;
    }

    public function generateCode(int $length = 10): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $code = '';

        for ($i = 0; $i < $length; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $code;
    }
}
