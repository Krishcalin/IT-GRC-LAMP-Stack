<?php

namespace App\Support;

/** Generates sequential, zero-padded reference IDs (RISK-001, DOC-014, …). */
class Refs
{
    public static function next(string $modelClass, string $prefix): string
    {
        $last = $modelClass::where('ref_id', 'like', $prefix.'-%')
            ->orderByDesc('ref_id')
            ->value('ref_id');
        $n = $last ? ((int) substr($last, strlen($prefix) + 1)) + 1 : 1;

        return sprintf('%s-%03d', $prefix, $n);
    }
}
