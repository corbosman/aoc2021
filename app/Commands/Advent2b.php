<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent2b extends Command
{
    protected $signature = '2b';
    protected $description = 'Advent 2b';

    public function handle()
    {
        $input = collect(file(storage_path('2.txt'), FILE_IGNORE_NEW_LINES));

        $position = $input->reduce(function($pos, $move) {
            [$direction, $distance] = explode(' ', $move);
            return match ($direction) {
                'forward'   => ['x' => $pos['x'] + (int)$distance, 'y' => ($pos['y'] + ($distance * $pos['a'])), 'a' => $pos['a']],
                'up'        => ['x' => $pos['x'], 'y' => $pos['y'], 'a' => $pos['a'] - (int)$distance],
                'down'      => ['x' => $pos['x'], 'y' => $pos['y'], 'a' => $pos['a'] + (int)$distance]
            };
        }, ['x' => 0, 'y' => 0, 'a' => 0]);

        $this->info($position['x'] * $position['y']);
    }
}
