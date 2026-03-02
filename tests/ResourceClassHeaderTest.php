<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResource;

#[CoversNothing]
class ResourceClassHeaderTest extends TestCase
{
    #[Test]
    public function responseCanAssertUsedResource(): void
    {
        Route::get('users/{id}', fn ($id) => UserResource::make(User::find($id)));

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertJsonResource(UserResource::class);
    }

    #[Test]
    public function responseCanAssertUsedResourceOnCollection(): void
    {
        Route::get('users', fn () => UserResource::collection(User::orderBy('id')->get()));

        User::factory(2)->create();

        $this->getJson('users')
            ->assertJsonResource(UserResource::class);
    }
}
