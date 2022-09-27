<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection as IlluminateAnonymousResourceCollection;
use Soyhuce\JsonResources\Concerns\ConvertsToResponse;

/**
 * @template TResource
 * @property \Illuminate\Support\Collection<TResource> $resource
 * @property class-string<TResource> $collects
 */
class AnonymousResourceCollection extends IlluminateAnonymousResourceCollection
{
    use ConvertsToResponse;

    /**
     * @return class-string<TResource>
     */
    protected function resourceClass(): string
    {
        return $this->collects;
    }

    /**
     * @param callable(TResource): mixed $closure
     */
    public function each(callable $closure): static
    {
        $this->resource->each($closure);

        return $this;
    }
}
