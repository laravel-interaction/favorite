<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Favorite\Events\Favorited;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\TestCase;

/**
 * @internal
 */
final class FavoritedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Favorited::class]);
        $user->favorite($channel);
        Event::assertDispatchedTimes(Favorited::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Favorited::class]);
        $user->favorite($channel);
        $user->favorite($channel);
        $user->favorite($channel);
        Event::assertDispatchedTimes(Favorited::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Favorited::class]);
        $user->toggleFavorite($channel);
        Event::assertDispatchedTimes(Favorited::class);
    }
}
