#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$crabs = collect(json_decode('['.file('input_e.txt')[0].']', true));
$avg = (int)round($crabs->avg());

$best = min(fuel($crabs, (int)floor($crabs->avg())), fuel($crabs, (int)ceil($crabs->avg())));

output("best=" . $best);

function fuel($crabs, $pos) : int
{
    return $crabs->reduce(fn($fuel, $crab) =>  $fuel + abs($crab-$pos)*(abs($crab-$pos)+1) / 2, 0);
}
