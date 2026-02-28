<?php

use Weijukeji\LaravelOpenObserve\OpenObserveClient;
use Illuminate\Support\Facades\Http;

function makeClient(array $overrides = []): OpenObserveClient
{
    return new OpenObserveClient(array_merge([
        'enabled' => false,
        'url' => 'http://localhost:5080',
        'organization' => 'test-org',
        'stream' => 'test-stream',
        'auth' => [
            'email' => 'test@example.com',
            'password' => 'password',
        ],
        'timeout' => 5,
        'ssl_verify' => true,
        'batch_size' => 100,
    ], $overrides));
}

test('client can be instantiated with config', function () {
    $client = makeClient();

    expect($client)->toBeInstanceOf(OpenObserveClient::class);
});

test('config defaults are applied for missing keys', function () {
    $client = new OpenObserveClient([]);

    expect($client)->toBeInstanceOf(OpenObserveClient::class);
});

test('send returns true when disabled', function () {
    $client = makeClient(['enabled' => false]);

    expect($client->send(['message' => 'test']))->toBeTrue();
});

test('sendBatch returns true for empty logs', function () {
    $client = makeClient(['enabled' => true]);

    expect($client->sendBatch([]))->toBeTrue();
});

test('flush returns true when batch is empty', function () {
    $client = makeClient();

    expect($client->flush())->toBeTrue();
});

test('addToBatch auto-flushes when batch size reached', function () {
    Http::fake(['*' => Http::response([], 200)]);

    $client = makeClient([
        'enabled' => true,
        'batch_size' => 2,
    ]);

    $client->addToBatch(['message' => 'first']);
    $client->addToBatch(['message' => 'second']);

    Http::assertSentCount(1);
});

test('sendBatch posts to correct endpoint', function () {
    Http::fake(['*' => Http::response([], 200)]);

    $client = makeClient([
        'enabled' => true,
        'organization' => 'my-org',
        'stream' => 'my-stream',
    ]);

    $client->send(['message' => 'test']);

    Http::assertSent(function ($request) {
        return $request->url() === 'http://localhost:5080/api/my-org/my-stream/_json';
    });
});

test('sendBatch returns false on http error', function () {
    Http::fake(['*' => Http::response('server error', 500)]);

    $client = makeClient(['enabled' => true]);

    expect($client->send(['message' => 'test']))->toBeFalse();
});

test('url trailing slash is trimmed', function () {
    Http::fake(['*' => Http::response([], 200)]);

    $client = makeClient([
        'enabled' => true,
        'url' => 'http://localhost:5080/',
    ]);

    $client->send(['message' => 'test']);

    Http::assertSent(function ($request) {
        return str_starts_with($request->url(), 'http://localhost:5080/api/');
    });
});
