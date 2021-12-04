<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent4b extends Command
{
    protected $signature = '4b';
    protected $description = 'Advent4b ';

    public function handle()
    {
        $last_board = null;
        $last_number = null;

        [$random, $boards] = $this->read_input();

        foreach($random as $number) {
            /* mark the number on the boards */
            $boards = $this->mark($number, $boards);

            /* find all the winning boards */
            $winners = $this->winners($boards);

            /* remove all full boards from the list of boards until we have 1 left */
            foreach($winners as $winner) {
                if (count($boards) === 1) {
                    $last_board = $winner;
                    $last_number = $number;
                    break 2;
                };
                unset($boards[$winner]);
            }
        }

        $unmarked_sum = collect($boards[$last_board])->flatten()->filter()->sum();

        $this->info("final score = " . $unmarked_sum * $last_number);
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
    public function winners($boards) : ?array
    {
        $winners = [];

        foreach($boards as $key => $board) {
            foreach(range(0,4) as $i) {
                if (count(array_filter($board[$i], fn($v) => $v !== false)) === 0 or
                    count(array_filter(array_column($board, $i), fn($v) => $v !== false)) === 0) {
                    $winners[] = $key;
                    break;
                }
            }
        }
        return $winners;
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
