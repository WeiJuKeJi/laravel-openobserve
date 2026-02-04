<?php

use Minhyung\LaravelOpenObserve\OpenObserveClient;

test('client can be instantiated with config', function () {
    $config = [
        'url' => 'http://localhost:5080',
        'organization' => 'test-org',
        'stream' => 'test-stream',
        'auth' => [
            'username' => 'test@example.com',
            'password' => 'password',
        ],
        'timeout' => 5,
        'ssl_verify' => true,
        'batch_size' => 100,
    ];

    $client = new OpenObserveClient($config);

    expect($client)->toBeInstanceOf(OpenObserveClient::class);
});

test('client can add logs to batch', function () {
    $config = [
        'url' => 'http://localhost:5080',
        'organization' => 'test-org',
        'stream' => 'test-stream',
        'auth' => [
            'username' => 'test@example.com',
            'password' => 'password',
        ],
        'batch_size' => 2,
    ];

    $client = new OpenObserveClient($config);

    // This should not throw any errors
    $client->addToBatch([
        'level' => 'info',
        'message' => 'Test message',
    ]);

    expect(true)->toBeTrue();
});
