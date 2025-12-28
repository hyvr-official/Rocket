<?php

namespace Hyvr\Rocket\Helpers;

use Illuminate\Support\Facades\File;

class ConfigHelper
{
    public static function init($terminal){
        $path = base_path('rocket.json');

        if(File::exists($path)){
            $config = file_get_contents($path);
            $config = json_decode($config, true);

            config(['rocket' => $config]);

            $terminal->line('⚙️ Config file loaded from: '.$path);
            $terminal->newLine();
        }
        else config(['rocket' => []]);
    }
}