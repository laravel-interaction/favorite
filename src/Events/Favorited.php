<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Events;

use Illuminate\Database\Eloquent\Model;

class Favorited
{
    public function __construct(public Model $model)
    {
    }
}
