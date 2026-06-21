<?php

namespace Database\Seeders;

use App\Models\DocumentedInformation;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        if (DocumentedInformation::count() > 0) {
            return;
        }

        $docs = json_decode(file_get_contents(database_path('seeders/data/documents.json')), true);
        $i = 1;
        foreach ($docs as $d) {
            DocumentedInformation::create([
                'ref_id' => sprintf('DOC-%03d', $i++),
                'title' => $d['title'],
                'doc_type' => $d['doc_type'],
                'clause_ref' => $d['clause_ref'] ?? null,
                'description' => $d['description'] ?? null,
                'mandatory' => true,
                'version' => '1.0',
                'status' => 'Draft',
                'classification' => 'Internal',
            ]);
        }
    }
}
