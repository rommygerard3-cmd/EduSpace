<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Services\MatriculeGenerator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();

        if (! $school) {
            return;
        }

        if (User::where('matricule', 'ADM-2026-0001')->exists()) {
            return;
        }

        $password = env('EDUSPACE_ADMIN_PASSWORD');
        $generated = false;

        if (! $password) {
            $password = Str::password(16, symbols: false);
            $generated = true;
        }

        $generator = app(MatriculeGenerator::class);

        $user = User::create([
            'school_id' => $school->id,
            'name' => 'Administrateur général',
            'email' => 'admin@eduspace.local',
            'matricule' => $generator->forUser('ADM'),
            'password' => Hash::make($password),
            'is_active' => true,
            'activated_at' => now(),
            'must_change_password' => true,
        ]);

        $user->assignRole(Role::where('name', Role::ADMINISTRATEUR_GENERAL)->firstOrFail());

        if ($generated) {
            Storage::disk('local')->put('eduspace-admin.txt', "Matricule: {$user->matricule}\nMot de passe temporaire: {$password}\n");
        }
    }
}
