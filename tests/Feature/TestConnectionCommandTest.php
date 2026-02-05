<?php

use Illuminate\Support\Facades\Http;

test('command displays configuration', function () {
    Http::fake(['*' => Http::response([], 200)]);

    $this->artisan('openobserve:test')
        ->expectsOutputToContain('http://localhost:5080')
        ->expectsOutputToContain('default')
        ->assertSuccessful();
});

test('command returns success on successful connection', function () {
    Http::fake(['*' => Http::response([], 200)]);

    $this->artisan('openobserve:test')
        ->expectsOutputToContain('Connection successful')
        ->assertSuccessful();
});

test('command returns failure on failed connection', function () {
    Http::fake(['*' => Http::response('error', 500)]);

    $this->artisan('openobserve:test')
        ->expectsOutputToContain('Connection failed')
        ->assertFailed();
});
