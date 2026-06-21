<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk()->assertSee('Sign in');
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_role_seeder_creates_six_roles(): void
    {
        $this->seed(RoleSeeder::class);
        $this->assertDatabaseCount('roles', 6);
    }

    public function test_active_user_can_login(): void
    {
        $user = User::create([
            'email' => 'tester@example.com',
            'full_name' => 'Tester',
            'hashed_password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $this->post('/login', ['email' => 'tester@example.com', 'password' => 'secret123'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::create([
            'email' => 'inactive@example.com',
            'full_name' => 'Inactive',
            'hashed_password' => Hash::make('secret123'),
            'is_active' => false,
        ]);

        $this->post('/login', ['email' => 'inactive@example.com', 'password' => 'secret123']);
        $this->assertGuest();
    }
}
