<?php

namespace App\Services;

use InvalidArgumentException;

class DataServiceFactory
{
    private static $services = [];

    public static function create(string $type): DataServiceInterface
    {
        // dd($type);
        if (!isset(self::$services[$type])) {
            self::$services[$type] = match($type) {
                'Tournament' => new BracketDataService(),
                'League' => new LeagueDataService(),
                default => throw new InvalidArgumentException("Unknown type: $type")
            };
        }

        return self::$services[$type];
    }
}