#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

const INFINITE = 9999999999999;

function load() : array
{
    $input = collect(file('input_e.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>str_split($i));
    $size = $input->count();
    $input = $input->flatten();
    $max = $input->count();
    return [$input->toArray(), $size, $max];
}

function calculate_risk($cave, $pos, $size, $max, &$cache) : int
{
    $risk = $cave[$pos];

    // if we reached bottom right, return it's risk
    if ($pos == ($max-1)) return $risk;

    // calculate down risk
    $down = $pos+$size;
    if ($down < $max) {
        $down_risk = $cache[$down] ?? calculate_risk($cave, $down, $size, $max, $cache);
    } else {
        $down_risk = INFINITE;
    }

    // calculate right risk
    $right = $pos+1;
    if ($right > $pos % $size) {
        $right_risk = $cache[$right] ?? calculate_risk($cave, $right, $size, $max, $cache);
    } else {
        $right_risk = INFINITE;
    }

    // which is the lowest?
    $lowest_risk = min($down_risk, $right_risk);
    $cache[$pos] = $risk + $lowest_risk;

    // print_cave($cave, $cache, $size, $max);

    return $risk + $lowest_risk;
}

[$cave, $size, $max] = load();
$cache = [];
$lowest_risk = calculate_risk($cave, 0, $size,$max, $cache);

output($lowest_risk - $cave[0]);

function print_cave($cave, $cache, $size, $max)
{
    for($pos=0; $pos<$max; $pos++) {
        $low = $cache[$pos] ?? '.';
        echo sprintf("%3s", $low);
        if ($pos % $size === 9) echo "\n";
    }
}
