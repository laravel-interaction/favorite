<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Favorite\Favorite;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;

/**
 * @internal
 */
final class FavoriteTest extends TestCase
{
    private User $user;

    private Channel $channel;

    private Favorite $favorite;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->favorite($this->channel);
        $this->favorite = Favorite::query()->firstOrFail();
    }

    public function testFavoritesTimestamp(): void
    {
        $this->assertInstanceOf(Carbon::class, $this->favorite->created_at);
        $this->assertInstanceOf(Carbon::class, $this->favorite->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Favorite::query()->withType(Channel::class)->count());
        $this->assertSame(0, Favorite::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('favorite.table_names.pivot'), $this->favorite->getTable());
    }

    public function testFavoriter(): void
    {
        $this->assertInstanceOf(User::class, $this->favorite->favoriter);
    }

    public function testFavoriteable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->favorite->favoriteable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->favorite->user);
    }

    public function testIsFavoritedTo(): void
    {
        $this->assertTrue($this->favorite->isFavoritedTo($this->channel));
        $this->assertFalse($this->favorite->isFavoritedTo($this->user));
    }

    public function testIsFavoritedBy(): void
    {
        $this->assertFalse($this->favorite->isFavoritedBy($this->channel));
        $this->assertTrue($this->favorite->isFavoritedBy($this->user));
    }
}
