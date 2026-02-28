<?php

namespace Weijukeji\LaravelOpenObserve\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use Weijukeji\LaravelOpenObserve\OpenObserveClient;

class OpenObserveHandler extends AbstractProcessingHandler
{
    protected OpenObserveClient $client;
    protected array $additionalFields;

    public function __construct(OpenObserveClient $client, array $additionalFields = [], int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->client = $client;
        $this->additionalFields = $additionalFields;
    }

    /**
     * Write the log record to OpenObserve.
     */
    protected function write(LogRecord $record): void
    {
        $data = [
            'timestamp' => $record->datetime->getTimestamp(),
            'level' => strtolower($record->level->getName()),
            'level_name' => $record->level->getName(),
            'message' => $record->message,
            'channel' => $record->channel,
            'context' => $record->context,
            'extra' => $record->extra,
        ];

        // Merge additional fields
        $data = array_merge($data, $this->additionalFields);

        // Add exception information if present
        if (isset($record->context['exception']) && $record->context['exception'] instanceof \Throwable) {
            $exception = $record->context['exception'];
            $data['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        }

        $this->client->addToBatch($data);
    }

    /**
     * Flush any remaining logs on destruction.
     */
    public function __destruct()
    {
        $this->client->flush();
    }
}
