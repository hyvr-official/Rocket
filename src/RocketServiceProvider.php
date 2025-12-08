<?php

namespace Hyvr\Rocket;

use Illuminate\Support\ServiceProvider;

class ToolsServiceProvider extends ServiceProvider
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