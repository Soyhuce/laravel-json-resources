<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;
use Illuminate\Contracts\Support\Arrayable;
use JsonSerializable;
use function call_user_func;

/**
 * @template T
 */
class AnonymousResource extends JsonResource
{
    protected Closure $formatUsing;

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

    public static function collection($resource): AnonymousCollection
    {
        return new AnonymousCollection($resource, EmptyAnonymousResource::class);
    }

    /**
     * @return array<array-key, mixed>|\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|JsonSerializable
     */
    public function format(): array|Arrayable|JsonSerializable
    {
        if ($this->resource === null) {
            return [];
        }

        return call_user_func($this->formatUsing, $this->resource);
    }
}
