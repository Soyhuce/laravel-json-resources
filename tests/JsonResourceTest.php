<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Route;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResource;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceWithFloat;

/**
 * @covers \Soyhuce\JsonResources\JsonResource
 */
class JsonResourceTest extends TestCase
{
    /**
     * @test
     */
    public function responseIsCorrectlyFormattedForMake(): void
    {
        Route::get('users/{id}', function ($id) {
            return UserResource::make(User::find($id));
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
    public function responseIsCorrectlyFormattedForCollection(): void
    {
        Route::get('users', function () {
            return UserResource::collection(User::orderBy('id')->get());
        });

        $first = User::factory()->createOne();
        $second = User::factory()->createOne();

        $this->getJson('users')
            ->assertOk()
            ->assertJsonPath('data', [
                [
                    'id' => $first->id,
                    'email' => $first->email,
                ],
                [
                    'id' => $second->id,
                    'email' => $second->email,
                ],
            ]);
    }

    /**
     * @test
     */
    public function responseIsCorrectlyFormattedForPagination(): void
    {
        Route::get('users', function () {
            return UserResource::collection(User::orderBy('id')->paginate());
        });

        $first = User::factory()->createOne();
        $second = User::factory()->createOne();

        $this->getJson('users')
            ->assertOk()
            ->assertJsonPath('data', [
                [
                    'id' => $first->id,
                    'email' => $first->email,
                ],
                [
                    'id' => $second->id,
                    'email' => $second->email,
                ],
            ])
            ->assertJsonStructure(['data', 'links', 'meta']);
    }

    /**
     * @test
     */
    public function responseIsHasZeroFractionPreserved(): void
    {
        Route::get('users', function () {
            return UserResourceWithFloat::collection(User::orderBy('id')->get());
        });

        $first = User::factory()->createOne();
        $second = User::factory()->createOne();

        $this->getJson('users')
            ->assertOk()
            ->assertJsonPath('data', [
                [
                    'id' => 1,
                    'sqrt_id' => 1.0,
                ],
                [
                    'id' => 2,
                    'sqrt_id' => 1.4142,
                ],
            ]);
    }
}
