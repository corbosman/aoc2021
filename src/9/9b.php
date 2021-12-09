#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>collect(str_split($i)));
$width = count($input[0])-1;
$height = count($input)-1;

$low_points = find_low_points($input, $width, $height);
$basins     = $low_points->map(fn($point) => flood($input, $point, $width, $height))->sort()->reverse()->take(3);
$output     = $basins->reduce(fn($c,$b)=>$c*$b,1);

output($output);

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

    $score = 1;
    $grid[$i][$j] = null;

    if ($i != 0 and $grid[$i-1][$j] != 9 and $grid[$i-1][$j] != null) $score += flood($grid, [$i-1, $j], $width, $height);
    if ($j != 0 and $grid[$i][$j-1] != 9 and $grid[$i][$j-1] != null) $score += flood($grid, [$i, $j-1], $width, $height);
    if ($j != $width and $grid[$i][$j+1] != 9 and $grid[$i][$j+1] != null) $score += flood($grid, [$i, $j+1], $width, $height);
    if ($i != $height and $grid[$i+1][$j] != 9 and $grid[$i+1][$j] != null) $score += flood($grid, [$i+1, $j], $width, $height);

    return $score;
}
