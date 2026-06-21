<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'category', 'severity', 'status', 'reporter',
        'reported_at', 'owner_id', 'risk_id', 'affected_assets', 'data_breach',
        'containment_actions', 'root_cause', 'lessons_learned', 'evidence_notes', 'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'data_breach' => 'boolean',
            'reported_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }
}
