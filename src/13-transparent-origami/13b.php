#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function load() : array
{
    return reduce(input('input.txt'), function($c, $v) {
        if (preg_match('/^fold along (.*)=(\d+)$/', $v, $m)) $c[1][] = [$m[1], (int)$m[2]];
        elseif (preg_match('/^(\d+),(\d+)$/', $v, $m))       $c[0][] = [(int)$m[2], (int)$m[1]];
        return $c;
    }, []);
}

function print_paper(array $paper) : void
{
    $max_rows = max(array_keys($paper));
    $max_columns = collect($paper)->reduce(fn($c, $v) => max($c, max(array_keys($v))),0);

    for($i=0;$i<=$max_rows;$i++) {
        for($j=0; $j<=$max_columns; $j++) {
            print(isset($paper[$i][$j]) ? '#' : '.');
        }
        print("\n");
    }
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
    $top_half    = filter($paper, fn($k)=>$k<$fold, ARRAY_FILTER_USE_KEY);
    $bottom_half = filter($paper, fn($k)=>$k>$fold, ARRAY_FILTER_USE_KEY);

    foreach($bottom_half as $k => $v) {
        $row = $fold - ($k-$fold);
        $top_half[$row] = isset($top_half[$row]) ? $top_half[$row] + $v  : $v;
    }
    return $top_half;
}

function fold_left(array $paper, int $fold) : array
{
    $left_half  = map(fn($row)=>filter($row, fn($k)=>$k<$fold, ARRAY_FILTER_USE_KEY), $paper);
    $right_half = map(fn($row)=>filter($row, fn($k)=>$k>$fold, ARRAY_FILTER_USE_KEY), $paper);

    foreach($right_half as $x => $c) {
        foreach($c as $y => $v) {
            $column = $fold - ($y-$fold);
            $left_half[$x][$column] = 1;
        }
    }

    return $left_half;
}

function fold(array $paper, array $folds) : array
{
    foreach($folds as $fold) {
        list($axis, $num) = [$fold[0], $fold[1]];
        $paper = match($axis) { 'x' => fold_left($paper, $num), 'y' => fold_up($paper, $num)};
    }
    return $paper;
}

[$coordinates, $folds] = load();
$paper = fill($coordinates);
$folded_paper = fold($paper, $folds);
$time2 = microtime(true);

//print_paper($folded_paper);
solution('EPLGRULR', $time1, $time2, '13b');

