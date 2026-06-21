<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MetricMeasurement extends Model
{
    use HasUuids;

    protected $fillable = ['metric_id', 'value', 'note', 'captured_at'];

    protected function casts(): array
    {
        return ['value' => 'float', 'captured_at' => 'date'];
    }

    public function metric(): BelongsTo
    {
        return $this->belongsTo(Metric::class, 'metric_id');
    }
}
