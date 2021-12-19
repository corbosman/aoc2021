#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$input = collect(input('input.txt'))->map(fn($i)=>str_split($i));

$brackets = [']' => '[', '}' => '{', ')' => '(', '>' => '<'];
$score    = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];
$errors   = collect();

foreach($input as $line) {
    $stack = collect();
    foreach($line as $nav) {
        if (in_array($nav, $brackets)) {
            $stack->push($nav);
        } else if ($stack->pop() !== $brackets[$nav]) {
            $errors->push($score[$nav]);
            break;
        }
    }
}

$time2 = microtime(true);
solution($errors->sum(), $time1, $time2, '10a');

