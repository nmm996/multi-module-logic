<?php

namespace Riario\Logic\Provider;

use Illuminate\Support\ServiceProvider;
use Riario\Logic\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected $commands = [
        Commands\MakeLogicCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderCommand::class
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->commands(config('logic.commands', $this->commands));
    }

    public function provides(): array
    {
        return $this->commands;
    }

}