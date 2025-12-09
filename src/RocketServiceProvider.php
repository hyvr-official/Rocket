<?php

namespace Hyvr\Rocket;

use Hyvr\Rocket\Commands\BuildCommand;
use Illuminate\Support\ServiceProvider;

class RocketServiceProvider extends ServiceProvider
{
    public function register(){}

    public function boot()
    {
        if($this->app->runningInConsole()){
            $this->commands([
                BuildCommand::class,
            ]);
        }
    }
}