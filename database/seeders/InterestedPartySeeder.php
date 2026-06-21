<?php

namespace Database\Seeders;

use App\Models\InterestedParty;
use Illuminate\Database\Seeder;

class InterestedPartySeeder extends Seeder
{
    public function run(): void
    {
        if (InterestedParty::count() > 0) {
            return;
        }

        $parties = json_decode(file_get_contents(database_path('seeders/data/interested_parties.json')), true);
        $i = 1;
        foreach ($parties as $p) {
            InterestedParty::create(['ref_id' => sprintf('PARTY-%03d', $i++)] + $p);
        }
    }
}
