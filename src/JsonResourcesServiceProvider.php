<?php

namespace Soyhuce\JsonResources;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Soyhuce\JsonResources\Commands\JsonResourcesCommand;

class JsonResourcesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-json-resources')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-json-resources_table')
            ->hasCommand(JsonResourcesCommand::class);
    }
}
