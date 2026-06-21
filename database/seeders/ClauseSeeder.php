<?php

namespace Database\Seeders;

use App\Models\ClauseRequirement;
use Illuminate\Database\Seeder;

class ClauseSeeder extends Seeder
{
    public function run(): void
    {
        $clauses = json_decode(file_get_contents(database_path('seeders/data/clauses.json')), true);
        foreach ($clauses as $c) {
            ClauseRequirement::updateOrCreate(
                ['clause' => $c['clause']],
                [
                    'title' => $c['title'],
                    'section' => $c['section'],
                    'clause_number' => $c['clause_number'],
                    'requirement' => $c['requirement'],
                    'documented_info' => $c['documented_info'] ?? null,
                ]
            );
        }
    }
}
