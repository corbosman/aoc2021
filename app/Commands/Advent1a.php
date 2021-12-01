<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent1a extends Command
{
    protected $signature = '1a';
    protected $description = 'Advent 1a';

    public function handle()
    {
        $this->info(
            collect(file(storage_path('1.txt'), FILE_IGNORE_NEW_LINES))
            ->sliding(2)
            ->filter(fn ($window) => $window->first() < $window->last())
            ->count()
        );
    }
}
