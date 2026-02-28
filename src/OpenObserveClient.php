<?php

namespace Weijukeji\LaravelOpenObserve;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class OpenObserveClient
{
    protected bool $enabled;
    protected string $url;
    protected string $organization;
    protected string $stream;
    protected string $email;
    protected string $password;
    protected int $timeout;
    protected bool $sslVerify;
    protected array $batch = [];
    protected int $batchSize;

    public function __construct(array $config)
    {
        $this->enabled = $config['enabled'] ?? true;
        $this->url = rtrim($config['url'] ?? '', '/');
        $this->organization = $config['organization'] ?? 'default';
        $this->stream = $config['stream'] ?? 'default';
        $this->email = $config['auth']['email'] ?? '';
        $this->password = $config['auth']['password'] ?? '';
        $this->timeout = $config['timeout'] ?? 5;
        $this->sslVerify = $config['ssl_verify'] ?? true;
        $this->batchSize = $config['batch_size'] ?? 100;
    }

    /**
     * Send a single log entry to OpenObserve.
     */
    public function send(array $data): bool
    {
        return $this->sendBatch([$data]);
    }

    /**
     * Add a log entry to the batch.
     */
    public function addToBatch(array $data): void
    {
        $this->batch[] = $data;

        if (count($this->batch) >= $this->batchSize) {
            $this->flush();
        }
    }

    /**
     * Flush the batch to OpenObserve.
     */
    public function flush(): bool
    {
        if (empty($this->batch)) {
            return true;
        }

        $batch = $this->batch;
        $this->batch = [];

        return $this->sendBatch($batch);
    }

    /**
     * Send a batch of log entries to OpenObserve.
     */
    public function sendBatch(array $logs): bool
    {
        if (!$this->enabled || empty($logs)) {
            return true;
        }

        $url = sprintf(
            '%s/api/%s/%s/_json',
            $this->url,
            $this->organization,
            $this->stream
        );

        try {
            $response = $this->buildRequest()->post($url, $logs);

            if (!$response->successful()) {
                throw new \RuntimeException(
                    "HTTP error {$response->status()}: {$response->body()}"
                );
            }

            return true;
        } catch (\Exception $e) {
            // Log error silently to avoid infinite loops
            error_log("OpenObserve error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Test the connection to OpenObserve.
     */
    public function testConnection(): bool
    {
        $testData = [
            'level' => 'info',
            'message' => 'OpenObserve connection test',
            'timestamp' => time(),
        ];

        return $this->send($testData);
    }

    /**
     * Build a configured HTTP request instance.
     */
    protected function buildRequest(): PendingRequest
    {
        $request = Http::withBasicAuth($this->email, $this->password)
            ->timeout($this->timeout)
            ->acceptJson();

        if (!$this->sslVerify) {
            $request->withoutVerifying();
        }

        return $request;
    }

    /**
     * Destructor to ensure batch is flushed.
     */
    public function __destruct()
    {
        $this->flush();
    }
}
