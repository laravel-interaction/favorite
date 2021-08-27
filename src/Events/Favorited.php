<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Events;

use Illuminate\Database\Eloquent\Model;

class Favorited
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $favorite;

    public function __construct(Model $favorite)
    {
        $this->favorite = $favorite;
    }
}
