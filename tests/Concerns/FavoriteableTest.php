<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Concerns;

use Iterator;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\TestCase;

/**
 * @internal
 */
final class FavoriteableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Favorite\Tests\Models\Channel|\LaravelInteraction\Favorite\Tests\Models\User>>>
     */
    public function provideModelClasses(): Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavorites($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame(1, $model->favoriteableFavorites()->count());
        self::assertSame(1, $model->favoriteableFavorites->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoritersCount($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame(1, $model->favoritersCount());
        $user->unfavorite($model);
        self::assertSame(1, $model->favoritersCount());
        $model->loadCount('favoriters');
        self::assertSame(0, $model->favoritersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoritersCountForHumans($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame('1', $model->favoritersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testIsFavoritedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isFavoritedBy($model));
        $user->favorite($model);
        self::assertTrue($model->isFavoritedBy($user));
        $model->load('favoriters');
        $user->unfavorite($model);
        self::assertTrue($model->isFavoritedBy($user));
        $model->load('favoriters');
        self::assertFalse($model->isFavoritedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testIsNotFavoritedBy($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotFavoritedBy($model));
        $user->favorite($model);
        self::assertFalse($model->isNotFavoritedBy($user));
        $model->load('favoriters');
        $user->unfavorite($model);
        self::assertFalse($model->isNotFavoritedBy($user));
        $model->load('favoriters');
        self::assertTrue($model->isNotFavoritedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoriters($modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame(1, $model->favoriters()->count());
        $user->unfavorite($model);
        self::assertSame(0, $model->favoriters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereFavoritedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame(1, $modelClass::query()->whereFavoritedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereFavoritedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotFavoritedBy($modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotFavoritedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotFavoritedBy($other)->count());
    }
}
