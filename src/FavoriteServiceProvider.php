<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class FavoriteServiceProvider extends InteractionServiceProvider
{
    /**
     * @var string
     */
    protected $interaction = InteractionList::FAVORITE;
}
