<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Support\Facades\Route;
use Soyhuce\JsonResources\JsonResources;
use Soyhuce\JsonResources\Tests\Fixtures\User;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceCollection;
use Soyhuce\JsonResources\Tests\Fixtures\UserResourceCollectionWithFloat;

/**
 * @coversNothing
 */
class ResourceCollectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        JsonResources::preventDatabaseQueries();
    }

    /**
     * @test
     */
    public function responseIsCorrectlyFormattedForCollectionWithResourceDiscovery(): void
    {
        Route::get('users', fn () => UserResourceCollection::make(User::orderBy('id')->get()));

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
    public function responseIsCorrectlyFormattedForCollectionWithoutResourceDiscovery(): void
    {
        Route::get('users', fn () => UserResourceCollectionWithFloat::make(User::orderBy('id')->get()));

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
