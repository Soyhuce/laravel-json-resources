<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource as IlluminateJsonResource;
use JsonSerializable;
use ReflectionClass;
use Soyhuce\JsonResources\Concerns\ConvertsToResponse;

class JsonResource extends IlluminateJsonResource
{
    use ConvertsToResponse;

    /**
     * @param mixed $resource
     * @return AnonymousResourceCollection<array-key, static>
     */
    public static function collection($resource): AnonymousResourceCollection
    {
        return tap(new AnonymousResourceCollection($resource, static::class), function (AnonymousResourceCollection $collection): void {
            if (property_exists(static::class, 'preserveKeys')) {
                $collection->preserveKeys = (new ReflectionClass(static::class))
                    ->newInstanceWithoutConstructor()
                    ->preserveKeys === true;
            }
        });
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array<array-key, mixed>|Arrayable<array-key, mixed>|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        if (method_exists($this, 'format')) {
            return app()->call($this->format(...));
        }

        return parent::toArray($request);
    }

    public function jsonOptions(): int
    {
        return JSON_PRESERVE_ZERO_FRACTION;
    }
}
