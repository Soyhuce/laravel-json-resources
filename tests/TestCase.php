<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\JsonResources\JsonResourcesServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        Factory::guessFactoryNamesUsing(fn (string $modelName) => $modelName . 'Factory');

        $this->withoutExceptionHandling();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     * @return array<string>
     */
    protected function getPackageProviders($app): array
    {
        return [
            JsonResourcesServiceProvider::class,
        ];
    }
}
