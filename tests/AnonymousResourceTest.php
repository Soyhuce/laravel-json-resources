<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\JsonResources\AnonymousResource;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserWithNameResource;

#[CoversClass(AnonymousResource::class)]
class AnonymousResourceTest extends TestCase
{
    #[Test]
    public function responseIsCorrectlyFormattedForMake(): void
    {
        Route::get(
            'users/{id}',
            fn ($id) => AnonymousResource::make(
                User::find($id),
                fn (User $user): array => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            )
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
    public function nullIsCorrectlyTreatedForRootResource(): void
    {
        Route::get(
            'users/{id}',
            fn ($id) => AnonymousResource::make(
                null,
                fn (User $user): array => [
                    'id' => $user->id,
                    'email' => $user->email,
                ]
            )
        );

        $this->getJson('users/1')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    #[Test]
    public function innerAnonymousIsCorrectlyTreated(): void
    {
        Route::get(
            'users/{id}',
            function ($id) {
                $user = User::find($id);
                $user->name = 'John Doe';

                return UserWithNameResource::make($user);
            }
        );

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

    #[Test]
    public function innerAnonymousWithNullIsCorrectlyTreated(): void
    {
        Route::get(
            'users/{id}',
            fn ($id) => UserWithNameResource::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk()
            ->assertJsonPath('data', [
                'id' => $user->id,
                'email' => $user->email,
                'extra' => null,
            ]);
    }

    #[Test]
    public function anonymousResourceCasUseAnonymousCollection(): void
    {
        Route::get(
            'users',
            fn () => AnonymousResource::collection(User::orderBy('id')->get())
                ->using(fn (User $user) => [
                    'id' => $user->id,
                    'email' => $user->email,
                ])
        );

        [$first, $second] = User::factory(2)->create();

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

    #[Test]
    public function resourcePreservesZeroFraction(): void
    {
        Route::get(
            'test',
            fn () => AnonymousResource::make([
                'one_half' => 0.5,
                'one' => 1.0,
            ])
        );

        $this->getJson('test')
            ->assertOk()
            ->assertJsonPath('data', [
                'one_half' => 0.5,
                'one' => 1.0,
            ]);
    }
}
