<?php declare(strict_types=1);

namespace Soyhuce\JsonResources\Database;

use Illuminate\Support\Facades\DB;
use function count;

class QueryLog
{
    private bool $wasLogging;

    /** @var array<array{query: string, bindings: array<mixed>, time: int}> */
    private array $initialLog;

    /** @var array<array{query: string, bindings: array<mixed>, time: int}> */
    private array $finalLog;

    public function __construct()
    {
        $this->wasLogging = DB::logging();
        $this->initialLog = DB::getQueryLog();

        DB::enableQueryLog();
    }

    public function terminate(): void
    {
        $this->finalLog = DB::getQueryLog();

        if (!$this->wasLogging) {
            DB::disableQueryLog();
        }
    }

    public function hasLogs(): bool
    {
        return count($this->finalLog) > count($this->initialLog);
    }

    /**
     * @return array<array{query: string, bindings: array<mixed>, time: int}>
     */
    public function getLogs(): array
    {
        return collect($this->finalLog)->diffKeys($this->initialLog)->values()->all();
    }
}
