<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Supplier extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'name', 'description', 'category', 'service_description', 'criticality',
        'data_classification', 'status', 'is_requirements_agreed', 'right_to_audit',
        'processes_pii', 'certifications', 'owner_id', 'contract_start', 'contract_end',
        'last_review_date', 'next_review_date', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_requirements_agreed' => 'boolean',
            'right_to_audit' => 'boolean',
            'processes_pii' => 'boolean',
            'contract_start' => 'date',
            'contract_end' => 'date',
            'last_review_date' => 'date',
            'next_review_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
