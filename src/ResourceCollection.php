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
     * @return array<mixed>|\Illuminate\Contracts\Support\Arrayable<array-key, mixed>|JsonSerializable
     */
    public function toArray($request)
    {
        return $this->format() ?? parent::toArray($request);
    }

    /**
     * @return array<mixed>|null
     */
    public function format(): ?array
    {
        return null;
    }

    public function jsonOptions(): int
    {
        return JSON_PRESERVE_ZERO_FRACTION;
    }
}
