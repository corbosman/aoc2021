#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    $input = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>str_split($i));
    return $input->toArray();
}

function expand(array $cave) : array
{
    $size = count($cave);
    $expanded = [];

    for($i=0;$i<5;$i++) {
        for($j=0;$j<5;$j++) {
            $add = $i+$j;
            foreach($cave as $x => $row) {
                foreach ($row as $y => $v) {
                    $risk = ($cave[$x][$y]+$add);
                    while($risk > 9) $risk = $risk - 9;
                    $expanded[$x+($i*$size)][$y+($j*$size)] = $risk;
                }
            }
        }
    }
    return $expanded;
}

function lowest(?int $risk1, ?int $risk2) : int
{
    if (!$risk1 && !$risk2) return 0;
    if (!$risk1) return $risk2;
    if (!$risk2) return $risk1;
    return min($risk1, $risk2);
}

function lowest_risk_level(array $cave) : int
{
    $max      = count($cave)-1;
    $risk_map = [];

    for($x=0; $x<=$max; $x++) {
        for($y=0; $y<=$max; $y++) {
            $current_risk = $cave[$x][$y];

            $top_risk  = $x-1<0 ? null : $risk_map[$x-1][$y];
            $left_risk = $y-1<0 ? null : $risk_map[$x][$y-1];

            $risk_map[$x][$y] = $current_risk + lowest($left_risk, $top_risk);
        }
    }
    print_risk_map($cave, $risk_map);
    return $risk_map[$max][$max] - $cave[0][0];
}

$cave = load();
//$risk = lowest_risk_level($cave);
//output("15a = {$risk}");

$cave = expand($cave);
$risk2 = lowest_risk_level($cave);
output("15b = {$risk2}");


function print_cave($cave)
{
    for($x=0; $x<count($cave); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            echo sprintf("%1s", $cave[$x][$y]);
        }
        echo "\n";
    }
}

function print_risk_map($cave, $risk_map)
{
    for($x=0; $x<count($cave); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            $risk = $risk_map[$x][$y] ?? '.';
            echo sprintf("%5s", $risk);
        }
        echo "\n";
    }
}
