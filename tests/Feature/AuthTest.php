<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Services\ActivationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdminUser(string $matricule = 'ADM-2026-0001', bool $active = true): User
    {
        $role = Role::firstOrCreate([
            'name' => 'administrateur_general',
            'label' => 'Administrateur general',
        ]);

        $user = User::factory()->create([
            'matricule' => $matricule,
            'password' => Hash::make('Password123'),
            'is_active' => $active,
            'activated_at' => now(),
            'must_change_password' => false,
        ]);

        $user->assignRole($role);

        return $user->fresh();
    }

    public function test_guest_is_redirected_from_dashboard_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_guest_can_view_the_login_page(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_guest_is_redirected_from_the_root_to_the_login_page(): void
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_user_can_authenticate_with_matricule_and_password(): void
    {
        $user = $this->makeAdminUser();

        $response = $this->post('/login', [
            'matricule' => $user->matricule,
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_unknown_matricule_is_rejected(): void
    {
        $response = $this->post('/login', [
            'matricule' => 'ELE-2026-9999',
            'password' => 'Password123',
        ]);

        $response->assertSessionHasErrors('matricule');
        $this->assertGuest();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = $this->makeAdminUser('ADM-2026-0002', false);

        $response = $this->post('/login', [
            'matricule' => $user->matricule,
            'password' => 'Password123',
        ]);

        $response->assertSessionHasErrors('matricule');
        $this->assertGuest();
    }

    public function test_user_with_pending_activation_is_redirected_to_activation(): void
    {
        $user = User::factory()->create([
            'matricule' => 'ADM-2026-0003',
            'password' => Hash::make('Password123'),
            'is_active' => true,
            'activation_code_hash' => 'pending',
            'activation_expires_at' => now()->addDays(2),
            'activated_at' => null,
            'must_change_password' => true,
        ]);

        $response = $this->post('/login', [
            'matricule' => 'ADM-2026-0003',
            'password' => 'Password123',
        ]);

        $response->assertRedirect(route('activate.show', ['matricule' => 'ADM-2026-0003']));
    }

    public function test_user_can_activate_account_with_code_and_set_password(): void
    {
        $user = User::factory()->create([
            'matricule' => 'ADM-2026-0004',
            'password' => Hash::make('Password123'),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $code = (new ActivationService)->prepare($user);

        $this->assertTrue((new ActivationService)->activate($user, $code, 'NewPassword123'));

        $this->assertNotNull($user->fresh()->activated_at);
        $this->assertFalse((bool) $user->fresh()->must_change_password);
        $this->assertTrue((bool) $user->fresh()->is_active);
    }

    public function test_user_can_change_their_password(): void
    {
        $user = User::factory()->create([
            'matricule' => 'ADM-2026-0005',
            'password' => Hash::make('Password123'),
            'is_active' => true,
            'must_change_password' => true,
        ]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'Password123',
            'password' => 'NewPassword123',
            'password_confirmation' => 'NewPassword123',
        ]);

        $response->assertRedirect();
        $this->assertFalse((bool) $user->fresh()->must_change_password);
    }
}
