<?php

namespace App\Commands;

use LaravelZero\Framework\Commands\Command;

class Advent4b extends Command
{
    protected $signature = '4b';
    protected $description = 'Advent4b ';

    public function handle()
    {
        $input = collect(file(storage_path('4.txt'), FILE_IGNORE_NEW_LINES));
        $random = map('intval', explode(',', $input[0]));
        $boards = $input->splice(2)->filter(fn($l) => $l !== "")->chunk(5)
            ->map(fn($board) => $board->map(fn($row) => array_map('intval', preg_split('/\s+/', trim($row))))->values())
            ->toArray();

        $number = $winner = null;
        foreach($random as $number) {
            $boards = $this->mark($number, $boards);
            $winners = $this->winners($boards);

            foreach($winners as $winner) {
                if (count($boards) === 1) break 2;
                unset($boards[$winner]);
            }
        }

        $unmarked_sum = collect($boards[$winner])->flatten()->filter()->sum();

        $this->info("final score = " . $unmarked_sum * $number);
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
}
