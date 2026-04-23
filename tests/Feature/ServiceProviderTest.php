<?php

namespace Tests\Feature;

use Illuminate\Support\ServiceProvider;
use Tests\TestCase;
use Thenextweb\PassGeneratorServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_registers_passgenerator_disk(): void
    {
        $disk = $this->app['config']->get('filesystems.disks.passgenerator');

        $this->assertIsArray($disk);
        $this->assertSame(storage_path('app/passgenerator'), $disk['root']);
    }

    public function test_registers_passgenerator_binding(): void
    {
        $this->assertTrue($this->app->bound('passgenerator'));
    }

    public function test_exposes_passgenerator_config_publish_tag(): void
    {
        $paths = ServiceProvider::pathsToPublish(PassGeneratorServiceProvider::class, 'passgenerator-config');

        $this->assertNotEmpty($paths);
        $this->assertContains(config_path('passgenerator.php'), array_values($paths));
    }
}