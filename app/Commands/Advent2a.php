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

        $position = $input->reduce(function($pos, $move) {
            [$direction, $distance] = explode(' ', $move);
            return match ($direction) {
                'forward'   => ['x' => $pos['x'] + (int) $distance, 'y' => $pos['y']],
                'up'        => ['x' => $pos['x'], 'y' => $pos['y'] - (int) $distance],
                'down'      => ['x' => $pos['x'], 'y' => $pos['y'] + (int) $distance]
            };
        }, ['x' => 0, 'y' => 0]);

        $this->info($position['x'] * $position['y']);
    }
}
