<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserResourceWithFloat extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sqrt_id' => round(sqrt($this->id), 4),
        ];
    }
}
