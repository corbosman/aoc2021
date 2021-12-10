#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));
$winner = (new Parser)->parse($input);

output($winner);


class Parser
{
    public function parse($input)
    {
        $incomplete   = $this->process($input);
        $score        = $this->score($incomplete);

        return $score->sort()->values()[$score->count() / 2];
    }

    private function process($array) : Collection
    {
        $processed  = collect();

        foreach($array as $line) {
            $stack = collect();
            foreach(str_split($line) as $chr) {
                if (str_contains('({[<', $chr)) {
                    $stack->push($chr);
                } else {
                    if ($stack->pop() !== $this->match($chr)) continue 2;
                }
            }
            $processed->push($stack);
        }
        return $processed;
    }

    public function score($array) : Collection
    {
        return $array->map(fn($line) => $line->reverse()->reduce(function($score, $chr) {
           return ($score * 5) + match($chr) {
               '(' => 1, '[' => 2, '{' => 3, '<' => 4
           };
        }, 0))->values();
    }

    private function match($chr) : string
    {
        return match($chr) {
            '[' => ']', ']' => '[', '{' => '}', '}' => '{', '<' => '>', '>' => '<', '(' => ')', ')' => '('
        };
    }

}

