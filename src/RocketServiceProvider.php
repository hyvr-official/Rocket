<?php

namespace Hyvr\Rocket;

use Hyvr\Rocket\Commands\BuildCommand;
use Hyvr\Rocket\Commands\RunCommand;
use Hyvr\Rocket\Helpers\ConfigHelper;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Route;

class RocketServiceProvider extends ServiceProvider
{
    public function register(){}

    public function boot(){
        Route::macro('builder', function ($values){
            $this->routeParameterValues = $values;

            return $this;
        });

        if($this->app->runningInConsole()){
            $this->commands([
                BuildCommand::class,
                RunCommand::class
            ]);
        }
    }
}