<?php

namespace Minhyung\LaravelOpenObserve\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Minhyung\LaravelOpenObserve\OpenObserveServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            OpenObserveServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'OpenObserve' => \Minhyung\LaravelOpenObserve\Facades\OpenObserve::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('openobserve.url', 'http://localhost:5080');
        $app['config']->set('openobserve.organization', 'default');
        $app['config']->set('openobserve.stream', 'default');
        $app['config']->set('openobserve.auth.username', 'test@example.com');
        $app['config']->set('openobserve.auth.password', 'password');
        $app['config']->set('openobserve.enabled', true);
    }
}
