#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load()
{
    return array_map(fn($l) => str_split($l), input('inputs/input.txt'));
}

function move(array $map, int $width, int $height) : array
{
    $moved_x = [];

    /* move right */
    for($x=0; $x<$height; $x++) {
        for($y=0; $y<$width; $y++) {
            $next_y = $y + 1 === $width ? 0 : $y + 1;

            if (!isset($moved_x[$x][$y]) && !isset($moved_x[$x][$next_y]) and $map[$x][$y] === '>' and $map[$x][$next_y] === '.') {
                $map[$x][$y] = '.';
                $map[$x][$next_y] = '>';
                $moved_x[$x][$y] = true;
                $moved_x[$x][$next_y] = true;
            }
        }
    }

    $moved_y = [];
    /* move down */
    for($x=0; $x<$height; $x++) {
        for($y=0; $y<$width; $y++) {
            $next_x = $x + 1 === $height ? 0 : $x + 1;

            if (!isset($moved_y[$x][$y]) and !isset($moved_y[$next_x][$y]) and $map[$x][$y] === 'v' and $map[$next_x][$y] === '.') {
                $map[$x][$y] = '.';
                $map[$next_x][$y] = 'v';
                $moved_y[$x][$y] = true;
                $moved_y[$next_x][$y] = true;
            }
        }
    }

    return [$map, count($moved_x) + count($moved_y)];
}

$time1  = microtime(true);
$map    = load();
$width  = count($map[0]);
$height = count($map);
$step = 0;

do {
    [$map, $moves] = move($map, $width, $height);
    $step++;
} while ($moves > 0);

$time2  = microtime(true);
solution($step, $time1, $time2, '25a');
