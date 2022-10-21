<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests;

use ErrorException;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDeprecationHandling;
use Orchestra\Testbench\TestCase as Orchestra;
use Soyhuce\JsonResources\JsonResourcesServiceProvider;

/**
 * @coversNothing
 */
class TestCase extends Orchestra
{
    use InteractsWithDeprecationHandling;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        Factory::guessFactoryNamesUsing(fn (string $modelName) => $modelName . 'Factory');

        $this->withoutExceptionHandling();
        $this->withoutDeprecationHandling();
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

    protected function withoutDeprecationHandling(): static
    {
        if ($this->originalDeprecationHandler == null) {
            $this->originalDeprecationHandler = set_error_handler(function ($level, $message, $file = '', $line = 0) {
                if (in_array($level, [E_DEPRECATED, E_USER_DEPRECATED]) || (error_reporting() & $level)) {
                    // Ignore fakerphp deprecations
                    if (str_starts_with($file, realpath(__DIR__ . '/../vendor/fakerphp'))) {
                        return;
                    }
                    throw new ErrorException($message, 0, $level, $file, $line);
                }
            });
        }

        return $this;
    }
}
