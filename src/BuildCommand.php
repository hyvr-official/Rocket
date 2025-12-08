<?php

namespace Hyvr\Rocket;

use Illuminate\Console\Command;

class BuildCommand extends Command
{
    protected $signature = 'rocket:build {name=World}';
    protected $description = 'Say hello from the Hyvr package';

    public function handle()
    {
        $name = $this->argument('name');

        $this->info("Hello, {$name}! — From Hyvr Tools package");

        return Command::SUCCESS;
    }
}