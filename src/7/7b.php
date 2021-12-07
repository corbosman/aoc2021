#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$crabs = collect(json_decode('['.file('input.txt')[0].']', true));
$min = $crabs->min();
$max = $crabs->max();
$avg = (int)round($crabs->avg());

$best = fuel($crabs,$avg);

for ($i=$avg-1; $i >= $min; $i--) {
    $fuel = fuel($crabs, $i);
    if ($fuel > $best) break;
    $best = $fuel;
}

for ($i=$avg+1; $i <= $min; $i++) {
    $fuel = fuel($crabs, $i);
    if ($fuel > $best) break;
    $best = $fuel;
}

output("best=" . $best);

function fuel($crabs, $pos) {
    return $crabs->reduce(fn($fuel, $crab) =>  $fuel + abs($crab-$pos)*(abs($crab-$pos)+1) / 2, 0);
}
