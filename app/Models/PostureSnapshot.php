<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PostureSnapshot extends Model
{
    use HasUuids;

    protected $fillable = [
        'snapshot_date', 'compliance_score', 'isms_conformity_score',
        'document_readiness_score', 'training_completion_rate',
        'implemented_controls', 'total_controls', 'open_risks', 'critical_risks',
        'open_findings', 'open_tasks', 'overdue_tasks',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'compliance_score' => 'float',
            'isms_conformity_score' => 'float',
            'document_readiness_score' => 'float',
            'training_completion_rate' => 'float',
        ];
    }
}
