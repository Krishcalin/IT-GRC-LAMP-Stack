<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Order matters: roles first, then the admin user, then the GRC catalogs.
        $this->call([
            RoleSeeder::class,
            // GRC domain seeders are appended here in Phase 2:
            // ControlSeeder::class, ClauseSeeder::class, DocumentSeeder::class, ...
        ]);

        $email = env('FIRST_SUPERUSER_EMAIL', 'admin@company.com');

        $admin = User::firstOrCreate(
            ['email' => $email],
            [
                'full_name' => env('FIRST_SUPERUSER_NAME', 'GRC Administrator'),
                'hashed_password' => Hash::make(env('FIRST_SUPERUSER_PASSWORD', 'Admin@123')),
                'is_active' => true,
                'is_superuser' => true,
                'auth_provider' => 'local',
            ]
        );

        if ($ciso = Role::where('name', 'CISO')->first()) {
            $admin->roles()->syncWithoutDetaching([$ciso->id]);
        }
    }
}
