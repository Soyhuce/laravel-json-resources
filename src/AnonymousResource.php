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
    /** @var \Closure(T): array<array-key, mixed> */
    protected Closure $formatUsing;

    /**
     * @param T|null $resource
     * @param \Closure(T): array<array-key, mixed>|null $formatUsing
     */
    public function __construct($resource, ?Closure $formatUsing = null)
    {
        parent::__construct($resource);

        $this->formatUsing = $formatUsing ?? fn ($result) => $result;
    }

    /**
     * @template TType
     * @param array<array-key, TType|null>|\Illuminate\Support\Collection<array-key, TType|null> $resource
     * @return \Soyhuce\JsonResources\AnonymousCollection<EmptyAnonymousResource<TType>, TType>
     */
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
