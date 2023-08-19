<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Configuration;

use LaravelInteraction\Favorite\Favorite;
use LaravelInteraction\Favorite\Tests\Models\Channel;
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\TestCase;

/**
 * @internal
 */
final class UuidsTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        parent::getEnvironmentSetUp($app);

        config([
            'favorite.uuids' => true,
        ]);
    }

    public function testKeyType(): void
    {
        $favorite = new Favorite();
        $this->assertSame('string', $favorite->getKeyType());
    }

    public function testIncrementing(): void
    {
        $favorite = new Favorite();
        $this->assertFalse($favorite->getIncrementing());
    }

    public function testKeyName(): void
    {
        $favorite = new Favorite();
        $this->assertSame('uuid', $favorite->getKeyName());
    }

    public function testKey(): void
    {
        $user = User::query()->create();
        $channel = Channel::query()->create();
        $user->favorite($channel);
        $this->assertIsString($user->favoriterFavorites()->firstOrFail()->getKey());
    }
}
