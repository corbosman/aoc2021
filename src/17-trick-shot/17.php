#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function target_area()
{
    preg_match_all('/(\d+)/', file('inputs/input.txt', FILE_IGNORE_NEW_LINES)[0], $matches);
    return map(fn($i)=>(int)$i, $matches[0]);
}

[$x1, $x2, $y1, $y2] = target_area();

$max_height = abs($y1) * (abs($y1)-1)/2;

output("16a = {$max_height}");
