<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Audit extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'audit_type', 'status',
        'lead_auditor_id', 'start_date', 'end_date', 'scope', 'conclusion',
    ];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function leadAuditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_auditor_id');
    }

    public function findings(): HasMany
    {
        return $this->hasMany(AuditFinding::class, 'audit_id')->orderBy('ref_id');
    }
}
