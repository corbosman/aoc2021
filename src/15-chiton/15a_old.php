#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

const INFINITE = 999999999999999;

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

function find_lowest_risk(array $cave, $x, $y, $path, $prev_risk, $size, &$map, &$lowest_risk, &$loop, $old_x, $old_y) : void
{
    print_risk_map($cave, $map);

    $current_risk  = $cave[$x][$y];
    $current_total = $prev_risk + $current_risk;
    $cached_total = $map[$x][$y] ?? INFINITE;

    // if ($loop === 45) exit;
    dump("pos = {$x},{$y} cur_total={$current_total} current_value={$current_risk}  global shortest: {$lowest_risk}");
    dump("from = {$old_x},{$old_y}");
    $path[$x][$y] = 1;


    if ($cached_total > $current_total) {
        dump("new lowest risk for {$x},{$y} = {$current_total}");
        $map[$x][$y] = $current_total;
    }

    /* got to the end */
    if ($x === $size - 1 && $y === $size - 1) {
        dump("got to the end");
        if ($current_total < $lowest_risk) {
            dump("new lowest total: " . $current_total);
            $lowest_risk = $current_total;
        }
        return;
    }

    $risks = [];
    $deltas = [
        [1,0],   // down
        [0,1],   // right
        [-1,0],  // up
        [0,-1],   // left
    ];

    $next_risks = collect();
    dump("checking next..");
    foreach($deltas as $d) {
        $next_x = $x+$d[0];
        $next_y = $y+$d[1];
        dump("next first: {$next_x},{$next_y}");
        if ($next_x < 0 || $next_x > $size-1 || $next_y < 0 || $next_y > $size-1) {
            dump("out of bounds, skipping");
            continue;
        }

        $next_risk = $cave[$next_x][$next_y];
        $next_risks->push(['coordinates' => [$next_x, $next_y], 'risk' => $next_risk]);
    }


    $next_risks = $next_risks->sortBy('risk')->toArray();
    foreach($next_risks as $r) {
        $loop = $loop+1;

        $next_x = $r['coordinates'][0];
        $next_y = $r['coordinates'][1];
        dump("next second: {$next_x},{$next_y}");

        // crossed path, skip
        if (isset($path[$next_x][$next_y])) {
            dump("previously visited, skipping");
            continue;
        }

        // at bottom, dont turn left
        if ($x === ($size-1) && $next_y < $y) {
            dump("at bottom, dont turn left");
            continue;
        }

        // at right, dont turn up
        dump("y={$y} size={$size} next_x={$next_x}");
        if ($y === ($size-1) and $next_x < $x) {
            dump("at right, dont turn up");
            continue;
        }

        // at top, dont turn left
        if ($x === 0 && $next_y < $y) {
            dump("at top, dont turn left");
            continue;
        }

        // at left, dont turn up
        if ($y === 0 and $next_x < $x) {
            dump("at left, dont turn up");
            continue;
        }

        $next_cached_total = $map[$next_x][$next_y] ?? INFINITE;
        dump("next cached total for {$next_x},{$next_y} = {$next_cached_total}");
        $next_total = $current_total + $cave[$next_x][$next_y];
        dump("next calculated for {$next_x},{$next_y} = {$next_total}");

        if ($next_cached_total < $next_total) $next_total = $next_cached_total;

        // dont try this if it wouldn't lower the total for that position
        if ($next_total >= $lowest_risk) {
            dump("{$next_x},{$next_y} already higher or equal to global lowest, skipping");
            continue;
        }

        dump("current_total={$current_total}, next_total={$next_total}");
        if ($next_total <= $current_total) {
            dump("{$next_x},{$next_y} already higher or equal to current, skipping");
            dump("already higher or equal to current, skipping");
            continue;
        }

        // if the difference in distance is too high, skip
//        $distance = ($size - $next_x) + ($size - $next_y);
//        if ($next_total + $distance >= $lowest_risk) {
//            dump("no point, distance is too high, skipping");
//            continue;
//        }

        find_lowest_risk($cave, $r['coordinates'][0], $r['coordinates'][1], $path, $current_total, $size, $map, $lowest_risk, $loop, $x, $y);
    }
}

$cave = load();
$size = count($cave);
$map = [0 => [0 => $cave[0][0]]];
$lowest_risk = INFINITE;
$loop = 1;
$path = [];

find_lowest_risk($cave, 0, 0, $path, 0, $size, $map, $lowest_risk, $loop, 0, 0);
print_risk_map($cave, $map);

output("15a = " . $map[$size-1][$size-1] - $map[0][0]);

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
    for($i=0;$i<count($cave); $i++){
        echo sprintf("%5s", $i);
    }
    echo "\n--------------------------------------------------------\n";
    for($x=0; $x<count($risk_map); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            $risk = $risk_map[$x][$y] ?? '.';
            echo sprintf("%5s", $risk);
        }
        echo " | $x";
        echo "\n";
    }
}
