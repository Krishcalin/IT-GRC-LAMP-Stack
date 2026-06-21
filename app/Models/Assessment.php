<?php

namespace App\Models;

use App\Support\Scoring;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Assessment extends Model
{
    use HasUuids;

    protected $fillable = [
        'ref_id', 'title', 'description', 'assessment_type', 'framework',
        'supplier_id', 'owner_id', 'status', 'due_date',
    ];

    protected $appends = ['score', 'avg_maturity', 'answered_count', 'item_count'];

    protected function casts(): array
    {
        return ['due_date' => 'date'];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(AssessmentItem::class, 'assessment_id')->orderBy('ref_id');
    }

    protected function itemCount(): Attribute
    {
        return Attribute::get(fn () => $this->items->count());
    }

    protected function answeredCount(): Attribute
    {
        return Attribute::get(fn () => $this->items->filter(
            fn ($i) => $i->maturity !== null || ! empty($i->result)
        )->count());
    }

    protected function avgMaturity(): Attribute
    {
        return Attribute::get(function () {
            $mats = $this->items->pluck('maturity')->filter(fn ($m) => $m !== null);
            return $mats->isNotEmpty() ? round($mats->avg(), 1) : null;
        });
    }

    protected function score(): Attribute
    {
        return Attribute::get(fn () => Scoring::aggregateScore(
            $this->items->pluck('maturity')->all(),
            $this->items->pluck('result')->all(),
        ));
    }
}
