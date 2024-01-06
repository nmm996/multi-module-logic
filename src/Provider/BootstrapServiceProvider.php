<?php

namespace Riario\Logic\Provider;

use Illuminate\Support\ServiceProvider;
use Riario\Logic\Contracts\LogicInterface;

class BootstrapServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot(): void
    {
    }

    /**
     * Register the provider.
     */
    public function register(): void
    {
        $this->app[LogicInterface::class]->register();
    }
}
