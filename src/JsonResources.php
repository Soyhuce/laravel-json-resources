<?php declare(strict_types=1);

namespace Soyhuce\JsonResources;

class JsonResources
{
    public static bool $preventDatabaseQueries = false;

    public static bool $addClassHeader = false;

    public static function preventDatabaseQueries(bool $preventDatabaseQueries = true): void
    {
        self::$preventDatabaseQueries = $preventDatabaseQueries;
    }

    public static function allowDatabaseQueries(): void
    {
        self::preventDatabaseQueries(false);
    }

    public static function addClassHeader(bool $addClassHeader = true): void
    {
        self::$addClassHeader = $addClassHeader;
    }

    public static function hideClassHeader(): void
    {
        self::addClassHeader(false);
    }
}
