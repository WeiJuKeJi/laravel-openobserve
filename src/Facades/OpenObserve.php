<?php

namespace Minhyung\LaravelOpenObserve\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static bool send(array $data)
 * @method static void addToBatch(array $data)
 * @method static bool flush()
 * @method static bool sendBatch(array $logs)
 * @method static bool testConnection()
 *
 * @see \Minhyung\LaravelOpenObserve\OpenObserveClient
 */
class OpenObserve extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'openobserve';
    }
}
