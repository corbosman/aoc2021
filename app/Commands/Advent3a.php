<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent3a extends Command
{
    protected $signature = '3a';
    protected $description = 'Advent3a ';

    public function handle()
    {
        $input = array_map(fn($line) => array_map('intval', str_split($line)), file(storage_path('3.txt'), FILE_IGNORE_NEW_LINES));
        $median = count($input) / 2;

        /* transpose the input array, sum each row, and return 1 or 0 depending on median, then change result to decimal */
        $gamma = bindec(implode(null,array_map(fn($a) => array_sum($a) > $median ? 1 : 0, array_map(fn(...$a) => $a, ...$input))));

        /* bitwise xor with the total number of bits in gamma */
        $epsilon = $gamma ^ (pow(2, strlen(decbin($gamma))) - 1);

        $this->info($gamma * $epsilon);
    }
}
