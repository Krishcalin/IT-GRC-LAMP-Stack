<?php

namespace Database\Seeders;

use App\Models\Control;
use App\Models\ControlMapping;
use Illuminate\Database\Seeder;

/**
 * Seeds all 148 controls across 5 frameworks + the cross-framework crosswalk,
 * from data/controls.json and data/control_mappings.json (extracted verbatim from
 * the source FastAPI seed module). Idempotent: controls keyed by clause, mappings
 * by (source,target) pair, so re-running won't duplicate or clobber user status.
 */
class ControlSeeder extends Seeder
{
    public function run(): void
    {
        $controls = json_decode(file_get_contents(database_path('seeders/data/controls.json')), true);
        foreach ($controls as $c) {
            Control::updateOrCreate(
                ['clause' => $c['clause']],
                [
                    'title' => $c['title'],
                    'description' => $c['description'],
                    'framework' => $c['framework'] ?? 'ISO 27001:2022',
                    'theme' => $c['theme'],
                    'implementation_guidance' => $c['implementation_guidance'] ?? null,
                ]
            );
        }

        $idByClause = Control::pluck('id', 'clause');
        $mappings = json_decode(file_get_contents(database_path('seeders/data/control_mappings.json')), true);
        foreach ($mappings as [$src, $tgt, $rel]) {
            $s = $idByClause[$src] ?? null;
            $t = $idByClause[$tgt] ?? null;
            if (! $s || ! $t) {
                continue;
            }
            ControlMapping::firstOrCreate(
                ['source_control_id' => $s, 'target_control_id' => $t],
                ['relationship_type' => $rel]
            );
        }
    }
}
