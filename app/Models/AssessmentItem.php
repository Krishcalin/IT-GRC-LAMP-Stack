<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'assessment_id', 'control_id', 'question', 'response', 'maturity', 'result', 'comment',
    ];

    protected function casts(): array
    {
        return ['maturity' => 'integer'];
    }

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'control_id');
    }
}
