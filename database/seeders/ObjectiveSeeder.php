<?php

namespace Database\Seeders;

use App\Models\Objective;
use Illuminate\Database\Seeder;

class ObjectiveSeeder extends Seeder
{
    public function run(): void
    {
        if (Objective::count() > 0) {
            return;
        }

        $objectives = json_decode(file_get_contents(database_path('seeders/data/objectives.json')), true);
        $i = 1;
        foreach ($objectives as $o) {
            Objective::create(['ref_id' => sprintf('OBJ-%03d', $i++)] + $o);
        }
    }
}
