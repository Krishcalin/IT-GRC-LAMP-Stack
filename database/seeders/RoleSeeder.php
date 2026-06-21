<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'CISO', 'description' => 'Chief Information Security Officer — full access', 'permissions' => ['*']],
            ['name' => 'GRC_Manager', 'description' => 'GRC Manager — manage controls, risks, audits, policies', 'permissions' => ['controls:*', 'risks:*', 'audits:*', 'policies:*', 'soa:*', 'assets:*', 'evidence:*', 'users:read']],
            ['name' => 'Risk_Owner', 'description' => 'Risk Owner — manage assigned risks, view controls', 'permissions' => ['risks:own', 'controls:read', 'soa:read', 'assets:read']],
            ['name' => 'Control_Owner', 'description' => 'Control Owner — manage assigned controls, view risks', 'permissions' => ['controls:own', 'risks:read', 'soa:read', 'evidence:create']],
            ['name' => 'Auditor', 'description' => 'Auditor — manage audits and findings, read-only elsewhere', 'permissions' => ['audits:*', 'controls:read', 'risks:read', 'soa:read', 'policies:read', 'assets:read', 'evidence:read']],
            ['name' => 'Viewer', 'description' => 'Read-only access to all modules', 'permissions' => ['*:read']],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['name' => $role['name']], $role);
        }
    }
}
