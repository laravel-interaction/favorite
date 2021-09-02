<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Favorite\Favorite[] $favoriteableFavorites
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Favorite\Concerns\Favoriter[] $favoriters
 * @property-read string|int|null $favoriters_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereFavoritedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotFavoritedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Favoriteable
{
    public function isNotFavoritedBy(Model $user): bool
    {
        return ! $this->isFavoritedBy($user);
    }

    public function isFavoritedBy(Model $user): bool
    {
        if (! is_a($user, config('favorite.models.user'))) {
            return false;
        }

        $favoritersLoaded = $this->relationLoaded('favoriters');

        if ($favoritersLoaded) {
            return $this->favoriters->contains($user);
        }

        return ($this->relationLoaded(
            'favoriteableFavorites'
        ) ? $this->favoriteableFavorites : $this->favoriteableFavorites())
            ->where(config('favorite.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function scopeWhereNotFavoritedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'favoriters',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereFavoritedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'favoriters',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function favoriteableFavorites(): MorphMany
    {
        return $this->morphMany(config('favorite.models.favorite'), 'favoriteable');
    }

    public function favoriters(): BelongsToMany
    {
        return $this->morphToMany(
            config('favorite.models.user'),
            'favoriteable',
            config('favorite.models.favorite'),
            null,
            config('favorite.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function favoritersCount(): int
    {
        if ($this->favoriters_count !== null) {
            return (int) $this->favoriters_count;
        }

        $this->loadCount('favoriters');

        return (int) $this->favoriters_count;
    }

    public function favoritersCountForHumans($precision = 1, $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        return Interaction::numberForHumans(
            $this->favoritersCount(),
            $precision,
            $mode,
            $divisors ?? config('favorite.divisors')
        );
    }
}
