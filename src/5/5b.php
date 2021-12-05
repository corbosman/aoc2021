#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$vents = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
    ->map(function($i) { preg_match_all('/(\d+)/', $i, $array); return $array[0]; })
    ->map(fn($i) => map(fn($i) => (int)$i, $i))
    ->reduce(function($carry, $i) {
        foreach(points($i[0], $i[1], $i[2], $i[3]) as $p) {
            $carry[$p[0]][$p[1]] = isset($carry[$p[0]][$p[1]]) ? $carry[$p[0]][$p[1]]+1 : 1;
        }
        return $carry;
    }, []);

output(collect($vents)->flatten()->filter(fn($i) => $i > 1)->count());

function points ($x1, $y1, $x2, $y2) : array
{
    $dx=$x2-$x1;
    $dy=$y2-$y1;
    $steps = gcd($dx, $dy);
    $points = [];
    for ($i=0; $i<=$steps; $i++) {
        $x = $x1+$i*$dx/$steps;
        $y = $y1+$i*$dy/$steps;
        $points[] = [$x, $y];
    }
    return $points;
}

function gcd($a,$b) : int
{
    $a = abs($a);
    $b = abs($b);
    return $a === 0 ? $b : ($b === 0 ? $a : gcd(min($a,$b),max($a,$b) % min($a,$b)));
}

