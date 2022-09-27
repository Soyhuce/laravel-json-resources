<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;

/**
 * @template TResource of \Soyhuce\JsonResources\EmptyAnonymousResource
 * @template TType
 * @extends \Soyhuce\JsonResources\AnonymousResourceCollection<TResource<TType>>
 */
class AnonymousCollection extends AnonymousResourceCollection
{
    /**
     * @param \Closure(TType): array<string, mixed> $formatUsing
     * @return $this
     */
    public function using(Closure $formatUsing): static
    {
        $this->each(fn (EmptyAnonymousResource $resource) => $resource->using($formatUsing));

        return $this;
    }
}
