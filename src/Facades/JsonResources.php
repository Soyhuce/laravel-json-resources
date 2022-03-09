<?php

namespace Soyhuce\JsonResources\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Soyhuce\JsonResources\JsonResources
 */
class JsonResources extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-json-resources';
    }
}
