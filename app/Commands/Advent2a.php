<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent2a extends Command
{
    protected $signature = '2a';
    protected $description = 'Advent 2a';

    public function handle()
    {
        $input = collect(file(storage_path('2.txt'), FILE_IGNORE_NEW_LINES));

        $position = $input->reduce(function($position, $move) {
            [$direction, $distance] = explode(' ', $move);
            return match ($direction) {
                'forward'   => [$position[0] + (int) $distance, $position[1]],
                'up'        => [$position[0], $position[1] - (int) $distance],
                'down'      => [$position[0], $position[1] + (int) $distance]
            };
        }, [0, 0]);

        $this->info($position[0] * $position[1]);
    }
}
