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

        $unmarked_sum = collect($winner)->flatten()->filter()->sum();

        $this->info("final score = " . $unmarked_sum * $number);
    }

    /**
     * Play bingo until we have a winner!
     */
    public function play($random, $boards, $i = 0) : array
    {
        if (($board = $this->winner($boards)) !== null) return [$board, $random[$i-1]];

        $boards = $this->mark($random[$i], $boards);

        return $this->play($random, $boards, $i+1);
    }

    /**
     * mark numbers on the boards
     */
    public function mark($number, $boards) : array
    {
        return map(fn($board) => map(fn($row) => map(fn($v) => $v === $number ? false : $v, $row), $board), $boards);
    }

    /**
     * Check if any board is a winner
     */
    public function winner($boards) : ?array
    {
       foreach($boards as $board) {
           foreach(range(0,4) as $i) {
               if (count(array_filter($board[$i], fn($v) => $v !== false)) === 0) return $board;
               if (count(array_filter(array_column($board, $i), fn($v) => $v !== false)) === 0) return $board;
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

        $random = map('intval', explode(',', $input[0]));

        $boards = $input
            ->splice(2)                                                                                                                // skip first 2 lines
            ->filter(fn($l) => $l !== "")                                                                                              // remove empty lines
            ->chunk(5)                                                                                                                 // chop into sections of 5 lines
            ->map(fn($board) => $board->map(fn($row) => array_map(fn($v) => (int)$v, preg_split('/\s+/', trim($row))))->values())
            ->toArray();

        return [$random, $boards];
    }
}
