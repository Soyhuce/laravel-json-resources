<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
        ];
    }
}
