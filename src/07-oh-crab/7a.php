#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$crabs = collect(json_decode('['.file('input.txt')[0].']', true));
$median = $crabs->median();
output($crabs->reduce(fn($fuel,$crab) => $fuel + abs($crab-$median),0));
