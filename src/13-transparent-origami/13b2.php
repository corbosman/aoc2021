#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    return reduce(file('input.txt', FILE_IGNORE_NEW_LINES), function($c, $v) {
        if (preg_match('/^fold along (.*)=(\d+)$/', $v, $m)) $c[1][] = [$m[1], (int)$m[2]];
        elseif (preg_match('/^(\d+),(\d+)$/', $v, $m))       $c[0][] = [(int)$m[1], (int)$m[2]];
        return $c;
    }, []);
}

function print_paper(array $coordinates) : void
{
    $max_rows = reduce($coordinates, fn($c, $v) => max($c, $v[1]),0);
    $max_columns = reduce($coordinates, fn($c, $v) => max($c, $v[0]),0);

    for($x=0; $x<=$max_rows; $x++) {
       for($y=0; $y<=$max_columns; $y++) {
           print(in_array([$y,$x], $coordinates) ? '#' : '.');
       }
       print("\n");
    }
}

function fold_up(array $coordinate, int $fold) : array
{
    $x = $coordinate[0];
    $y = $coordinate[1] < $fold ? $coordinate[1] : ($fold - ($coordinate[1] - $fold));
    return [$x, $y];
}

function fold_left(array $coordinate, int $fold) : array
{
    $x = $coordinate[0] < $fold ? $coordinate[0] : ($fold - ($coordinate[0] - $fold));
    $y = $coordinate[1];
    return [$x,$y];
}

function fold(array $coordinates, array $folds) : array
{
    return map(
        fn($coordinate) => reduce(
            $folds,
            fn($coordinate, $fold) => match($fold[0]) { 'x' => fold_left($coordinate, $fold[1]), 'y' => fold_up($coordinate, $fold[1])}, $coordinate),
        $coordinates
    );
}

[$coordinates, $folds] = load();
$coordinates = fold($coordinates, $folds);
print_paper($coordinates);

