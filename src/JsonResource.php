<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Http\Resources\Json\JsonResource as IlluminateJsonResource;
use ReflectionClass;
use Soyhuce\JsonResources\Concerns\ConvertsToResponse;
use function is_array;

class JsonResource extends IlluminateJsonResource
{
    use ConvertsToResponse;

    public static function collection($resource)
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function ($collection): void {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new ReflectionClass(static::class))
                    ->newInstanceWithoutConstructor()
                    ->preserveKeys === true;
            }
        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return $this->format();
    }

    /**
     * @return array<string, mixed>
     */
    public function format(): array
    {
        if ($this->resource === null) {
            return [];
        }

        return is_array($this->resource)
            ? $this->resource
            : $this->resource->toArray();
    }

    public function jsonOptions(): int
    {
        return JSON_PRESERVE_ZERO_FRACTION;
    }
}
