<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\ResourceCollection;

class UserResourceCollectionWithFloat extends ResourceCollection
{
    public function format(): array
    {
        return $this->collection
            ->map(fn (User $user) => [
                'id' => $user->id,
                'sqrt_id' => round(sqrt($user->id), 4),
            ])
            ->toArray();
    }
}
