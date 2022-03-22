# An opinionated JsonResource for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/soyhuce/laravel-json-resources.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-json-resources)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-json-resources/run-tests?label=tests)](https://github.com/soyhuce/laravel-json-resources/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-json-resources/Check%20&%20fix%20styling?label=code%20style)](https://github.com/soyhuce/laravel-json-resources/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![GitHub PHPStan Action Status](https://img.shields.io/github/workflow/status/soyhuce/laravel-json-resources/PHPStan?label=phpstan)](https://github.com/soyhuce/laravel-json-resources/actions?query=workflow%3APHPStan+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/soyhuce/laravel-json-resources.svg?style=flat-square)](https://packagist.org/packages/soyhuce/laravel-json-resources)

Don't ever run database queries in JsonResource serialization, without overhead in production !

## Installation

You can install the package via composer:

```bash
composer require soyhuce/laravel-json-resources
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="json-resources-config"
```

## Usage

This package provides a base class `Soyhuce\JsonResources\JsonResource`.

It gives a simplified interface for data serialization :

```php
class UserResource extends \Soyhuce\JsonResources\JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
```

It is still possible tu use `public function toArray($request): array` method.

A base class `\Soyhuce\JsonResources\ResourceCollection` est aussi disponible.

### Forbid to execute database queries

Using this base resource, you can forbid to execute database queries during serialization.

You just need to activate this option in `json-resources.forbid-database-queries` config.

This configuration is thought to be used in local environments but should be deactivated in production. It allows you to
tests that all required relations are correctly loaded (in feature tests for exemple) while limiting overhead in
production.

For exemple :

```php
class UserResource extends \Soyhuce\JsonResources\JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role->label,
        ];
    }
}

class UserController
{
    public function index(): \Illuminate\Http\Resources\Json\JsonResource
    {
        return UserResource::collection(User::all());
    }
}
```

A `DatabaseQueryDetected` exception will be raised with every executed request :

```txt
2 queries detected in resource :
select * from "roles" where "id" = 2
select * from "roles" where "id" = 1
```

### AnonymousResource

It could be useful to have anonymous resources for some cases.

For exemple if user's role is nullable, you could have :

```php
class UserResource extends \Soyhuce\JsonResources\JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'role' => $this->role !== null 
                ? [
                    'id' => $this->role->id,
                    'label' => $this->role->label,
                ]
                : null
        ];
    }
}
```

Via an `AnonymousResource`, you could do

```php
class UserResource extends \Soyhuce\JsonResources\JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'role' => new \Soyhuce\JsonResources\AnonymousResource(
                $this->role,
                fn (Role $role) => [
                    'id' => $this->role->id,
                    'label' => $this->role->label,
                ]
            )
        ];
    }
}
```

If the user has a role, you will get :

```json
{
  "data": {
    "id": 1,
    "role": {
      "id": 2,
      "label": "classic"
    }
  }
}
```

If the user doest not have a role, you will get :

```json
{
  "data": {
    "id": 1,
    "role": null
  }
}
```

### Returning anonymous resource from the controller

It is possible to return an anonymous resource from the controller. In cas the provided data is null, you won't get '
null' but an empty array `[]`.

```php
class SomeController
{
    public function show()
    {
        return \Soyhuce\JsonResources\AnonymousResource::make(
           $this->searchSomeItem(), // returns Item|null
           fn (Item $item) => [
               'label' => $item->label,
               'prop' => $item->prop 
           ]
        );
    }
}
```

If the item is found

```json
{
  "data": {
    "label": "the label",
    "prop": 14
  }
}
```

If the item is `null`

```json
{
  "data": []
}
```

> Note : Anonymous resource collections are not supported

#### AnonymousResource without format

It is possible to return an anonymous resource without callback. In this case, the resource will be returned as is.

```php
class SomeController
{
    public function show()
    {
        $foo = $this->fetchFoo();
        $bars = $this->fetchBars();
        
        return \Soyhuce\JsonResources\AnonymousResource::make([
           'foo' => FooResource::make($foo),
           'bars' => BarResource::collection($bars), 
       ]);
    }
}
```

Le json retourné aura alors la forme

```json
{
  "data": {
    "foo": {
      // some foo data
    },
    "bar": [
      {
        // some bar data
      },
      {
        // some bar data
      }
    ]
  }
}
```

### Json encoding

All returned jsons are encoded by default with `JSON_PRESERVE_ZERO_FRACTION` option.

### Feature tests

The `JsonResource` and `ResourceCollection` classes will add a `X-Json-Resource` header to the response when the
application is in `local` or `testing` environment.

This header will contain the actual resource class used to generate the response and can be used in functional tests to
verify which resource was used.

In order to test this, you cas use `TestResponse::assertJsonResource(TheResource::class)` method.

```php
use Soyhuce\JsonResources\Tests\Fixtures\User;

class SomeController
{
    public function index(): JsonResouce
    {
        return UserResource::collection(User::all());
    }
    
    public function show(User $user): JsonResource
    {
        return UserResource::make($user);
    }

}

class UserTest extends TestCase
{
    /** @test */
    public function indexUsesUserResource(): void
    {
        $this->getJson('users')
            ->assertOk()
            ->assertJsonResource(UserResource::class);
    }
    
    /** @test */
    public function showUsesUserResource(): void
    {
        $user = User:::factory()->createOne();
        
        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonResource(UserResource::class);
    }
}
```

### Unit tests

It can be useful to unit test the `JsonResource`. You can
use [soyhuce/laravel-testing](https://github.com/Soyhuce/laravel-testing) for this.

In this cas, you will probably need to allow database queries to be run. You can do this calling

```php
\Soyhuce\JsonResources\JsonResources::allowDatabaseQueries();
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Bastien Philippe](https://github.com/bastien-phi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
