#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

const DAYS = 80;

$fish  = array_fill(0,9,0);
foreach(json_decode('['.file('input.txt')[0].']', true) as $i) $fish[$i]++;

for($i=1; $i <= DAYS; $i++) {
    $breeding = array_shift($fish);
    $fish[6] += $breeding;
    $fish[8] = $breeding;
}
output("fish=". sum($fish));
