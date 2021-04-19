<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Favorite\Favorite;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;

class FavoriteTest extends TestCase
{
    /**
     * @var \LaravelInteraction\Favorite\Tests\Models\User
     */
    protected $user;

    /**
     * @var \LaravelInteraction\Favorite\Tests\Models\Channel
     */
    protected $channel;

    /**
     * @var \LaravelInteraction\Favorite\Favorite
     */
    protected $favorite;

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
        self::assertInstanceOf(Carbon::class, $this->favorite->created_at);
        self::assertInstanceOf(Carbon::class, $this->favorite->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Favorite::query()->withType(Channel::class)->count());
        self::assertSame(0, Favorite::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('favorite.table_names.favorites'), $this->favorite->getTable());
    }

    public function testFavoriter(): void
    {
        self::assertInstanceOf(User::class, $this->favorite->favoriter);
    }

    public function testFavoriteable(): void
    {
        self::assertInstanceOf(Channel::class, $this->favorite->favoriteable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->favorite->user);
    }

    public function testIsFavoritedTo(): void
    {
        self::assertTrue($this->favorite->isFavoritedTo($this->channel));
        self::assertFalse($this->favorite->isFavoritedTo($this->user));
    }

    public function testIsFavoritedBy(): void
    {
        self::assertFalse($this->favorite->isFavoritedBy($this->channel));
        self::assertTrue($this->favorite->isFavoritedBy($this->user));
    }
}
