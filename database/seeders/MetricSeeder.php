<?php

namespace Database\Seeders;

use App\Models\Metric;
use App\Models\MetricMeasurement;
use App\Models\Objective;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MetricSeeder extends Seeder
{
    public function run(): void
    {
        if (Metric::count() === 0) {
            $objIds = Objective::pluck('id', 'ref_id');
            $metrics = json_decode(file_get_contents(database_path('seeders/data/metrics.json')), true);
            $i = 1;
            foreach ($metrics as $m) {
                $ref = $m['objective_ref'] ?? null;
                unset($m['objective_ref']);
                Metric::create([
                    'ref_id' => sprintf('MET-%03d', $i++),
                    'objective_id' => $ref ? ($objIds[$ref] ?? null) : null,
                ] + $m);
            }
        }

        // Backfill ~6 monthly measurements per metric so trend charts render.
        if (MetricMeasurement::count() === 0) {
            $today = Carbon::today();
            foreach (Metric::all() as $m) {
                if ($m->current_value === null) {
                    continue;
                }
                $current = (float) $m->current_value;
                $start = $current * ($m->direction === 'lower_is_better' ? 1.3 : 0.7);
                $n = 6;
                for ($k = 0; $k < $n; $k++) {
                    $frac = $k / ($n - 1);
                    $value = round($start + ($current - $start) * $frac, 1);
                    MetricMeasurement::create([
                        'metric_id' => $m->id,
                        'value' => $value,
                        'captured_at' => $today->copy()->subDays(($n - 1 - $k) * 30),
                        'note' => 'seed backfill',
                    ]);
                }
            }
        }
    }
}
