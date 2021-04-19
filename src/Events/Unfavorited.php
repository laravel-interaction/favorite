<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Events;

use Illuminate\Database\Eloquent\Model;

class Unfavorited
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $favorite;

    /**
     * Liked constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $favorite
     */
    public function __construct(Model $favorite)
    {
        $this->favorite = $favorite;
    }
}
