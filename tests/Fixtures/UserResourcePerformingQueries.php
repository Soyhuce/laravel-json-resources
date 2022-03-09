<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserResourcePerformingQueries extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->fresh()->id,
        ];
    }
}
