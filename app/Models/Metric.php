<?php

namespace App\Models;

use App\Support\Scoring;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Metric extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'name', 'description', 'metric_type', 'clause_ref', 'objective_id',
        'target_value', 'current_value', 'unit', 'direction', 'frequency', 'owner_id', 'last_measured',
    ];

    protected $appends = ['rag'];

    protected function casts(): array
    {
        return [
            'target_value' => 'float',
            'current_value' => 'float',
            'last_measured' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function objective(): BelongsTo
    {
        return $this->belongsTo(Objective::class, 'objective_id');
    }

    public function measurements(): HasMany
    {
        return $this->hasMany(MetricMeasurement::class, 'metric_id')->orderBy('captured_at');
    }

    protected function rag(): Attribute
    {
        return Attribute::get(fn () => Scoring::computeRag($this->target_value, $this->current_value, $this->direction ?? 'higher_is_better'));
    }
}
