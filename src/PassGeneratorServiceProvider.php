<?php

namespace Thenextweb;

use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider;

class PassGeneratorServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered from the package.
     *
     * @var array<string>
     */
    protected array $commands = [];

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            realpath(__DIR__ . '/../config/passgenerator.php'),
            'passgenerator'
        );

        $this->registerFilesystemDisk($this->app['config']);

        $this->app->bind('passgenerator', static function () {
            return new PassGenerator();
        });
    }

    /**
     * Perform post-registration booting of services.
     */
    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            realpath(__DIR__ . '/../config/passgenerator.php') => config_path('passgenerator.php'),
        ], 'config');

        $this->publishes([
            realpath(__DIR__ . '/../config/passgenerator.php') => config_path('passgenerator.php'),
        ], 'passgenerator-config');

        if ($this->commands !== []) {
            $this->commands($this->commands);
        }
    }

    private function registerFilesystemDisk(ConfigRepository $config): void
    {
        if ($config->has('filesystems.disks.passgenerator')) {
            return;
        }

        $defaultDisk = $config->get('filesystems.default');
        $defaultDiskConfig = is_string($defaultDisk)
            ? $config->get("filesystems.disks.$defaultDisk")
            : null;

        if (!is_array($defaultDiskConfig)) {
            $defaultDiskConfig = [
                'driver' => 'local',
            ];
        }

        $defaultDiskConfig['root'] = storage_path('app/passgenerator');

        $config->set('filesystems.disks.passgenerator', $defaultDiskConfig);
    }
}