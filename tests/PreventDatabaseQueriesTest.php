<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Soyhuce\JsonResources\Exceptions\DatabaseQueryDetected;
use Soyhuce\JsonResources\JsonResources;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResource;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceCollectionPerformingQuery;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourcePerformingQueries;

#[CoversNothing]
class PreventDatabaseQueriesTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        JsonResources::preventDatabaseQueries();
    }

    #[Test]
    public function noQueryIsAllowedUsingMake(): void
    {
        Route::get(
            'users/{id}',
            fn ($id) => UserResourcePerformingQueries::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->expectException(DatabaseQueryDetected::class);
        $this->expectExceptionMessage(
            <<<message
            1 query detected in resource :
            select * from "users" where "id" = {$user->id} limit 1
            message
        );

        $this->getJson("users/{$user->id}");
    }

    #[Test]
    public function noQueryIsAllowedUsingCollection(): void
    {
        Route::get(
            'users',
            fn () => UserResourcePerformingQueries::collection(User::all())
        );

        [$first, $second] = User::factory(2)->create();

        $this->expectException(DatabaseQueryDetected::class);
        $this->expectExceptionMessage(
            <<<message
            2 queries detected in resource :
            select * from "users" where "id" = {$first->id} limit 1
            select * from "users" where "id" = {$second->id} limit 1
            message
        );

        $this->getJson('users');
    }

    #[Test]
    public function noQueryIsAllowedUsingPaginate(): void
    {
        Route::get(
            'users',
            fn () => UserResourcePerformingQueries::collection(User::paginate())
        );

        [$first, $second] = User::factory(2)->create();

        $this->expectException(DatabaseQueryDetected::class);
        $this->expectExceptionMessage(
            <<<message
            2 queries detected in resource :
            select * from "users" where "id" = {$first->id} limit 1
            select * from "users" where "id" = {$second->id} limit 1
            message
        );

        $this->getJson('users');
    }

    #[Test]
    public function noQueryIsAllowedUsingResourceCollection(): void
    {
        Route::get(
            'users',
            fn () => UserResourceCollectionPerformingQuery::make(User::all())
        );

        User::factory(2)->create();

        $this->expectException(DatabaseQueryDetected::class);
        $this->expectExceptionMessage(
            <<<'message'
            1 query detected in resource :
            select count(*) as aggregate from "users"
            message
        );

        $this->getJson('users');
    }

    #[Test]
    public function queryPreventionCanBeDisabled(): void
    {
        JsonResources::allowDatabaseQueries();

        Route::get(
            'users/{id}',
            fn ($id) => UserResourcePerformingQueries::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk();
    }

    #[Test]
    public function queryIsAllowedOutsideResource(): void
    {
        Route::get('users/{id}', function ($id) {
            $resource = UserResource::make(User::find($id));
            User::find($id);

            return $resource;
        });

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk();
    }

    #[Test]
    public function queryIsAllowedInTest(): void
    {
        Route::post('users/{email}', function ($email) {
            $user = User::create(['email' => $email]);

            return UserResource::make($user);
        });

        $this->postJson('users/some-value')
            ->assertCreated()
            ->assertJsonPath('data.email', 'some-value');

        $this->assertDatabaseHas('users', ['email' => 'some-value']);
    }

    #[Test]
    public function queryLogIsRespected(): void
    {
        DB::enableQueryLog();
        Route::get(
            'users/{id}',
            fn ($id) => UserResource::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->getJson("users/{$user->id}")
            ->assertOk();

        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $this->assertTrue(DB::logging());
        $this->assertCount(3, DB::getQueryLog());
    }

    #[Test]
    public function queryLogIsRespectedButCanFail(): void
    {
        DB::enableQueryLog();
        Route::get(
            'users/{id}',
            fn ($id) => UserResourcePerformingQueries::make(User::find($id))
        );

        $user = User::factory()->createOne();

        $this->expectException(DatabaseQueryDetected::class);
        $this->expectExceptionMessage(
            <<<message
            1 query detected in resource :
            select * from "users" where "id" = {$user->id} limit 1
            message
        );

        $this->getJson("users/{$user->id}");
    }
}
