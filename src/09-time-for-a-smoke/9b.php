#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$input = collect(input('input.txt'))->map(fn($i)=>collect(str_split($i)));
$width = count($input[0])-1;
$height = count($input)-1;

$low_points = find_low_points($input, $width, $height);
$basins     = $low_points->map(fn($point) => flood($input, $point, $width, $height))->sort()->reverse()->take(3);
$output     = $basins->reduce(fn($c,$b)=>$c*$b,1);
$time2 = microtime(true);

solution($output, $time1, $time2, '9b');


function find_low_points($input, $width, $height) : ArrayAccess
{
    $coordinates = [];
    for($i=0;$i<=$height;$i++) {
        for($j=0;$j<=$width;$j++) {
            if( ($i == 0 or $input[$i][$j] < $input[$i-1][$j]) and
                ($j == 0 or $input[$i][$j] < $input[$i][$j-1]) and
                ($j == $width or $input[$i][$j] < $input[$i][$j+1]) and
                ($i == $height or $input[$i][$j] < $input[$i+1][$j])) {
                $coordinates[] = [$i,$j];
            }
        }
    }
    return collect($coordinates);
}

function flood(&$grid, $point, $width, $height) : int
{
    $i = $point[0];
    $j = $point[1];

    /* done if we're out of bounds or this grid point is not possible */
    if ($i < 0 or $j < 0 or $j > $width or $i > $height or $grid[$i][$j] == 9) return 0;

    $score = 1;
    $grid[$i][$j] = 9;

    $score += flood($grid, [$i-1, $j], $width, $height);
    $score += flood($grid, [$i, $j-1], $width, $height);
    $score += flood($grid, [$i, $j+1], $width, $height);
    $score += flood($grid, [$i+1, $j], $width, $height);

    return $score;
}
