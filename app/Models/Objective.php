<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Objective extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'clause_ref', 'measure',
        'target_value', 'current_value', 'unit', 'status', 'owner_id', 'due_date', 'review_date',
    ];

    protected function casts(): array
    {
        return ['due_date' => 'date', 'review_date' => 'date'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function metrics(): HasMany
    {
        return $this->hasMany(Metric::class, 'objective_id');
    }
}
