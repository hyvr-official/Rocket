<?php

namespace Hyvr\Rocket\Helpers;

use Illuminate\Support\Facades\File;

class ConfigHelper
{
    public static function init($terminal){
        $path = base_path('rocket.json');

        if(!File::exists($path)){
            $terminal->newline();
            $terminal->error("Error: config file rocket.json is not found on the path ".$path);
            exit();
        }

        $config = file_get_contents($path);
        $config = json_decode($config, true);

        if(!isset($config['base_url'])){
            $terminal->newline();
            $terminal->error("Error: base_url is not found on the rocket.json config file");
            exit();
        }

        config(['rocket' => $config]);
    }
}