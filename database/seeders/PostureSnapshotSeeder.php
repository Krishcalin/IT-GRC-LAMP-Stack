<?php

namespace Database\Seeders;

use App\Models\PostureSnapshot;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostureSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        if (PostureSnapshot::count() > 0) {
            return;
        }

        $today = Carbon::today();
        // [days_ago, compliance, conformity, readiness, training]
        $history = [
            [150, 22.0, 30.0, 18.0, 60.0],
            [120, 31.0, 43.0, 24.0, 68.0],
            [90, 45.0, 57.0, 41.0, 75.0],
            [60, 58.0, 70.0, 53.0, 82.0],
            [30, 67.0, 80.0, 65.0, 90.0],
        ];

        foreach ($history as [$daysAgo, $comp, $conf, $ready, $train]) {
            PostureSnapshot::create([
                'snapshot_date' => $today->copy()->subDays($daysAgo),
                'compliance_score' => $comp,
                'isms_conformity_score' => $conf,
                'document_readiness_score' => $ready,
                'training_completion_rate' => $train,
                'implemented_controls' => (int) round(105 * $comp / 100),
                'total_controls' => 105,
                'open_risks' => max(0, (int) (12 - $comp / 10)),
                'critical_risks' => max(0, (int) (4 - $comp / 30)),
                'open_findings' => max(0, (int) (8 - $comp / 15)),
                'open_tasks' => max(0, (int) (10 - $comp / 12)),
                'overdue_tasks' => max(0, (int) (4 - $comp / 30)),
            ]);
        }
    }
}
