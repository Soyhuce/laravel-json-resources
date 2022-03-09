<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Concerns;

use Illuminate\Http\JsonResponse;
use Soyhuce\JsonResources\Database\QueryLog;
use Soyhuce\JsonResources\Exceptions\DatabaseQueryDetected;
use Soyhuce\JsonResources\JsonResources;

trait ConvertsToResponse
{
    private ?QueryLog $queryLog = null;

    /**
     * @param \Illuminate\Http\Request $request
     */
    public function toResponse($request): JsonResponse
    {
        $this->beforeResponse();
        $response = parent::toResponse($request);
        $this->afterResponse($response);

        return $response;
    }

    protected function resourceClass(): string
    {
        return static::class;
    }

    private function beforeResponse(): void
    {
        if (JsonResources::$preventDatabaseQueries) {
            $this->queryLog = new QueryLog();
        }
    }

    private function afterResponse(JsonResponse $response): void
    {
        if ($this->queryLog !== null) {
            $this->queryLog->terminate();
            if ($this->queryLog->hasLogs()) {
                throw DatabaseQueryDetected::fromQueryLog($this->queryLog->getLogs());
            }
        }
        if (JsonResources::$addClassHeader) {
            $response->header('X-Json-Resource', $this->resourceClass());
        }
    }
}
