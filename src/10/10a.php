#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>str_split($i));

$brackets = [']' => '[', '}' => '{', ')' => '(', '>' => '<'];
$score    = [')' => 3, ']' => 57, '}' => 1197, '>' => 25137];
$errors   = collect();

foreach($input as $line) {
    $stack = collect();
    foreach($line as $nav) {
        if (in_array($nav, $brackets)) {
            $stack->push($nav);
        } else {
            if ($stack->pop() !== $brackets[$nav]) {
                $errors->push($score[$nav]);
                break;
            }
        }
    }
}

output($errors->sum());

