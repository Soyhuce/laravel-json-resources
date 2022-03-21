<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Http\Resources\Json\ResourceCollection as IlluminateResourceCollection;
use JsonSerializable;
use Soyhuce\JsonResources\Concerns\ConvertsToResponse;

class ResourceCollection extends IlluminateResourceCollection
{
    use ConvertsToResponse;

    /**
     * @param \Illuminate\Http\Request $request
     * @return array<array-key, mixed>|\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|JsonSerializable
     */
    public function toArray($request)
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
