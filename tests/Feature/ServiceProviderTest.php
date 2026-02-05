<?php

use Minhyung\LaravelOpenObserve\OpenObserveClient;
use Minhyung\LaravelOpenObserve\Facades\OpenObserve;

test('service provider registers client as singleton', function () {
    $client = app(OpenObserveClient::class);

    expect($client)->toBeInstanceOf(OpenObserveClient::class)
        ->and(app(OpenObserveClient::class))->toBe($client);
});

test('client is accessible via alias', function () {
    expect(app('openobserve'))->toBeInstanceOf(OpenObserveClient::class);
});

test('facade resolves to client', function () {
    expect(OpenObserve::getFacadeRoot())->toBeInstanceOf(OpenObserveClient::class);
});

test('config has all required keys', function () {
    $config = config('openobserve');

    expect($config)->toBeArray()
        ->toHaveKeys(['enabled', 'url', 'organization', 'stream', 'auth', 'batch_size', 'timeout', 'ssl_verify', 'additional_fields'])
        ->and($config['auth'])->toHaveKeys(['username', 'password']);
});

test('config values match environment setup', function () {
    expect(config('openobserve.enabled'))->toBeTrue()
        ->and(config('openobserve.url'))->toBe('http://localhost:5080')
        ->and(config('openobserve.organization'))->toBe('default')
        ->and(config('openobserve.stream'))->toBe('default');
});
