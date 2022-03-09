<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\ResourceCollection;

class UserResourceCollectionPerformingQuery extends ResourceCollection
{
    public function format(): array
    {
        return [
            'users' => UserResource::collection($this->resource),
            'count' => User::count(),
        ];
    }
}
