<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Concerns;

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
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavorites(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame(1, $model->favoriteableFavorites()->count());
        $this->assertSame(1, $model->favoriteableFavorites->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoritersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame(1, $model->favoritersCount());
        $user->unfavorite($model);
        $this->assertSame(1, $model->favoritersCount());
        $model->loadCount('favoriters');
        $this->assertSame(0, $model->favoritersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoritersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame('1', $model->favoritersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testIsFavoritedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertFalse($model->isFavoritedBy($model));
        $user->favorite($model);
        $this->assertTrue($model->isFavoritedBy($user));
        $model->load('favoriters');
        $user->unfavorite($model);
        $this->assertTrue($model->isFavoritedBy($user));
        $model->load('favoriters');
        $this->assertFalse($model->isFavoritedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testIsNotFavoritedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $this->assertTrue($model->isNotFavoritedBy($model));
        $user->favorite($model);
        $this->assertFalse($model->isNotFavoritedBy($user));
        $model->load('favoriters');
        $user->unfavorite($model);
        $this->assertFalse($model->isNotFavoritedBy($user));
        $model->load('favoriters');
        $this->assertTrue($model->isNotFavoritedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testFavoriters(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame(1, $model->favoriters()->count());
        $user->unfavorite($model);
        $this->assertSame(0, $model->favoriters()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereFavoritedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame(1, $modelClass::query()->whereFavoritedBy($user)->count());
        $this->assertSame(0, $modelClass::query()->whereFavoritedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Favorite\Tests\Models\User|\LaravelInteraction\Favorite\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotFavoritedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->favorite($model);
        $this->assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotFavoritedBy($user)->count()
        );
        $this->assertSame($modelClass::query()->count(), $modelClass::query()->whereNotFavoritedBy($other)->count());
    }
}
