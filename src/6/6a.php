#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$fish  = array_fill(0,9,0);
foreach(json_decode('['.file('input.txt')[0].']', true) as $i) $fish[$i]++;

for($i=1; $i <= 80; $i++) {
    $breading = $fish[0];
    for ($f=1; $f<=8; $f++) $fish[$f-1] = $fish[$f];
    $fish[6] += $breading;
    $fish[8] = $breading;
}
output("day80=". array_sum($fish));
