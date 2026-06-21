<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRecord extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'campaign_id', 'participant', 'user_id', 'status', 'score', 'completed_at', 'evidence',
    ];

    protected function casts(): array
    {
        return ['score' => 'float', 'completed_at' => 'date'];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(TrainingCampaign::class, 'campaign_id');
    }
}
