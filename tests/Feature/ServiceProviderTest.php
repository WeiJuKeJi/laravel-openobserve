<?php

use Weijukeji\LaravelOpenObserve\OpenObserveClient;
use Weijukeji\LaravelOpenObserve\Facades\OpenObserve;

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
        ->and($config['auth'])->toHaveKeys(['email', 'password']);
});
