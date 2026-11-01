<?php

namespace App\Services;

use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class MatriculeGenerator
{
    public function forStudent(?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        return $this->next("MAT-{$year}-", Student::query(), 'matricule');
    }

    public function forUser(string $prefix = 'USR', ?int $year = null): string
    {
        $year ??= (int) now()->format('Y');

        return $this->next(strtoupper($prefix)."-{$year}-", User::query(), 'matricule');
    }

    private function next(string $prefix, $query, string $column): string
    {
        return DB::transaction(function () use ($prefix, $query, $column) {
            $last = $query->where($column, 'like', $prefix.'%')
                ->orderByDesc($column)
                ->lockForUpdate()
                ->value($column);

            $sequence = 1;

            if ($last && preg_match('/(\d+)$/', $last, $matches)) {
                $sequence = (int) $matches[1] + 1;
            }

            do {
                $matricule = $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                $sequence++;
            } while ($query->where($column, $matricule)->exists());

            return $matricule;
        });
    }
}
