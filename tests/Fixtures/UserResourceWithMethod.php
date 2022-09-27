<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Tests\Fixtures;

use Illuminate\Support\Str;
use Soyhuce\JsonResources\JsonResource;

/**
 * @mixin \Soyhuce\JsonResources\Tests\Fixtures\User
 */
class UserResourceWithMethod extends JsonResource
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

    public function capitalizeEmail(): void
    {
        $this->resource->email = Str::upper($this->resource->email);
    }
}
