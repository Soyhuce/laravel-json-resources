<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Testing\TestResponse;
use Soyhuce\JsonResources\Testing\TestResponseMixin;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class JsonResourcesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('json-resources')
            ->hasConfigFile();
    }

    public function bootingPackage(): void
    {
        JsonResources::preventDatabaseQueries((bool) config('json-resource.forbid-database-queries'));
        JsonResources::addClassHeader((bool) app()->environment('local', 'testing'));

        if (app()->environment('local', 'testing')) {
            TestResponse::mixin(new TestResponseMixin());
        }
    }
}
