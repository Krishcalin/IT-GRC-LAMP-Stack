<?php

namespace App\Models;

use App\Support\Scoring;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Risk extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'category', 'likelihood', 'impact',
        'inherent_risk_level', 'treatment', 'treatment_plan',
        'residual_likelihood', 'residual_impact', 'residual_risk_level',
        'owner_id', 'status', 'review_date',
    ];

    protected function casts(): array
    {
        return [
            'likelihood' => 'integer',
            'impact' => 'integer',
            'residual_likelihood' => 'integer',
            'residual_impact' => 'integer',
            'review_date' => 'date',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function controls(): BelongsToMany
    {
        return $this->belongsToMany(Control::class, 'risk_controls');
    }

    /** Recompute inherent/residual levels from the 5x5 matrix. Call before save. */
    public function recalculateLevels(): void
    {
        $this->inherent_risk_level = Scoring::riskLevel($this->likelihood, $this->impact);
        if ($this->residual_likelihood && $this->residual_impact) {
            $this->residual_risk_level = Scoring::riskLevel($this->residual_likelihood, $this->residual_impact);
        } else {
            $this->residual_risk_level = null;
        }
    }
}
