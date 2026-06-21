<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policy extends Model
{
    use HasUuids;

    protected $table = 'policies';

    protected $fillable = [
        'ref_id', 'title', 'description', 'version', 'status', 'category', 'owner_id',
        'approved_by', 'approved_at', 'effective_date', 'review_date', 'next_review_date', 'content',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'effective_date' => 'date',
            'review_date' => 'date',
            'next_review_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function acknowledgments(): HasMany
    {
        return $this->hasMany(PolicyAcknowledgment::class, 'policy_id');
    }
}
