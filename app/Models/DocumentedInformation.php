<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentedInformation extends Model
{
    use HasUuids;

    protected $table = 'documented_information';

    protected $fillable = [
        'ref_id', 'title', 'description', 'doc_type', 'clause_ref', 'mandatory',
        'version', 'status', 'classification', 'location', 'owner_id', 'approver_id',
        'approved_at', 'review_date', 'next_review_date',
    ];

    protected function casts(): array
    {
        return [
            'mandatory' => 'boolean',
            'approved_at' => 'datetime',
            'review_date' => 'date',
            'next_review_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
