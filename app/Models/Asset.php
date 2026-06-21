<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Asset extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'name', 'description', 'asset_type', 'classification',
        'owner_id', 'department', 'location', 'status', 'criticality',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function risks(): BelongsToMany
    {
        return $this->belongsToMany(Risk::class, 'asset_risks');
    }
}
