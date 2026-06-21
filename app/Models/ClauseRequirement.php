<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClauseRequirement extends Model
{
    use HasUuids;

    protected $fillable = [
        'clause', 'title', 'section', 'clause_number', 'requirement',
        'documented_info', 'conformity_status', 'implementation_notes', 'owner_id', 'review_date',
    ];

    protected function casts(): array
    {
        return ['clause_number' => 'integer', 'review_date' => 'date'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
