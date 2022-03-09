<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Route;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResource;

/**
 * @coversNothing
 */
class ResourceClassHeaderTest extends TestCase
{
    /**
     * @test
     */
    public function responseCanAssertUsedResource(): void
    {
        Route::get('users/{id}', function ($id) {
            return UserResource::make(User::find($id));
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertJsonResource(UserResource::class);
    }

    /**
     * @test
     */
    public function responseCanAssertUsedResourceOnCollection(): void
    {
        Route::get('users', function () {
            return UserResource::collection(User::orderBy('id')->get());
        });

        User::factory(2)->create();

        $this->getJson('users')
            ->assertJsonResource(UserResource::class);
    }
}
