<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;

class AnonymousCollection extends AnonymousResourceCollection
{
    public function using(Closure $formatUsing): static
    {
        $this->each(fn (EmptyAnonymousResource $resource) => $resource->using($formatUsing));

        return $this;
    }
}
