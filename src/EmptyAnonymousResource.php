<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;
use function call_user_func;

/**
 * @template TType
 */
class EmptyAnonymousResource extends JsonResource
{
    /** @var \Closure(TType): mixed */
    protected Closure $formatUsing;

    /**
     * @param \Closure(TType): mixed $formatUsing
     */
    public function using(Closure $formatUsing): static
    {
        $this->formatUsing = $formatUsing;

        return $this;
    }

    public function format(): mixed
    {
        return call_user_func($this->formatUsing, $this->resource);
    }
}
