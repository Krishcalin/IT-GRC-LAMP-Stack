<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingCampaign extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'training_type', 'topic', 'clause_ref',
        'status', 'audience', 'materials_link', 'owner_id', 'start_date', 'end_date',
    ];

    protected $appends = ['total_participants', 'completed_participants', 'completion_rate'];

    protected function casts(): array
    {
        return ['start_date' => 'date', 'end_date' => 'date'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(TrainingRecord::class, 'campaign_id')->orderBy('ref_id');
    }

    protected function totalParticipants(): Attribute
    {
        return Attribute::get(fn () => $this->records->count());
    }

    protected function completedParticipants(): Attribute
    {
        return Attribute::get(fn () => $this->records->where('status', 'Completed')->count());
    }

    protected function completionRate(): Attribute
    {
        return Attribute::get(function () {
            $total = $this->records->count();
            return $total ? round($this->records->where('status', 'Completed')->count() / $total * 100, 1) : 0.0;
        });
    }
}
