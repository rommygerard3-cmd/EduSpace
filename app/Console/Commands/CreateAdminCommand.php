<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\School;
use App\Models\User;
use App\Services\MatriculeGenerator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminCommand extends Command
{
    protected $signature = 'eduspace:create-admin
        {--name=Administrateur général : Nom complet}
        {--email=admin@eduspace.local : Adresse email}
        {--password= : Mot de passe temporaire}
        {--no-force-change : Ne pas exiger le changement de mot de passe}';

    protected $description = 'Crée un compte administrateur général pour EduSpace';

    public function handle(MatriculeGenerator $generator): int
    {
        $school = School::first();

        if (! $school) {
            $this->error('Aucun établissement n’existe. Lancez d’abord les seeders de base.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: $this->secret('Mot de passe temporaire (8 caractères minimum)');

        if (! $password || strlen($password) < 8) {
            $this->error('Le mot de passe doit contenir au moins 8 caractères.');

            return self::FAILURE;
        }

        $user = User::create([
            'school_id' => $school->id,
            'name' => $this->option('name'),
            'email' => $this->option('email'),
            'matricule' => $generator->forUser('ADM'),
            'password' => Hash::make($password),
            'is_active' => true,
            'activated_at' => now(),
            'must_change_password' => ! $this->option('no-force-change'),
        ]);

        $user->assignRole(Role::where('name', Role::ADMINISTRATEUR_GENERAL)->firstOrFail());

        $this->info('Compte administrateur créé.');
        $this->line('Matricule : '.$user->matricule);
        $this->line('Le mot de passe n’est pas affiché. Changez-le à la première connexion.');

        return self::SUCCESS;
    }
}
