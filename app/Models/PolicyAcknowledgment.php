<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PolicyAcknowledgment extends Model
{
    use HasUuids;

    protected $fillable = ['policy_id', 'user_id', 'acknowledged_at'];

    protected function casts(): array
    {
        return ['acknowledged_at' => 'datetime'];
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'policy_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
