<?php

declare(strict_types=1);

namespace LaravelInteraction\Favorite\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Favorite\Concerns\Favoriteable;
use LaravelInteraction\Favorite\Concerns\Favoriter;

/**
 * @method static \LaravelInteraction\Favorite\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Favoriter;
    use Favoriteable;
}
