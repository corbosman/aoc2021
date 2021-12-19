#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$input = collect(input('input.txt'));

$score = $input->reduce(function($incomplete, $line) {
            $stack = collect();
            foreach(str_split($line) as $chr) {
                if (str_contains('({[<', $chr)) $stack->push($chr);
                elseif ($stack->pop() !== pair($chr)) return $incomplete;
            }
            return $incomplete->push($stack);
        }, collect())
    ->map(fn($line) => $line->reverse()->reduce(fn($score, $chr) => ($score * 5) + match($chr) { '(' => 1, '[' => 2, '{' => 3, '<' => 4 }, 0))
    ->sort()
    ->values();

$score = $score[floor(count($score)/2)];
$time2 = microtime(true);
solution($score, $time1, $time2, '10b');

function pair($chr) : string
{
    return match($chr) {
        '[' => ']', ']' => '[', '{' => '}', '}' => '{', '<' => '>', '>' => '<', '(' => ')', ')' => '('
    };
}


