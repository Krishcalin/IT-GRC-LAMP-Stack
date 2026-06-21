<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Validates the full Phase 2 data layer seeds correctly (runs on in-memory SQLite). */
class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_seed_loads_all_catalogs(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('roles', 6);
        $this->assertDatabaseCount('controls', 148);
        $this->assertDatabaseCount('control_mappings', 96);
        $this->assertDatabaseCount('clause_requirements', 30);
        $this->assertDatabaseCount('documented_information', 17);
        $this->assertDatabaseCount('interested_parties', 5);
        $this->assertDatabaseCount('objectives', 3);
        $this->assertDatabaseCount('metrics', 5);
        $this->assertDatabaseCount('suppliers', 4);
        $this->assertDatabaseCount('incidents', 4);
        $this->assertDatabaseCount('assessments', 2);
        $this->assertDatabaseCount('tasks', 5);
        $this->assertDatabaseCount('posture_snapshots', 5);

        // Re-seeding is idempotent (no duplicate controls/mappings).
        $this->seed(DatabaseSeeder::class);
        $this->assertDatabaseCount('controls', 148);
        $this->assertDatabaseCount('control_mappings', 96);
    }

    public function test_framework_spread(): void
    {
        $this->seed(\Database\Seeders\ControlSeeder::class);
        $this->assertDatabaseCount('controls', 148);
        $this->assertSame(93, \App\Models\Control::where('framework', 'ISO 27001:2022')->count());
        $this->assertSame(12, \App\Models\Control::where('framework', 'ISO 27019:2024')->count());
        $this->assertSame(22, \App\Models\Control::where('framework', 'NIST CSF 2.0')->count());
        $this->assertSame(13, \App\Models\Control::where('framework', 'SOC 2')->count());
        $this->assertSame(8, \App\Models\Control::where('framework', 'IEC 62443-2-1:2024')->count());
    }
}
