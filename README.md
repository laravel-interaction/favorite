# Laravel Favorite

User favorite/unfavorite behaviour for Laravel.

<p align="center">
<a href="https://packagist.org/packages/laravel-interaction/favorite"><img src="https://poser.pugx.org/laravel-interaction/favorite/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/favorite"><img src="https://poser.pugx.org/laravel-interaction/favorite/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-interaction/favorite"><img src="https://poser.pugx.org/laravel-interaction/favorite/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/favorite"><img src="https://poser.pugx.org/laravel-interaction/favorite/license" alt="License"></a>
</p>

> **Requires [PHP 7.3+](https://php.net/releases/)**

Require Laravel Favorite using [Composer](https://getcomposer.org):

```bash
composer require laravel-interaction/favorite
```

## Usage

### Setup Favoriter

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Favorite\Concerns\Favoriter;

class User extends Model
{
    use Favoriter;
}
```

### Setup Favoriteable

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Favorite\Concerns\Favoriteable;

class Channel extends Model
{
    use Favoriteable;
}
```

### Favoriter

```php
use LaravelInteraction\Favorite\Tests\Models\Channel;
/** @var \LaravelInteraction\Favorite\Tests\Models\User $user */
/** @var \LaravelInteraction\Favorite\Tests\Models\Channel $channel */
// Favorite to Favoriteable
$user->favorite($channel);
$user->unfavorite($channel);
$user->toggleFavorite($channel);

// Compare Favoriteable
$user->hasFavorited($channel);
$user->hasNotFavorited($channel);

// Get favorited info
$user->favoriterFavorites()->count(); 

// with type
$user->favoriterFavorites()->withType(Channel::class)->count(); 

// get favorited channels
Channel::query()->whereFavoritedBy($user)->get();

// get favorited channels doesnt favorited
Channel::query()->whereNotFavoritedBy($user)->get();
```

### Favoriteable

```php
use LaravelInteraction\Favorite\Tests\Models\User;
use LaravelInteraction\Favorite\Tests\Models\Channel;
/** @var \LaravelInteraction\Favorite\Tests\Models\User $user */
/** @var \LaravelInteraction\Favorite\Tests\Models\Channel $channel */
// Compare Favoriter
$channel->isFavoritedBy($user); 
$channel->isNotFavoritedBy($user);
// Get favoriters info
$channel->favoriters->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('favoriters')->get();
$channels->each(function (Channel $channel){
    echo $channel->favoriters()->count(); // 1100
    echo $channel->favoriters_count; // "1100"
    echo $channel->favoritersCount(); // 1100
    echo $channel->favoritersCountForHumans(); // "1.1K"
});
```

### Events

| Event | Fired |
| --- | --- |
| `LaravelInteraction\Favorite\Events\Favorited` | When an object get favorited. |
| `LaravelInteraction\Favorite\Events\Unfavorited` | When an object get unfavorited. |

## License

Laravel Favorite is an open-sourced software licensed under the [MIT license](LICENSE).
