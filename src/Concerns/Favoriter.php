<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Favorite\Favorite;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Favorite\Favorite[] $favoriterFavorites
 * @property-read int|null $favoriter_favorites_count
 */
trait Favoriter
{
    public function hasNotFavorited(Model $object): bool
    {
        return ! $this->hasFavorited($object);
    }

    public function hasFavorited(Model $object): bool
    {
        return ($this->relationLoaded(
            'favoriterFavorites'
        ) ? $this->favoriterFavorites : $this->favoriterFavorites())
            ->where('favoriteable_id', $object->getKey())
            ->where('favoriteable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function favorite(Model $object): Favorite
    {
        $attributes = [
            'favoriteable_id' => $object->getKey(),
            'favoriteable_type' => $object->getMorphClass(),
        ];

        return $this->favoriterFavorites()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $favoriterFavoritesLoaded = $this->relationLoaded('favoriterFavorites');
                if ($favoriterFavoritesLoaded) {
                    $this->unsetRelation('favoriterFavorites');
                }

                return $this->favoriterFavorites()
                    ->create($attributes);
            });
    }

    public function favoriterFavorites(): HasMany
    {
        return $this->hasMany(
            config('favorite.models.favorite'),
            config('favorite.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    /**
     * @return bool|\LaravelInteraction\Favorite\Favorite
     */
    public function toggleFavorite(Model $object)
    {
        return $this->hasFavorited($object) ? $this->unfavorite($object) : $this->favorite($object);
    }

    public function unfavorite(Model $object): bool
    {
        $hasNotFavorited = $this->hasNotFavorited($object);
        if ($hasNotFavorited) {
            return true;
        }

        $favoriterFavoritesLoaded = $this->relationLoaded('favoriterFavorites');
        if ($favoriterFavoritesLoaded) {
            $this->unsetRelation('favoriterFavorites');
        }

        return (bool) $this->favoritedItems(\get_class($object))
            ->detach($object->getKey());
    }

    protected function favoritedItems(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'favoriteable',
            config('favorite.models.favorite'),
            config('favorite.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
