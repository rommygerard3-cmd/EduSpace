<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ModuleViewsTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, array $permissions, string $matricule = 'ADM-2026-0001'): User
    {
        $roleModel = Role::firstOrCreate([
            'name' => $role,
            'label' => ucfirst($role),
        ]);

        foreach ($permissions as $permissionName) {
            $permissionModel = Permission::firstOrCreate([
                'name' => $permissionName,
                'label' => $permissionName,
                'group' => 'general',
            ]);
            $roleModel->permissions()->attach($permissionModel);
        }

        $school = School::create([
            'name' => 'Complexe Scolaire EduSpace',
            'code' => 'EDUSPACE',
            'settings' => ['currency' => 'USD', 'pass_mark' => 10],
        ]);

        $user = User::factory()->create([
            'school_id' => $school->id,
            'matricule' => $matricule,
            'password' => Hash::make('Password123'),
            'is_active' => true,
            'activated_at' => now(),
            'must_change_password' => false,
        ]);

        $user->assignRole($roleModel);

        return $user->fresh();
    }

    public function test_discipline_dashboard_view_renders(): void
    {
        $user = $this->userWithRole('directeur_discipline', ['discipline.manage', 'students.view'], 'DIS-2026-0001');

        $this->actingAs($user)->get('/discipline')->assertOk();
    }

    public function test_finance_dashboard_view_renders(): void
    {
        $user = $this->userWithRole('comptabilite', ['finances.view', 'finances.manage'], 'CPT-2026-0001');

        $this->actingAs($user)->get('/finance')->assertOk();
    }

    public function test_admin_audits_view_renders(): void
    {
        $user = $this->userWithRole('administrateur_general', ['audits.view', 'settings.manage'], 'ADM-2026-0002');

        $this->actingAs($user)->get('/admin/audits')->assertOk();
    }

    public function test_admin_reports_view_renders(): void
    {
        $user = $this->userWithRole('administrateur_general', ['reports.view'], 'ADM-2026-0003');

        $this->actingAs($user)->get('/admin/reports')->assertOk();
    }

    public function test_admin_settings_view_renders(): void
    {
        $user = $this->userWithRole('administrateur_general', ['settings.manage'], 'ADM-2026-0004');

        $this->actingAs($user)->get('/admin/settings')->assertOk();
    }

    public function test_search_view_renders(): void
    {
        $user = $this->userWithRole('administrateur_general', ['users.manage', 'students.manage'], 'ADM-2026-0005');

        $this->actingAs($user)->get('/search?q=test')->assertOk();
    }

    public function test_teacher_preparations_index_view_renders(): void
    {
        $user = $this->userWithRole('enseignant', ['preparations.submit', 'preparations.review'], 'ENS-2026-0001');

        $this->actingAs($user)->get('/teacher/preparations')->assertForbidden();
    }
}
