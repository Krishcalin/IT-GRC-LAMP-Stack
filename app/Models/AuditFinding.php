<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditFinding extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'audit_id', 'control_id', 'finding_type', 'description', 'severity',
        'corrective_action', 'due_date', 'status', 'assigned_to', 'closed_at',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'closed_at' => 'datetime'];
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'control_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
