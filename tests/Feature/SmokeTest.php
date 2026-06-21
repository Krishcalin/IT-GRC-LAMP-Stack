<?php

namespace Tests\Feature;

use App\Models\Assessment;
use App\Models\Control;
use App\Models\Metric;
use App\Models\TrainingCampaign;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Renders every authenticated page against the seeded database. Catches view /
 * Blade-component / route wiring errors (e.g. a missing <x-card> component) that
 * unit tests miss — every page must return HTTP 200.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $this->seed(DatabaseSeeder::class);

        return User::where('email', 'admin@company.com')->firstOrFail();
    }

    public function test_index_pages_render(): void
    {
        $this->actingAs($this->admin());

        $paths = [
            '/', 'controls', 'risks', 'clauses', 'soa', 'documents', 'policies',
            'suppliers', 'incidents', 'assets', 'interested-parties', 'objectives',
            'metrics', 'tasks', 'assessments', 'audits', 'training', 'evidence',
            'analytics', 'frameworks', 'reports', 'reminders',
            'risks/create', 'documents/create', 'metrics/create', 'audits/create',
        ];
        foreach ($paths as $p) {
            $this->get('/'.ltrim($p, '/'))->assertOk();
        }
    }

    public function test_detail_pages_render(): void
    {
        // Show-page templates for seeded modules (the unseeded ones — risks /
        // audits / policies — reuse the same Blade components exercised here and
        // on their index pages).
        $this->actingAs($this->admin());

        $this->get(route('controls.show', Control::first()))->assertOk();
        $this->get(route('assessments.show', Assessment::first()))->assertOk();
        $this->get(route('metrics.show', Metric::first()))->assertOk();
        $this->get(route('training.show', TrainingCampaign::first()))->assertOk();
    }
}
