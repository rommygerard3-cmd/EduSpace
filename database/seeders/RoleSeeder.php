<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'students.view' => ['Consulter les élèves', 'Élèves'],
            'students.manage' => ['Gérer les élèves', 'Élèves'],
            'teachers.view' => ['Consulter les enseignants', 'Enseignants'],
            'teachers.manage' => ['Gérer les enseignants', 'Enseignants'],
            'users.manage' => ['Gérer les comptes', 'Administration'],
            'roles.manage' => ['Gérer les rôles et permissions', 'Administration'],
            'classes.view' => ['Consulter les classes', 'Structure'],
            'classes.manage' => ['Gérer les classes', 'Structure'],
            'subjects.manage' => ['Gérer les matières', 'Structure'],
            'years.manage' => ['Gérer les années et périodes', 'Structure'],
            'schedules.manage' => ['Gérer les horaires', 'Structure'],
            'assessments.view' => ['Consulter les évaluations', 'Pédagogie'],
            'assessments.manage' => ['Créer et modifier les évaluations', 'Pédagogie'],
            'grades.enter' => ['Saisir les notes', 'Pédagogie'],
            'grades.publish' => ['Publier les résultats', 'Pédagogie'],
            'grades.view' => ['Consulter les notes', 'Pédagogie'],
            'attendance.view' => ['Consulter les présences', 'Pédagogie'],
            'attendance.manage' => ['Gérer les présences', 'Pédagogie'],
            'report_cards.view' => ['Consulter les bulletins', 'Pédagogie'],
            'report_cards.publish' => ['Publier les bulletins', 'Pédagogie'],
            'preparations.submit' => ['Soumettre des fiches de préparation', 'Pédagogie'],
            'preparations.review' => ['Examiner les fiches de préparation', 'Pédagogie'],
            'prefect.supervise' => ['Superviser la pédagogie', 'Pédagogie'],
            'finances.view' => ['Consulter les finances', 'Finances'],
            'finances.manage' => ['Gérer les finances', 'Finances'],
            'discipline.manage' => ['Gérer la discipline', 'Discipline'],
            'notifications.view' => ['Consulter les notifications', 'Communication'],
            'notifications.manage' => ['Envoyer des notifications', 'Communication'],
            'audits.view' => ['Consulter le journal d’audit', 'Administration'],
            'settings.manage' => ['Gérer les paramètres', 'Administration'],
            'reports.view' => ['Consulter les rapports', 'Rapports'],
        ];

        $permissionIds = [];

        foreach ($permissions as $name => [$label, $group]) {
            $permission = Permission::firstOrCreate(['name' => $name], ['label' => $label, 'group' => $group]);
            $permissionIds[$name] = $permission->id;
        }

        $roles = [
            Role::ADMINISTRATEUR_GENERAL => 'Administrateur général',
            Role::DIRECTION => 'Direction',
            Role::SECRETARIAT => 'Secrétariat',
            Role::COMPTABILITE => 'Comptabilité',
            Role::ENSEIGNANT => 'Enseignant',
            Role::ELEVE => 'Élève',
            Role::PREFET => 'Préfet',
            Role::DIRECTEUR_DISCIPLINE => 'Directeur de discipline',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate(['name' => $name], ['label' => $label]);
        }

        $all = array_keys($permissionIds);

        $mapping = [
            Role::ADMINISTRATEUR_GENERAL => $all,
            Role::DIRECTION => $all,
            Role::SECRETARIAT => [
                'students.view', 'students.manage', 'teachers.view', 'users.manage',
                'classes.view', 'classes.manage', 'years.manage', 'reports.view',
                'notifications.view', 'notifications.manage',
            ],
            Role::COMPTABILITE => [
                'students.view', 'finances.view', 'finances.manage', 'reports.view', 'notifications.view',
            ],
            Role::ENSEIGNANT => [
                'students.view', 'classes.view', 'assessments.view', 'assessments.manage',
                'grades.enter', 'grades.publish', 'grades.view', 'attendance.view', 'attendance.manage',
                'preparations.submit', 'reports.view', 'notifications.view', 'report_cards.view',
            ],
            Role::ELEVE => [
                'grades.view', 'report_cards.view', 'finances.view', 'notifications.view', 'attendance.view',
            ],
            Role::PREFET => [
                'students.view', 'teachers.view', 'classes.view', 'assessments.view', 'grades.view',
                'attendance.view', 'preparations.review', 'prefect.supervise', 'reports.view',
                'notifications.view', 'report_cards.view',
            ],
            Role::DIRECTEUR_DISCIPLINE => [
                'students.view', 'classes.view', 'attendance.view', 'discipline.manage',
                'notifications.view', 'notifications.manage', 'reports.view',
            ],
        ];

        foreach ($mapping as $roleName => $names) {
            $role = Role::where('name', $roleName)->firstOrFail();
            $role->permissions()->sync(array_map(fn ($name) => $permissionIds[$name], $names));
        }
    }
}
