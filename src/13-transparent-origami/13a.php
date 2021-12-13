#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    return reduce(file('input_e.txt', FILE_IGNORE_NEW_LINES), function($c, $v) {
        if (preg_match('/^fold along (.*)=(\d+)$/', $v, $m)) $c[1][] = [$m[1], (int)$m[2]];
        elseif (preg_match('/^(\d+),(\d+)$/', $v, $m))       $c[0][] = [(int)$m[2], (int)$m[1]];
        return $c;
    }, []);
}

function fill(array $coordinates) : array
{
    return reduce($coordinates, function($c, $v) {
        list($x, $y) = $v;
        $c[$x][$y] = 1;
        return $c;
    }, []);
}

function fold_up(array $paper, int $fold) : array
{
    $top_half = filter($paper, fn($k)=>$k<$fold, ARRAY_FILTER_USE_KEY);
    $bottom_half = filter($paper, fn($v, $k)=>$k>$fold, ARRAY_FILTER_USE_BOTH);

    foreach($bottom_half as $k => $v) {
        $row = $fold - ($k-$fold);
        $top_half[$row] = isset($top_half[$row]) ? $top_half[$row] + $v  : $v;
    }
    return $top_half;
}

function fold_left(array $paper, int $fold) : array
{
   $left_half = filter($paper, fn($k))
}

function fold(array $paper, array $folds) : array
{
    print_paper($paper);
    foreach($folds as $fold) {
        list($axis, $num) = [$fold[0], $fold[1]];
        $paper = match($axis) {
            'x' => fold_left($paper, $num),
            'y' => fold_up($paper, $num)
        };
    }
    return $paper;
}

[$coordinates, $folds] = load();
$paper = fill($coordinates);
$folded_paper = fold($paper, $folds);
$dots = array_sum(array_map("count",$folded_paper));
dd($dots);


function print_paper(array $paper) {
    $max_rows = max(array_keys($paper));
    $max_columns = collect($paper)->values()->map(fn($i) => array_flip($i))->flatten()->max();

    for($i=0;$i<=$max_rows;$i++) {
        echo "$i ";
        for($j=0; $j<=$max_columns; $j++) {
            echo isset($paper[$i][$j]) ? '#' : '.';
        }
        echo "\n";
    }
    echo "\n";
}
