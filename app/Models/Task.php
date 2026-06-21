<?php

namespace App\Models;

use App\Support\Scoring;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    use HasUuids;

    public const OPEN_STATUSES = ['Open', 'In Progress', 'Blocked'];

    protected $fillable = [
        'ref_id', 'title', 'description', 'task_type', 'status', 'priority',
        'assignee_id', 'created_by_id', 'due_date', 'completed_at',
        'resource_type', 'resource_id', 'resource_label',
        'decision', 'decision_comment', 'decided_by_id', 'decided_at',
    ];

    protected $appends = ['overdue'];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function decidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by_id');
    }

    protected function overdue(): Attribute
    {
        return Attribute::get(fn () => Scoring::taskIsOverdue($this->due_date, $this->status));
    }
}
