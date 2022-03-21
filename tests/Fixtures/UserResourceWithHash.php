<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Illuminate\Contracts\Hashing\Hasher;
use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserResourceWithHash extends JsonResource
{
    public function format(Hasher $hash): array
    {
        return [
            'id' => $this->id,
            'hash' => $hash->make($this->id),
        ];
    }
}
