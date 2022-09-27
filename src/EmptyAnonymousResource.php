<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;

/**
 * @template T
 * @extends AnonymousResource<T>
 */
class EmptyAnonymousResource extends AnonymousResource
{
    /**
     * @phpstan-param T|null $resource
     * @param mixed $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
    }

    /**
     * @param \Closure(T): array<string, mixed> $formatUsing
     */
    public function using(Closure $formatUsing): static
    {
        $this->formatUsing = $formatUsing;

        return $this;
    }
}
