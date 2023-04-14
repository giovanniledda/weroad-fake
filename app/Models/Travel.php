<?php

namespace App\Models;

use App\Enums\Mood;
use App\Observers\ProjectObserver;
use App\Traits\HasPublicUuids;
use function auth;
use function collect;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use function config;
use function now;
use const PERMISSION_SUPER_ADMIN;

class Travel extends Model
{
    use HasFactory, HasPublicUuids;

    protected $table = 'travels';

    protected $guarded = [
        'id',
        'slug',
        'nights',
    ];

    protected $hidden = [
        'id',
        'publicationDate',
    ];

    protected $casts = [
        'moods' => 'array',
    ];

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'travelId');
    }

    public function publish(): self
    {
        $this->update([
            'publicationDate' => now(),
        ]);

        return $this;
    }

    public function unpublish(): self
    {
        $this->update([
            'publicationDate' => null,
        ]);

        return $this;
    }

    /**
     * Interact with the travel's publicationDate.
     */
    protected function isPublic(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => ! is_null($attributes['publicationDate']),
        );
    }

    public function scopePublic(Builder $builder): void
    {
        $builder->whereNotNull('publicationDate');
    }

    public function scopePrivate(Builder $builder): void
    {
        $builder->whereNull('publicationDate');
    }

    public function initializeMoods(): self
    {
        $this->update([
            'moods' => [
                'moods' => collect(Mood::cases())->flatMap(function (Mood $mood) {
                    return [$mood->value => 0];
                })->toArray(),
            ],
        ]);

        return $this;
    }

    public function updateMood(Mood $moodToUpdate, int $value): self
    {
        $oldMoods = $this->moods['moods'];

        $this->update([
            'moods' => [
                'moods' => collect(Mood::cases())->flatMap(function (Mood $mood) use ($oldMoods, $value, $moodToUpdate) {
                    return [$mood->value => ($mood->value == $moodToUpdate->value) ? $value : $oldMoods[$mood->value]];
                })->toArray(),
            ],
        ]);

        return $this;
    }

    public static function getPaginatedList()
    {
        return Travel::when(!auth()->check(), function ($builder) {
            $builder->public();
        })->orderBy('id')
            ->fastPaginate(config('app.page_size'));
    }
}
