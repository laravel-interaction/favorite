<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Concerns;

use LaravelInteraction\Favorite\Favorite;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\TestCase;

/**
 * @internal
 */
final class FavoriterTest extends TestCase
{
    public function testFavorite(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->favorite($channel);
        $this->assertDatabaseHas(
            Favorite::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'favoriteable_type' => $channel->getMorphClass(),
                'favoriteable_id' => $channel->getKey(),
            ]
        );
        $user->load('favoriterFavorites');
        $user->unfavorite($channel);
        $user->load('favoriterFavorites');
        $user->favorite($channel);
    }

    public function testUnfavorite(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->favorite($channel);
        $this->assertDatabaseHas(
            Favorite::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'favoriteable_type' => $channel->getMorphClass(),
                'favoriteable_id' => $channel->getKey(),
            ]
        );
        $user->unfavorite($channel);
        $this->assertDatabaseMissing(
            Favorite::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'favoriteable_type' => $channel->getMorphClass(),
                'favoriteable_id' => $channel->getKey(),
            ]
        );
    }

    public function testToggleFavorite(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFavorite($channel);
        $this->assertDatabaseHas(
            Favorite::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'favoriteable_type' => $channel->getMorphClass(),
                'favoriteable_id' => $channel->getKey(),
            ]
        );
        $user->toggleFavorite($channel);
        $this->assertDatabaseMissing(
            Favorite::query()->getModel()->getTable(),
            [
                'user_id' => $user->getKey(),
                'favoriteable_type' => $channel->getMorphClass(),
                'favoriteable_id' => $channel->getKey(),
            ]
        );
    }

    public function testFavorites(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFavorite($channel);
        $this->assertSame(1, $user->favoriterFavorites()->count());
        $this->assertSame(1, $user->favoriterFavorites->count());
    }

    public function testHasFavorited(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFavorite($channel);
        $this->assertTrue($user->hasFavorited($channel));
        $user->toggleFavorite($channel);
        $user->load('favoriterFavorites');
        $this->assertFalse($user->hasFavorited($channel));
    }

    public function testHasNotFavorited(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->toggleFavorite($channel);
        $this->assertFalse($user->hasNotFavorited($channel));
        $user->toggleFavorite($channel);
        $this->assertTrue($user->hasNotFavorited($channel));
    }
}
