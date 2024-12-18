<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;

/**
 * @template TResource of \Soyhuce\JsonResources\EmptyAnonymousResource
 * @template TKey of array-key
 * @template TType
 * @extends \Soyhuce\JsonResources\AnonymousResourceCollection<TKey, TResource<TType>>
 */
class AnonymousCollection extends AnonymousResourceCollection
{
    /**
     * @param Closure(TType): mixed $formatUsing
     * @return $this
     */
    public function using(Closure $formatUsing): static
    {
        $this->each(fn (EmptyAnonymousResource $resource) => $resource->using($formatUsing));

        return $this;
    }
}
