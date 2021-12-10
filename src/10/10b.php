#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));

$score = $input->reduce(function($incomplete, $line) {
    $stack = collect();
    foreach(str_split($line) as $chr) {
        if (str_contains('({[<', $chr)) {
            $stack->push($chr);
        } else {
            if ($stack->pop() !== pair($chr)) return $incomplete;
        }
    }
    return $incomplete->push($stack);
}, collect())->map(fn($line) => $line->reverse()->reduce(function($score, $chr) {
    return ($score * 5) + match($chr) {
            '(' => 1, '[' => 2, '{' => 3, '<' => 4
        };
}, 0))->sort()->values();;

output($score[count($score)/2]);

function pair($chr) : string
{
    return match($chr) {
        '[' => ']', ']' => '[', '{' => '}', '}' => '{', '<' => '>', '>' => '<', '(' => ')', ')' => '('
    };
}


