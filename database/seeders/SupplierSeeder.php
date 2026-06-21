<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        if (Supplier::count() > 0) {
            return;
        }

        $suppliers = json_decode(file_get_contents(database_path('seeders/data/suppliers.json')), true);
        $i = 1;
        foreach ($suppliers as $s) {
            Supplier::create(['ref_id' => sprintf('SUP-%03d', $i++)] + $s);
        }
    }
}
