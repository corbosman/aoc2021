<?php

namespace App\Commands;

use Illuminate\Support\Collection;
use LaravelZero\Framework\Commands\Command;

class Advent4a extends Command
{
    protected $signature = '4a';
    protected $description = 'Advent4a ';

    public function handle()
    {
        [$random, $boards] = $this->read_input();

        [$winner, $number] = $this->play($random, $boards);

        $unmarked_sum = $winner->flatten()->filter(fn($v) => is_int($v))->sum();

        $this->info("final score = " . $unmarked_sum * $number);
    }

    /**
     * Play a game of bingo!
     */
    public function play($random, $boards, $i = 0) : array
    {
        if (($board = $this->winner($boards)) !== null) return [$board, $random[$i-1]];

        $boards = $this->round($random[$i], $boards);

        return $this->play($random, $boards, $i+1);
    }

    /**
     * Play a round of bingo
     */
    public function round($number, $boards) : Collection
    {
        return $boards->map(fn($board) => $board->map(fn($row) => array_map(fn($v) => $v === $number ? 'X' : $v, $row)));
    }

    /**
     * Check if any board is a winner
     */
    public function winner($boards) : ?Collection
    {
       foreach($boards as $board) {

           /* check each row */
           foreach($board as $row) {
               if (count(array_filter($row, fn($v) => $v !== 'X')) === 0) return $board;
           }

           /* check each column */
           foreach($board->transpose() as $row) {
               if (count(array_filter($row, fn($v) => $v !== 'X')) === 0) return $board;
           }
       }

       return null;
    }

    /**
     * read the input file and return the random numbers and a set of boards
     */
    public function read_input() : array
    {
        $input = collect(file(storage_path('4.txt'), FILE_IGNORE_NEW_LINES));

        $random = collect(explode(',', $input[0]))->map(fn($n) => (int)$n);

        $boards = $input
            ->splice(2)                                                                                                                // skip first 2 lines
            ->filter(fn($l) => $l !== "")                                                                                              // remove empty lines
            ->chunk(5)                                                                                                                 // chop into sections of 5 lines
            ->map(fn($board) => $board->map(fn($row) => array_map(fn($v) => (int)$v, preg_split('/\s+/', trim($row))))->values());     // convert into 2 dimensional array

        return [$random, $boards];
    }
}
