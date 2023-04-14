<?php

namespace App\Models;

use App\Traits\HasPublicUuids;
use Illuminate\Support\Carbon;
use function define;
use function defined;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeByPrice(Builder $builder, ?int $start = null, ?int $end = null): void
    {
        $builder->when(! empty($start), function ($builder) use ($start) {
            $builder->where('price', '>=', $start * 100);
        })->when(! empty($end), function ($builder) use ($end) {
            $builder->where('price', '<=', $end * 100);
        });
    }

    public function scopeByStartingDate(Builder $builder, ?string $from = null, ?string $to = null): void
    {
        $builder->when(! empty($from), function ($builder) use ($from) {
            $builder->where('startingDate', '>=', $from);
        })->when(! empty($to), function ($builder) use ($to) {
            $builder->where('startingDate', '<=', $to);
        });
    }

    public function getCalculatedEndingDate(): string
    {
        return Carbon::createFromFormat('Y-m-d', $this->startingDate)->addDays($this->travel->days)->format('Y-m-d');
    }
}
