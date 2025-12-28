<?php

namespace Hyvr\Rocket\Helpers;


class URLHelper
{
    public static function buildRouteWithParameter(string $route, array $params): string {
        return preg_replace_callback('/\{(\w+)\}/', function ($matches) use ($params){
            return $params[$matches[1]] ?? $matches[0];
        }, $route);
    }
}