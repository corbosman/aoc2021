#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));
$numbers = map('intval', explode(',', $input->first()));
$boards = $input->splice(2)->filter(fn($l) => $l !== "")->chunk(5)
    ->map(fn($board) => $board->map(fn($row) => array_map('intval', preg_split('/\s+/', trim($row))))->values())->toArray();

$number = $winner = null;
foreach($numbers as $number) {
    $boards = map(fn($board) => map(fn($row) => map(fn($v) => $v === $number ? false : $v, $row), $board), $boards);
    foreach(winners($boards) as $winner) {
        if (count($boards) === 1) break 2;
        unset($boards[$winner]);
    }
}

$unmarked_sum = collect($boards[$winner])->flatten()->filter()->sum();
output("final score = " . $unmarked_sum * $number);

function winners($boards) : array
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
