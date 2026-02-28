<?php

namespace Weijukeji\LaravelOpenObserve\Logging;

use Monolog\Logger;
use Weijukeji\LaravelOpenObserve\OpenObserveClient;

class OpenObserveLogger
{
    /**
     * Create a custom Monolog instance.
     */
    public function __invoke(array $config): Logger
    {
        $openObserveConfig = config('openobserve');

        // Create OpenObserve client
        $client = new OpenObserveClient($openObserveConfig);

        // Get additional fields from config
        $additionalFields = $openObserveConfig['additional_fields'] ?? [];

        // Create handler
        $handler = new OpenObserveHandler(
            $client,
            $additionalFields,
            $config['level'] ?? Logger::DEBUG
        );

        // Create logger instance
        $logger = new Logger($config['name'] ?? 'openobserve');
        $logger->pushHandler($handler);

        return $logger;
    }
}
