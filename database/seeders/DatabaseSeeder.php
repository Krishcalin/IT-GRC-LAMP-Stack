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
        // 1) Roles, then the first superuser (sample seeders assign work to it).
        $this->call(RoleSeeder::class);

        $admin = User::firstOrCreate(
            ['email' => env('FIRST_SUPERUSER_EMAIL', 'admin@company.com')],
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

        // 2) GRC catalogs + sample data.
        $this->call([
            ControlSeeder::class,        // 148 controls (5 frameworks) + 96 crosswalk mappings
            ClauseSeeder::class,         // 30 ISMS clauses (4-10)
            DocumentSeeder::class,       // 17 mandatory documents
            InterestedPartySeeder::class,
            ObjectiveSeeder::class,
            MetricSeeder::class,         // metrics + measurement history
            SupplierSeeder::class,
            IncidentSeeder::class,
            TrainingSeeder::class,
            AssessmentSeeder::class,
            TaskSeeder::class,
            PostureSnapshotSeeder::class,
        ]);
    }
}
