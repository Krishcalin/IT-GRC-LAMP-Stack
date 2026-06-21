<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InterestedParty extends Model
{
    use HasUuids;

    protected $table = 'interested_parties';

    protected $fillable = [
        'ref_id', 'name', 'party_type', 'category', 'requirements', 'addressed_in_isms', 'notes', 'owner_id',
    ];

    protected function casts(): array
    {
        return ['addressed_in_isms' => 'boolean'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
