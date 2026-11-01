<?php

namespace App\Services;

use App\Models\AppNotification;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;

class NotificationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function toUser(User $user, string $type, string $title, string $body, array $data = [], string $channel = 'app'): AppNotification
    {
        $isInApp = $channel === 'app';

        return AppNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'data' => $data,
            'channel' => $channel,
            'status' => $isInApp ? 'sent' : 'pending',
            'sent_at' => $isInApp ? now() : null,
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function toRole(string $role, string $type, string $title, string $body, array $data = []): int
    {
        $count = 0;

        User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->where('name', $role))
            ->chunkById(100, function ($users) use (&$count, $type, $title, $body, $data) {
                foreach ($users as $user) {
                    $this->toUser($user, $type, $title, $body, $data);
                    $count++;
                }
            });

        return $count;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function toStudent(Student $student, string $type, string $title, string $body, array $data = []): ?AppNotification
    {
        if (! $student->user || ! $student->user->is_active) {
            return null;
        }

        return $this->toUser($student->user, $type, $title, $body, $data);
    }

    public function notifyDiscipline(string $type, string $title, string $body, array $data = []): int
    {
        return $this->toRole(Role::DIRECTEUR_DISCIPLINE, $type, $title, $body, $data);
    }
}
