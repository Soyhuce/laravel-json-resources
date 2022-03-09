<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Testing;

use Closure;

/**
 * @mixin \Illuminate\Testing\TestResponse
 */
class TestResponseMixin
{
    public function assertJsonResource(): Closure
    {
        return function (string $resource): self {
            $this->assertHeader('X-Json-Resource', $resource);

            return $this;
        };
    }
}
