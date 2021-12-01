<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;

class Advent1b extends Command
{
    protected $signature = '1b';
    protected $description = 'Advent 1b';

    public function handle()
    {
        $this->info(
            collect(file(storage_path('1.txt'), FILE_IGNORE_NEW_LINES))
            ->sliding(3)
            ->map(fn ($window) => $window->sum())
            ->sliding(2)
            ->filter(fn ($window) => $window->first() < $window->last())
            ->count()
        );
    }

}
