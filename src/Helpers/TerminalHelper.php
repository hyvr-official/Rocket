<?php

namespace Hyvr\Rocket\Helpers;

class TerminalHelper
{
    public static function printHeader($terminal){
        $terminal->newLine();
        $terminal->line('<fg=#ef4444;options=bold>   ___   ____   _____   __ __   ____ ______</>');
        $terminal->line('<fg=#ef4444;options=bold>  / _ \ / __ \ / ___/  / //_/  / __//_  __/</>');
        $terminal->line('<fg=#ef4444;options=bold> / , _// /_/ // /__   / ,<    / _/   / /   </>');
        $terminal->line('<fg=#ef4444;options=bold>/_/|_| \____/ \___/  /_/|_|  /___/  /_/    </>');
        $terminal->line('<fg=white;options=bold>From Hyvr • v1.01</>');
        $terminal->newLine();
    }
}