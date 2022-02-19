<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Favorite\Events\Favorited;
use LaravelInteraction\Favorite\Events\Unfavorited;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $favoriter
 * @property \Illuminate\Database\Eloquent\Model $favoriteable
 *
 * @method static \LaravelInteraction\Favorite\Favorite|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Favorite\Favorite|\Illuminate\Database\Eloquent\Builder query()
 */
class Favorite extends MorphPivot
{
    /**
     * @var array<string, class-string<\LaravelInteraction\Favorite\Events\Favorited>>|array<string, class-string<\LaravelInteraction\Favorite\Events\Unfavorited>>
     */
    protected $dispatchesEvents = [
        'created' => Favorited::class,
        'deleted' => Unfavorited::class,
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    /**
     * @var bool
     */
    public $incrementing = true;

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return false;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable(): string
    {
        return config('favorite.table_names.favorites') ?: parent::getTable();
    }

    public function isFavoritedBy(Model $user): bool
    {
        return $user->is($this->favoriter);
    }

    public function isFavoritedTo(Model $object): bool
    {
        return $object->is($this->favoriteable);
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('favoriteable_type', app($type)->getMorphClass());
    }

    public function favoriteable(): MorphTo
    {
        return $this->morphTo();
    }

    public function favoriter(): BelongsTo
    {
        return $this->user();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('favorite.models.user'), config('favorite.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('favorite.uuids');
    }
}
