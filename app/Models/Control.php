<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Control extends Model
{
    use HasUuids;

    protected $fillable = [
        'clause', 'title', 'description', 'framework', 'theme',
        'implementation_guidance', 'status', 'owner_id', 'review_date',
    ];

    protected function casts(): array
    {
        return ['review_date' => 'date'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function soaEntry(): HasOne
    {
        return $this->hasOne(SoaEntry::class, 'control_id');
    }

    public function risks(): BelongsToMany
    {
        return $this->belongsToMany(Risk::class, 'risk_controls');
    }

    public function mappingsFrom(): HasMany
    {
        return $this->hasMany(ControlMapping::class, 'source_control_id');
    }

    public function mappingsTo(): HasMany
    {
        return $this->hasMany(ControlMapping::class, 'target_control_id');
    }
}
