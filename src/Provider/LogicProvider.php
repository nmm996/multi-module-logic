<?php

namespace Riario\Logic\Provider;

use Illuminate\Support\ServiceProvider;
use Logic\Price\Providers\PriceServiceProvider;

class LogicProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerProviders();
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerLogics();
        $this->registerNamespaces();
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
    }
    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(\Riario\Logic\Contracts\LogicInterface::class, function ($app) {
            $path = $app['config']->get('logic.paths.logic');

            return new \Riario\Logic\Laravel\LogicRepository($app, $path);
        });
    }

    /**
     * Register all modules.
     */
    protected function registerLogics()
    {
        $this->app->register(BootstrapServiceProvider::class);
    }
    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../Config/logic.php';
        $this->publishes([
            $configPath => config_path('logic.php'),
        ], 'logic');
    }
}