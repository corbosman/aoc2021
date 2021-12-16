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

function create_unvisited(array $cave) : array
{
    // initialise the first unvisited distance
    $unvisited[0][0] = $cave[0][0];
    return $unvisited;
}

function next_node(array $unvisited) : array
{
    $next = [];
    $min = INFINITE;
    foreach($unvisited as $x => $row) {
        foreach($row as $y => $distance) {
            if ($distance < $min) {
                $min = $distance;
                $next = [$x,$y];
            }
        }
    }
    return $next;
 }

 function register_visit($x, $y, &$visited, &$unvisited) : void
 {
     $visited[$x][$y] = true;
     unset($unvisited[$x][$y]);
     if (count($unvisited[$x]) === 0) unset($unvisited[$x]);
 }

function neighbors(array $cave, int $x, int $y, int $width, int $height, $visited) : array
{
    $neighbors = [];

    foreach ([[1,0], [0,1], [-1,0], [0,-1]] as list($dx, $dy)) {
        if ($x+$dx >= 0 && $x+$dx < $height && $y+$dy >= 0 && $y+$dy < $height) {
            $neighbors[] = [$x+$dx, $y+$dy];
        }
    }

    return $neighbors;
}

function dijkstra(array $cave, $x, $y) : int
{
    $unvisited = create_unvisited($cave);

    //dump("unvisited=" . count($unvisited));
    $loop = 1;
    $visited   = [];
    $distances = [0=>[0=>1]];
    $width     = count($cave[0]);
    $height    = count($cave);
    $tx        = $height-1;
    $ty        = $width-1;

    while (count($unvisited) > 0) {

        if ($x == $tx && $y == $ty) {
            dump("reached end node");
            return $distances[$tx][$ty] - $distances[0][0];
        }

        // find all the neighbors for the current position
        $neighbors = neighbors($cave, $x, $y, $width, $height, $visited);

        // foreach neighbor, calculate the new distance and check with previous known minimal distance
        foreach ($neighbors as list($nx, $ny)) {
            dump("neighbor {$nx},{$ny}");
            if (isset($visited[$nx][$ny])) {
                dump("already visited {$nx},{$ny}");
                continue;
            }

            $distance   = $distances[$x][$y] + $cave[$nx][$ny];
            $distance_n = $distances[$nx][$ny] ?? INFINITE;
            if ($distance < $distance_n) {
                $distances[$nx][$ny] = $distance;
                $unvisited[$nx][$ny] = $distance;
            }
        }

        // mark current node as visited
        register_visit($x, $y, $visited, $unvisited);

        if (count($unvisited) > 0) {
            list($x, $y) = next_node($unvisited);
        }
        // print_risk_map($cave, $distances);
    }

    // print_risk_map($cave, $distances);

    return $distances[$height-1][$width-1] - $distances[0][0];
}

$cave = load();
$minimum_risk = dijkstra($cave, 0, 0);
output("15a={$minimum_risk}");

$cave = expand($cave);
$minimum_risk = dijkstra($cave, 0, 0);
output("15b={$minimum_risk}");



function print_cave($cave)
{
    for($x=0; $x<count($cave); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            echo sprintf("%1s", $cave[$x][$y]);
        }
        echo "\n";
    }
}

function print_risk_map($cave, $distances)
{
    for($i=0;$i<count($cave); $i++){
        echo sprintf("%5s", $i);
    }
    echo "\n--------------------------------------------------------\n";
    for($x=0; $x<count($distances); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            $risk = $distances[$x][$y] ?? '.';
            echo sprintf("%5s", $risk);
        }
        echo " | $x";
        echo "\n";
    }
    echo "\n";
}
