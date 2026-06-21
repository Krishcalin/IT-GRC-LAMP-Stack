<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentItem;
use App\Models\Control;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        if (Assessment::count() > 0) {
            return;
        }

        $ownerId = User::orderBy('created_at')->value('id');
        $supplierIds = Supplier::pluck('id', 'name');
        $controlIds = Control::pluck('id', 'clause');

        $assessments = json_decode(file_get_contents(database_path('seeders/data/assessments.json')), true);
        $ai = 1;
        $ii = 1;
        foreach ($assessments as $a) {
            $items = $a['items'] ?? [];
            $supplierName = $a['supplier'] ?? null;
            $assessment = Assessment::create([
                'ref_id' => sprintf('ASMT-%03d', $ai++),
                'owner_id' => $ownerId,
                'supplier_id' => $supplierName ? ($supplierIds[$supplierName] ?? null) : null,
                'title' => $a['title'],
                'assessment_type' => $a['assessment_type'],
                'framework' => $a['framework'] ?? null,
                'status' => $a['status'] ?? 'Draft',
                'description' => $a['description'] ?? null,
            ]);
            foreach ($items as $it) {
                $clause = $it['clause'] ?? null;
                unset($it['clause']);
                AssessmentItem::create([
                    'ref_id' => sprintf('ASI-%03d', $ii++),
                    'assessment_id' => $assessment->id,
                    'control_id' => $clause ? ($controlIds[$clause] ?? null) : null,
                ] + $it);
            }
        }
    }
}
