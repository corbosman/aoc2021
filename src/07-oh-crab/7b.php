#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function fuel($crabs, $pos) : int
{
    return $crabs->reduce(fn($fuel, $crab) =>  $fuel + abs($crab-$pos)*(abs($crab-$pos)+1) / 2, 0);
}

$crabs = collect(json_decode('['.input('input.txt')[0].']', true));
$avg = $crabs->avg();

$best = min(fuel($crabs, (int)floor($crabs->avg())), fuel($crabs, (int)ceil($crabs->avg())));

$time2 = microtime(true);
solution($best, $time1, $time2, '7b');
