#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$crabs  = collect(json_decode('['.input('input.txt')[0].']', true));
$median = $crabs->median();
$count  = $crabs->reduce(fn($fuel,$crab) => $fuel + abs($crab-$median),0);

$time2 = microtime(true);
solution($count, $time1, $time2, '7a');
