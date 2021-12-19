#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$vents = collect(input('input.txt'))
    ->map(function($i) { preg_match_all('/(\d+)/', $i, $array); return $array[0]; })
    ->filter(fn($i) => $i[0] === $i[2] or $i[1] === $i[3])
    ->map(fn($i) => map(fn($i) => (int)$i, $i))
    ->reduce(function($carry, $i) {
        foreach(range($i[0], $i[2]) as $x) {
            foreach(range($i[1], $i[3]) as $y) {
                $carry[$x][$y] = isset($carry[$x][$y]) ? $carry[$x][$y]+1 : 1;
            }
        }
        return $carry;
    }, []);

$time2 = microtime(true);
$count = collect($vents)->flatten()->filter(fn($i) => $i > 1)->count();
solution($count, $time1, $time2, '5a');

