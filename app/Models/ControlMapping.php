<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlMapping extends Model
{
    use HasUuids;

    protected $fillable = ['source_control_id', 'target_control_id', 'relationship_type', 'note'];

    public function sourceControl(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'source_control_id');
    }

    public function targetControl(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'target_control_id');
    }
}
