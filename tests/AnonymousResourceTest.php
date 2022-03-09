<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use BadMethodCallException;
use Illuminate\Support\Facades\Route;
use Soyhuce\JsonResources\AnonymousResource;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserWithNameResource;

/**
 * @covers \Soyhuce\JsonResources\AnonymousResource
 */
class AnonymousResourceTest extends TestCase
{
    /**
     * @test
     */
    public function responseIsCorrectlyFormattedForMake(): void
    {
        Route::get('users/{id}', function ($id) {
            return AnonymousResource::make(
                User::find($id),
                fn (User $user): array => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            );
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * @test
     */
    public function nullIsCorrectlyTreatedForRootResource(): void
    {
        Route::get('users/{id}', function ($id) {
            return AnonymousResource::make(
                null,
                fn (User $user): array => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            );
        });

        $this->getJson('users/1')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    /**
     * @test
     */
    public function innerAnonymousIsCorrectlyTreated(): void
    {
        Route::get('users/{id}', function ($id) {
            $user = User::find($id);
            $user->name = 'John Doe';

            return UserWithNameResource::make($user);
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
                'extra' => [
                    'name' => 'John Doe',
                ],
            ]);
    }

    /**
     * @test
     */
    public function innerAnonymousWithNullIsCorrectlyTreated(): void
    {
        Route::get('users/{id}', function ($id) {
            return UserWithNameResource::make(User::find($id));
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
                'extra' => null,
            ]);
    }

    /**
     * @test
     */
    public function anonymousResourceCannotBeUsedOnCollections(): void
    {
        Route::get('users', function () {
            return AnonymousResource::collection(User::orderBy('id')->get());
        });

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('collection on AnonymousResource is not supported.');

        $this->getJson('users');
    }

    /**
     * @test
     */
    public function formatterIsNullable(): void
    {
        Route::get('users/{id}', function ($id) {
            $user = User::findOrFail($id);

            return AnonymousResource::make([
                'id' => $user->id,
                'email' => $user->email,
            ]);
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    /**
     * @test
     */
    public function resourcePreservesZeroFraction(): void
    {
        Route::get('test', function () {
            return AnonymousResource::make([
                'one_half' => 0.5,
                'one' => 1.0,
            ]);
        });

        $this->getJson('test')
            ->assertOk()
            ->assertJsonPath('data', [
                'one_half' => 0.5,
                'one' => 1.0,
            ]);
    }
}
