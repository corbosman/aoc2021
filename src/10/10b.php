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
        $incomplete   = $this->discard($input);
        $completions  = $this->complete($incomplete);
        $score        = $this->score($completions);

        return $score->sort()->values()[$score->count() / 2];
    }

    private function discard($array) : Collection
    {
        return $array->filter(function($line) {
            $stack = collect();
            foreach(str_split($line) as $nav) {
                if (str_contains('({[<', $nav)) {
                    $stack->push($nav);
                } else if ($stack->pop() !== $this->match($nav)) {
                    return false;
                }
            }
            return true;
        });
    }

    private function complete($array) : Collection
    {
       return $array->map(function($line) {
          return collect(str_split($this->remove_pairs($line)))->reverse()->map(fn($c) => $this->match($c));
       });
    }

    public function score($array) : Collection
    {
        return $array->map(fn($line) => $line->reduce(function($score, $chr) {
           return ($score * 5) + match($chr) {
               ')' => 1, ']' => 2, '}' => 3, '>' => 4
           };
        }, 0))->values();
    }

    private function remove_pairs($str) : string
    {
        do {
            $str = str_replace('[]', '', $str, $count_1);
            $str = str_replace('{}', '', $str, $count_2);
            $str = str_replace('<>', '', $str, $count_3);
            $str = str_replace('()', '', $str, $count_4);
        } while ($count_1 + $count_2 + $count_3 + $count_4 > 0);
        return $str;
    }

    private function match($chr) : string
    {
        return match($chr) {
            '[' => ']', ']' => '[', '{' => '}', '}' => '{', '<' => '>', '>' => '<', '(' => ')', ')' => '('
        };
    }

}

