<?php

namespace Thenextweb;

use Illuminate\Support\ServiceProvider;

class PassGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The commands to be registered from the package.
     *
     * @var array
     */
    protected $commands = [
    ];

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        $this->publishAllConfigs();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('passgenerator', function ($app) {
            return new PassGenerator($app);
        });

        $this->commands($this->commands);
    }

    /**
     * It is possible that someone using the package may not publish the config file, or they only
     * have a subset of the configurable values in their local version of the config file. This uses
     * the default values unless there are published ones.
     *
     * http://stagerightlabs.com/blog/laravel5-pacakge-development-service-provider
     */
    private function setupConfig()
    {
        //This will merge the 'default' values from the original config file of the package
        // with the values of the "published" config file (in case the config files were not published)
        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/passgenerator.php'), 'passgenerator');

        $this->mergeConfigFrom(realpath(__DIR__ . '/../config/pass-filesystem.php'), 'filesystems');
    }

    /**
     * Publish all the package's config files to the app.
     */
    private function publishAllConfigs()
    {
        $this->publishes([
            realpath(__DIR__ . '/../config/passgenerator.php') => config_path('passgenerator.php'),
        ], 'config');
    }
}
