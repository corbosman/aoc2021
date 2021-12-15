#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

const INFINITE = 9999999999999;

function load() : array
{
    $input = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>str_split($i));
    return $input->toArray();
}

function calculate_risk($cave) : array
{
    $height = count($cave);
    $width = count($cave[0]);
    $risk_map = [];

    for($x=$height-1; $x>=0; $x--) {
        for($y=$width-1; $y>=0; $y--) {
            $current_risk = $cave[$x][$y];

            $bottom_risk  = $x+1 >= $height ? null : $risk_map[$x+1][$y];
            $right_risk   = $y+1 >= $width ? null : $risk_map[$x][$y+1];

            if (!$bottom_risk && !$right_risk) {
                $lowest_risk = 0;
            } else {
                $lowest_risk = !$bottom_risk ? $right_risk : (!$right_risk ? $bottom_risk : min($bottom_risk, $right_risk));
            }
            $risk_map[$x][$y] = $current_risk + $lowest_risk;
        }
    }
    return $risk_map;
}

$cave = load();
$risk_map = calculate_risk($cave);
$risk_for_top_left = $risk_map[0][0] - $cave[0][0];

output($risk_for_top_left);

