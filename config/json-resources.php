<?php declare(strict_types=1);

return [
    /*
     * Enable this will cause the package to throw an exception each time a query is performed in JsonResource serialization.
     * This is quite useful for development and testing but you probably don't want to use it in production.
     * Indeed, it's better to provide the service slowly instead of not providing it at all !
     */
    'forbid-database-queries' => (bool) env('JSON_RESOURCE_FORBID_QUERIES', false),
];
