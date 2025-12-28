<?php

namespace Hyvr\Rocket\Core;

use Illuminate\Routing\UrlGenerator;

class RocketURLGenerator extends UrlGenerator
{
    public function formatRoot($scheme, $root = null){
        if(config('rocket.is_in_build_context', false)){
            $base_url = trim(config('rocket.base_url', ''), '/');

            return $base_url;
        }
        else{
            if(is_null($root)){
                if(is_null($this->cachedRoot)){
                    $this->cachedRoot = $this->forcedRoot ?: $this->request->root();
                }

                $root = $this->cachedRoot;
            }

            $start = str_starts_with($root, 'http://') ? 'http://' : 'https://';

            return preg_replace('~'.$start.'~', $scheme, $root, 1);
        }
    }

    public function format($root, $path, $route = null){
        $path = '/'.trim($path, '/');

        if($this->formatHostUsing){
            $root = call_user_func($this->formatHostUsing, $root, $route);
        }

        if($this->formatPathUsing){
            $path = call_user_func($this->formatPathUsing, $path, $route);
        }

        if(config('rocket.is_in_build_context', false) && trim(config('rocket.base_url', ''), '/')==''){
            return $path;
        }
        else return trim($root.$path, '/');
    }
}