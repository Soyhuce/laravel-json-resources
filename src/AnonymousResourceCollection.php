<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection as IlluminateAnonymousResourceCollection;
use Soyhuce\JsonResources\Concerns\ConvertsToResponse;

class AnonymousResourceCollection extends IlluminateAnonymousResourceCollection
{
    use ConvertsToResponse;

    protected function resourceClass(): string
    {
        return $this->collects;
    }
}
