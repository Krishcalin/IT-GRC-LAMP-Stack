<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoaEntry extends Model
{
    use HasUuids;

    protected $table = 'soa_entries';

    protected $fillable = [
        'control_id', 'applicable', 'justification', 'implementation_status',
        'implementation_evidence', 'responsible_id', 'notes',
    ];

    protected function casts(): array
    {
        return ['applicable' => 'boolean'];
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'control_id');
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
