<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Soyhuce\JsonResources\AnonymousResource;
use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserWithNameResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'extra' => new AnonymousResource(
                $this->name,
                fn (string $name): array => [
                    'name' => $name,
                ]
            ),
        ];
    }
}
