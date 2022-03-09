<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use BadMethodCallException;
use Closure;
use function call_user_func;

/**
 * @template T
 */
class AnonymousResource extends JsonResource
{
    private Closure $formatUsing;

    /**
     * @param mixed $resource
     * @phpstan-param T|null $resource
     * @phpstan-param \Closure(T): array<string, mixed> $formatUsing
     */
    public function __construct($resource, ?Closure $formatUsing = null)
    {
        parent::__construct($resource);

        $this->formatUsing = $formatUsing ?? fn ($result) => $result;
    }

    public static function collection($resource): void
    {
        throw new BadMethodCallException('collection on AnonymousResource is not supported.');
    }

    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        if ($this->resource === null) {
            return [];
        }

        return call_user_func($this->formatUsing, $this->resource);
    }
}
