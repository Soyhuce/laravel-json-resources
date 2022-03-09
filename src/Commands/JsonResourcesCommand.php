<?php

namespace Soyhuce\JsonResources\Commands;

use Illuminate\Console\Command;

class JsonResourcesCommand extends Command
{
    public $signature = 'laravel-json-resources';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
