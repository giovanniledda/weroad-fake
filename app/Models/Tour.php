<?php

namespace App\Models;

use App\Traits\HasPublicUuids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use function define;
use function defined;
use function is_null;

defined('ENOUGH_HUGE_PRICE') or define('ENOUGH_HUGE_PRICE', 100000000000000000);

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
        $calculatedStart = !is_null($start) ? ($start * 100) : 0;

        $calculatedEnd = !is_null($end) ? ($end * 100) : ENOUGH_HUGE_PRICE;

        $builder->whereBetween('price', [$calculatedStart, $calculatedEnd]);
    }

    public function scopeByStartingDate(Builder $builder, string $from, string $to): void
    {
        if (!is_null($to)) {
            $builder->where('startingDate', '<=', $to);
        }

        if (!is_null($from)) {
            $builder->where('startingDate', '>=', $from);
        }
        
//        $builder->whereBetween('startingDate', [$from, $to]);
    }
}
