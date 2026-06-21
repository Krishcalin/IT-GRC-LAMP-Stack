<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Seeder;

class IncidentSeeder extends Seeder
{
    public function run(): void
    {
        if (Incident::count() > 0) {
            return;
        }

        $incidents = json_decode(file_get_contents(database_path('seeders/data/incidents.json')), true);
        $i = 1;
        foreach ($incidents as $inc) {
            Incident::create(['ref_id' => sprintf('INC-%03d', $i++)] + $inc);
        }
    }
}
