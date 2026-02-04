<?php

namespace Minhyung\LaravelOpenObserve;

use RuntimeException;

class OpenObserveClient
{
    protected string $url;
    protected string $organization;
    protected string $stream;
    protected string $username;
    protected string $password;
    protected int $timeout;
    protected bool $sslVerify;
    protected array $batch = [];
    protected int $batchSize;

    public function __construct(array $config)
    {
        $this->url = rtrim($config['url'] ?? '', '/');
        $this->organization = $config['organization'] ?? 'default';
        $this->stream = $config['stream'] ?? 'default';
        $this->username = $config['auth']['username'] ?? '';
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
        if (empty($logs)) {
            return true;
        }

        $url = sprintf(
            '%s/api/%s/%s/_json',
            $this->url,
            $this->organization,
            $this->stream
        );

        try {
            $ch = curl_init($url);

            if ($ch === false) {
                throw new RuntimeException('Failed to initialize cURL');
            }

            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($logs),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
                CURLOPT_USERPWD => $this->username . ':' . $this->password,
                CURLOPT_TIMEOUT => $this->timeout,
                CURLOPT_SSL_VERIFYPEER => $this->sslVerify,
                CURLOPT_SSL_VERIFYHOST => $this->sslVerify ? 2 : 0,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);

            curl_close($ch);

            if ($response === false) {
                throw new RuntimeException("cURL error: {$error}");
            }

            if ($httpCode < 200 || $httpCode >= 300) {
                throw new RuntimeException("HTTP error {$httpCode}: {$response}");
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
     * Destructor to ensure batch is flushed.
     */
    public function __destruct()
    {
        $this->flush();
    }
}
