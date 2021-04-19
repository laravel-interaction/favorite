<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Favorite\Concerns\Favoriteable;

/**
 * @method static \LaravelInteraction\Favorite\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Favoriteable;
}
