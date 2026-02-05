<?php

use Minhyung\LaravelOpenObserve\OpenObserveClient;
use Minhyung\LaravelOpenObserve\Logging\OpenObserveHandler;
use Monolog\Level;
use Monolog\LogRecord;

function makeDisabledClient(): OpenObserveClient
{
    return new OpenObserveClient([
        'enabled' => false,
        'url' => 'http://localhost:5080',
        'organization' => 'default',
        'stream' => 'default',
        'auth' => ['email' => 'test', 'password' => 'test'],
        'batch_size' => 100,
    ]);
}

function makeLogRecord(string $message = 'Test message', Level $level = Level::Info, array $context = []): LogRecord
{
    return new LogRecord(
        datetime: new \DateTimeImmutable(),
        channel: 'test',
        level: $level,
        message: $message,
        context: $context,
    );
}

test('handler can be instantiated', function () {
    $handler = new OpenObserveHandler(makeDisabledClient());

    expect($handler)->toBeInstanceOf(OpenObserveHandler::class);
});

test('handler writes log record to client batch', function () {
    $client = makeDisabledClient();
    $handler = new OpenObserveHandler($client);

    $handler->handle(makeLogRecord('Hello world'));

    // flush triggers sendBatch, which returns true because enabled=false
    expect($client->flush())->toBeTrue();
});

test('handler merges additional fields', function () {
    $client = Mockery::mock(OpenObserveClient::class);
    $client->shouldReceive('addToBatch')
        ->once()
        ->withArgs(function (array $data) {
            return $data['app_name'] === 'TestApp' && $data['environment'] === 'testing';
        });
    $client->shouldReceive('flush')->andReturn(true);

    $handler = new OpenObserveHandler($client, [
        'app_name' => 'TestApp',
        'environment' => 'testing',
    ]);

    $handler->handle(makeLogRecord());
});

test('handler extracts log level correctly', function () {
    $client = Mockery::mock(OpenObserveClient::class);
    $client->shouldReceive('addToBatch')
        ->once()
        ->withArgs(function (array $data) {
            return $data['level'] === 'error' && $data['level_name'] === 'ERROR';
        });
    $client->shouldReceive('flush')->andReturn(true);

    $handler = new OpenObserveHandler($client);
    $handler->handle(makeLogRecord('Error occurred', Level::Error));
});

test('handler captures exception information', function () {
    $exception = new \RuntimeException('Something went wrong', 42);

    $client = Mockery::mock(OpenObserveClient::class);
    $client->shouldReceive('addToBatch')
        ->once()
        ->withArgs(function (array $data) {
            return isset($data['exception'])
                && $data['exception']['class'] === 'RuntimeException'
                && $data['exception']['message'] === 'Something went wrong'
                && $data['exception']['code'] === 42
                && isset($data['exception']['file'])
                && isset($data['exception']['line'])
                && isset($data['exception']['trace']);
        });
    $client->shouldReceive('flush')->andReturn(true);

    $handler = new OpenObserveHandler($client);
    $handler->handle(makeLogRecord('Error', Level::Error, ['exception' => $exception]));
});

test('handler ignores non-throwable exception context', function () {
    $client = Mockery::mock(OpenObserveClient::class);
    $client->shouldReceive('addToBatch')
        ->once()
        ->withArgs(function (array $data) {
            return !isset($data['exception']);
        });
    $client->shouldReceive('flush')->andReturn(true);

    $handler = new OpenObserveHandler($client);
    $handler->handle(makeLogRecord('Not an exception', Level::Warning, ['exception' => 'string value']));
});
