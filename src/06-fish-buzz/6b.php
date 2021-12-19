#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

const DAYS = 256;

$fish  = array_fill(0,9,0);
foreach(json_decode('['.input('input.txt')[0].']', true) as $i) $fish[$i]++;

for($i=1; $i <= DAYS; $i++) {
    $breeding = array_shift($fish);
    $fish[6] += $breeding;
    $fish[8] = $breeding;
}

$time2 = microtime(true);
solution(sum($fish), $time1, $time2, '6b');
