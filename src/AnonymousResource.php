<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Closure;
use function call_user_func;

/**
 * @template TType
 */
class AnonymousResource extends JsonResource
{
    /** @var Closure(TType): mixed */
    protected Closure $formatUsing;

    /**
     * @param TType|null $resource
     * @param Closure(TType): mixed|null $formatUsing
     */
    public function __construct($resource, ?Closure $formatUsing = null)
    {
        parent::__construct($resource);

        $this->formatUsing = $formatUsing ?? fn ($result) => $result;
    }

    /**
     * @template TCollectionType
     * @param array<array-key, TCollectionType>|\Illuminate\Pagination\AbstractPaginator|\Illuminate\Support\Collection<array-key, TCollectionType> $resource
     * @return AnonymousCollection<EmptyAnonymousResource<TCollectionType>, array-key, TCollectionType>
     */
    public static function collection($resource): AnonymousCollection
    {
        return new AnonymousCollection($resource, EmptyAnonymousResource::class);
    }

    public function format(): mixed
    {
        if ($this->resource === null) {
            return [];
        }

        return call_user_func($this->formatUsing, $this->resource);
    }
}
