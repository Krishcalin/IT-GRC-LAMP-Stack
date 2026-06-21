<?php

namespace Database\Seeders;

use App\Models\TrainingCampaign;
use App\Models\TrainingRecord;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        if (TrainingCampaign::count() > 0) {
            return;
        }

        $campaigns = json_decode(file_get_contents(database_path('seeders/data/training.json')), true);
        $ci = 1;
        $ri = 1;
        foreach ($campaigns as $camp) {
            $records = $camp['records'] ?? [];
            unset($camp['records']);
            $campaign = TrainingCampaign::create(['ref_id' => sprintf('TRN-%03d', $ci++)] + $camp);
            foreach ($records as $rec) {
                TrainingRecord::create(
                    ['ref_id' => sprintf('TRR-%03d', $ri++), 'campaign_id' => $campaign->id] + $rec
                );
            }
        }
    }
}
