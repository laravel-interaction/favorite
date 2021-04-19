<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Events;

use Illuminate\Support\Facades\Event;
use LaravelInteraction\Favorite\Events\Unfavorited;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\TestCase;

class UnfavoritedTest extends TestCase
{
    public function testOnce(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->favorite($channel);
        Event::fake([Unfavorited::class]);
        $user->unfavorite($channel);
        Event::assertDispatchedTimes(Unfavorited::class);
    }

    public function testTimes(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->favorite($channel);
        Event::fake([Unfavorited::class]);
        $user->unfavorite($channel);
        $user->unfavorite($channel);
        Event::assertDispatchedTimes(Unfavorited::class);
    }

    public function testToggle(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        Event::fake([Unfavorited::class]);
        $user->toggleFavorite($channel);
        $user->toggleFavorite($channel);
        Event::assertDispatchedTimes(Unfavorited::class);
    }
}
