<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evidence extends Model
{
    use HasUuids;

    protected $table = 'evidence';

    protected $fillable = [
        'title', 'description', 'file_name', 'file_path', 'file_type', 'file_size',
        'uploaded_by', 'control_id', 'risk_id', 'audit_id', 'policy_id',
    ];

    protected function casts(): array
    {
        return ['file_size' => 'integer'];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function control(): BelongsTo
    {
        return $this->belongsTo(Control::class, 'control_id');
    }

    public function risk(): BelongsTo
    {
        return $this->belongsTo(Risk::class, 'risk_id');
    }

    public function audit(): BelongsTo
    {
        return $this->belongsTo(Audit::class, 'audit_id');
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class, 'policy_id');
    }
}
