<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\JsonResources\JsonResource;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResource;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceWithFloat;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceWithHash;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceWithMethod;

#[CoversClass(JsonResource::class)]
class JsonResourceTest extends TestCase
{
    #[Test]
    public function responseIsCorrectlyFormattedForMake(): void
    {
        Route::get(
            'users/{id}',
            fn ($id) => UserResource::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
            ]);
    }

    #[Test]
    public function responseIsCorrectlyFormattedForCollection(): void
    {
        Route::get(
            'users',
            fn () => UserResource::collection(User::orderBy('id')->get())
        );

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

    #[Test]
    public function responseIsCorrectlyFormattedForPagination(): void
    {
        Route::get(
            'users',
            fn () => UserResource::collection(User::orderBy('id')->paginate())
        );

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

    #[Test]
    public function responseIsHasZeroFractionPreserved(): void
    {
        Route::get(
            'users',
            fn () => UserResourceWithFloat::collection(User::orderBy('id')->get())
        );

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

    #[Test]
    public function jsonResourceCanHaveDependencyInjected(): void
    {
        Route::get(
            'users',
            fn () => UserResourceWithHash::collection(User::orderBy('id')->get())
        );

        User::factory(2)->create();

        $data = $this->getJson('users')
            ->assertOk()
            ->json('data');

        $this->assertTrue(Hash::check($data[0]['id'], $data[0]['hash']));
        $this->assertTrue(Hash::check($data[1]['id'], $data[1]['hash']));
    }

    #[Test]
    public function eachResourceCanBeCalled(): void
    {
        Route::get(
            'users',
            fn () => UserResourceWithMethod::collection(User::orderBy('id')->get())
                ->each(fn (UserResourceWithMethod $resource) => $resource->capitalizeEmail())
        );

        [$first, $second] = User::factory(2)->create();

        $this->getJson('users')
            ->assertOk()
            ->assertJsonPath('data', [
                [
                    'id' => $first->id,
                    'email' => Str::upper($first->email),
                ],
                [
                    'id' => $second->id,
                    'email' => Str::upper($second->email),
                ],
            ]);
    }
}
