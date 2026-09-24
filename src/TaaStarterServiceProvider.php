<?php

namespace TaaStarter;

use Illuminate\Support\ServiceProvider;
use TaaStarter\Console\Commands\InstallCommand;

class TaaStarterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {$this->commands([
                InstallCommand::class,
            ]);
        }
    }

    public function register()
    {
        //
    }
}
