<?php

namespace Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Thenextweb\PassGeneratorServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [PassGeneratorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('filesystems.default', 'local');
        $app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => __DIR__ . '/../storage/app',
        ]);
        $app['config']->set('passgenerator.pass_type_identifier', 'pass.com.example.app');
        $app['config']->set('passgenerator.organization_name', 'Example Org');
        $app['config']->set('passgenerator.team_identifier', 'TEAMID1234');
    }
}