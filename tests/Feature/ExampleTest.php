<?php

use Minhyung\LaravelOpenObserve\OpenObserveClient;
use Minhyung\LaravelOpenObserve\Facades\OpenObserve;

test('service provider registers client', function () {
    $client = app(OpenObserveClient::class);

    expect($client)->toBeInstanceOf(OpenObserveClient::class);
});

test('facade can access client', function () {
    expect(OpenObserve::getFacadeRoot())->toBeInstanceOf(OpenObserveClient::class);
});

test('config is loaded correctly', function () {
    expect(config('openobserve'))->toBeArray()
        ->and(config('openobserve.enabled'))->toBeBool()
        ->and(config('openobserve.url'))->toBeString();
});
