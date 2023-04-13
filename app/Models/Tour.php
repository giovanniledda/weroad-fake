<?php

namespace App\Models;

use App\Traits\HasPublicUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tour extends Model
{
    use HasFactory, HasPublicUuids;

    protected $table = 'tours';

    protected $casts = [
//        'startingDate' => 'date:Y-m-d',
//        'endingDate' => 'date:Y-m-d',
    ];

    protected $guarded = [
        'id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'id',
    ];

    public function travel(): BelongsTo
    {
        return $this->belongsTo(Travel::class, 'travelId');
    }

    /**
     * Interact with tour's price.
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => $value / 100,
            set: fn (string $value) => $value * 100,
        );
    }

    public function scopeByTravelSlug(Builder $builder, string $slug): void
    {
        $builder->whereRelation('travel', 'slug', $slug);
    }

    public function scopeByPrice(Builder $builder, int $start, int $end): void
    {
        $builder->whereBetween('price', [$start * 100, $end * 100]);
    }

    public function scopeByStartingDate(Builder $builder, string $from, string $to): void
    {
        $builder->whereBetween('startingDate', [$from, $to]);
    }
}
