<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Exceptions;

use Exception;
use Illuminate\Support\Str;
use function sprintf;

class DatabaseQueryDetected extends Exception
{
    /**
     * @param array<array{query: string, bindings: array<mixed>, time: float|null}> $queryLog
     */
    public static function fromQueryLog(array $queryLog): self
    {
        $queries = collect($queryLog)->map(fn (array $query) => static::formatQuery($query));

        $message = sprintf(
            '%d %s detected in resource :%s%s',
            $queries->count(),
            Str::plural('query', $queries->count()),
            PHP_EOL,
            $queries->implode(PHP_EOL)
        );

        return new self($message);
    }

    /**
     * @param array{query: string, bindings: array<mixed>, time: float|null} $query
     */
    protected static function formatQuery(array $query): string
    {
        return Str::replaceArray('?', $query['bindings'], $query['query']);
    }
}
