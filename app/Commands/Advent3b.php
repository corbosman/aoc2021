<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent3b extends Command
{
    protected $signature = '3b';
    protected $description = 'Advent3b';

    public function handle()
    {
        $input = collect(array_map(fn($line) => array_map('intval', str_split($line)), file(storage_path('3.txt'), FILE_IGNORE_NEW_LINES)));

        $o2 = $this->rating($input, 'o2');
        $co2 = $this->rating($input, 'co2');
        $life_support = $o2*$co2;

        $this->info("o2={$o2} co2={$co2} life_support={$life_support}");
    }

    public function rating($numbers, $rating, $pos = 0) : int
    {
        /* only one number left, we found the rating */
        if (count($numbers) === 1) return bindec(implode(null, $numbers[0]));

        /* count 1s if we want o2 rating, else count 0 */
        $digit = $rating === 'o2' ? 1 : 0;

        /* get the bits */
        $bits  = array_map(fn(...$a) => $a, ...$numbers);

        /* the median for these number of bits */
        $median = count($numbers) / 2;

        /* which bit are we checking */
        $bit = array_sum($bits[$pos]) >= $median ? (int)$digit : (int)!$digit;

        /* find all the numbers with this bit at the specific position */
        $numbers = $numbers->filter(fn($n) => $n[$pos] === $bit)->values();

        /* filter the remaining numbers until we find the rating */
        return $this->rating($numbers, $rating, $pos+1);
    }
}
