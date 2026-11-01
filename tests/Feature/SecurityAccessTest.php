<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAccessTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(string $role, array $permissions, string $matricule = 'SEC-2026-0001'): User
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

    public function test_student_cannot_access_admin_module(): void
    {
        $user = $this->userWithRole('eleve', []);

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_student_cannot_access_finance_module(): void
    {
        $user = $this->userWithRole('eleve', []);

        $this->actingAs($user)->get('/finance')->assertForbidden();
    }

    public function test_student_cannot_access_teacher_module(): void
    {
        $user = $this->userWithRole('eleve', []);

        $this->actingAs($user)->get('/teacher/assessments')->assertForbidden();
    }

    public function test_teacher_cannot_access_student_module(): void
    {
        $user = $this->userWithRole('enseignant', []);

        $this->actingAs($user)->get('/student/grades')->assertForbidden();
    }

    public function test_teacher_cannot_access_admin_module(): void
    {
        $user = $this->userWithRole('enseignant', []);

        $this->actingAs($user)->get('/admin/students')->assertForbidden();
    }

    public function test_prefect_cannot_access_finance_module(): void
    {
        $user = $this->userWithRole('prefet', []);

        $this->actingAs($user)->get('/finance')->assertForbidden();
    }

    public function test_discipline_director_cannot_access_academic_module(): void
    {
        $user = $this->userWithRole('directeur_discipline', []);

        $this->actingAs($user)->get('/teacher/assessments')->assertForbidden();
    }

    public function test_comptable_cannot_access_academic_module(): void
    {
        $user = $this->userWithRole('comptabilite', []);

        $this->actingAs($user)->get('/admin/students')->assertForbidden();
    }

    public function test_user_without_any_role_cannot_access_admin_module(): void
    {
        $school = School::create([
            'name' => 'Complexe Scolaire EduSpace',
            'code' => 'EDUSPACE',
            'settings' => ['currency' => 'USD', 'pass_mark' => 10],
        ]);

        $user = User::factory()->create([
            'school_id' => $school->id,
            'matricule' => 'SEC-2026-0002',
            'password' => Hash::make('Password123'),
            'is_active' => true,
            'activated_at' => now(),
            'must_change_password' => false,
        ]);

        $this->actingAs($user)->get('/admin/users')->assertForbidden();
    }

    public function test_student_cannot_update_own_profile_information(): void
    {
        $user = $this->userWithRole('eleve', []);

        $this->actingAs($user)->put('/profile', [
            'name' => 'Nouveau nom',
            'email' => 'eleve@example.com',
            'phone' => '+243000000000',
        ])->assertForbidden();
    }
}
