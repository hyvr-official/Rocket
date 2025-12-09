<?php

namespace Hyvr\Rocket\Helpers;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class FileHelper
{
    public static function map($path){
        $map = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );

        foreach($iterator as $file){
            if($file->isFile()){
                $extension = strtolower($file->getExtension());

                if(!isset($map[$extension])) $map[$extension] = [];

                $map[$extension][] = $file->getPathname();
            }
        }

        return $map;
    }
}