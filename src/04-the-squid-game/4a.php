#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function winner($boards) : ?array
{
    foreach($boards as $board) {
        foreach(range(0,4) as $i) {
            if (count(array_filter($board[$i], fn($v) => $v !== false)) === 0) return $board;
            if (count(array_filter(array_column($board, $i), fn($v) => $v !== false)) === 0) return $board;
        }
    }
    return null;
}

$input = collect(input('inputs/input.txt'));
$numbers = map('intval', explode(',', $input[0]));
$boards = $input->splice(2)->filter(fn($l) => $l !== "")->chunk(5)
    ->map(fn($board) => $board->map(fn($row) => array_map('intval', preg_split('/\s+/', trim($row))))->values())->toArray();

$board = $number = null;

foreach($numbers as $number) {
    $boards = map(fn($board) => map(fn($row) => map(fn($v) => $v === $number ? false : $v, $row), $board), $boards);
    if (($board = winner($boards)) !== null) break;
}

$unmarked_sum = collect($board)->flatten()->filter()->sum();
$time2 = microtime(true);
solution($unmarked_sum * $number, $time1, $time2, '4a');

