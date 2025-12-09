<?php

namespace Hyvr\Rocket\Helpers;

use MatthiasMullie\Minify\CSS;
use MatthiasMullie\Minify\JS;
use voku\helper\HtmlMin;

class MinifyHelper
{
    public static function html($raw){
        $minifer = new HtmlMin();

        return $minifer->minify($raw); 
    }

    public static function css($raw){
        $minifer = new CSS();
        $minifer->add($raw);
        
        return $minifer->minify();
    }

    public static function js($raw){
        $minifer = new JS();
        $minifer->add($raw);
        
        return $minifer->minify();
    }
}