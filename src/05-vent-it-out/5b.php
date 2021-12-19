#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function points ($x1, $y1, $x2, $y2) : array
{
    $dx=$x2-$x1;
    $dy=$y2-$y1;
    $steps = max(abs($x1-$x2), abs($y1-$y2));
    $points = [];
    for ($i=0; $i<=$steps; $i++) {
        $x = $x1+$i*$dx/$steps;
        $y = $y1+$i*$dy/$steps;
        $points[] = [$x, $y];
    }
    return $points;
}

$vents = collect(input('input.txt'))
    ->map(function($i) { preg_match_all('/(\d+)/', $i, $array); return $array[0]; })
    ->map(fn($i) => map(fn($i) => (int)$i, $i))
    ->reduce(function($points, $i) {
        foreach(points($i[0], $i[1], $i[2], $i[3]) as $p) {
            $points[$p[0]][$p[1]] = isset($points[$p[0]][$p[1]]) ? $points[$p[0]][$p[1]]+1 : 1;
        }
        return $points;
    }, []);

$time2 = microtime(true);
$output = collect($vents)->flatten()->filter(fn($i) => $i > 1)->count();
solution($output, $time1, $time2, '5b');


