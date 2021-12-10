#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input_e.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>collect(str_split($i)));

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

output($score[count($score)/2]);

function pair($chr) : string
{
    return match($chr) {
        '[' => ']', ']' => '[', '{' => '}', '}' => '{', '<' => '>', '>' => '<', '(' => ')', ')' => '('
    };
}


