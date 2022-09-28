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
     * @return \Soyhuce\JsonResources\AnonymousResourceCollection<static>
     */
    public static function collection($resource): AnonymousResourceCollection
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
     * @return array<array-key, mixed>|\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|JsonSerializable
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
