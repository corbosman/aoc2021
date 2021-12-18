#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function target_area()
{
    preg_match_all('/(-?[0-9]+)/', file('inputs/input.txt', FILE_IGNORE_NEW_LINES)[0], $matches);
    return map(fn($i)=>(int)$i, $matches[0]);
}

/*
 * find all possible x speeds for our landing site
 *
 * 1. A speed that is too low will never make it to the landing site
 * 2. So count from 1, until you hit an acceleration that brings us within our landing site
 * 3. All x speeds larger than this value until we hit the end of our landing site are candidates
 * 4. If a speed is larger than the end of our landing site we will miss it
 */
function vx_candidates(int $x1, int $x2) : array
{
    for($x=1; $x<$x1; $x++) {
        $max_x = $x*($x+1)/2;
        if ($max_x >= $x1 && $max_x <= $x2) break;
    }
    return range($x, $x2);
}

/*
 * Find all possible y values for our landing site
 *
 * 1. Find the max height for the landing site, similar to 17a.
 * 2. Now find all initial y values that stay below that height from 0,0
 */
function vy_candidates(int $y1, int $y2, int $max_height) : array
{
    $y = 0;
    do {
        $y++;
        $max_y = $y*($y+1)/2;
    } while ($max_y < $max_height);

    return range($y, $y1);
}

/*
 * simulate trajectories from within allowed vx and vy initial vectors
 */
function simulate(int $x1, int $x2, int $y1, int $y2, int $max_height) : int
{

    $vx_candidates = vx_candidates($x1, $x2);
    $vy_candidates = vy_candidates($y1, $y2, $max_height);

    $landed = [];
    foreach($vx_candidates as $vx_candidate) {
        foreach($vy_candidates as $vy_candidate) {
            $x = 0;
            $y = 0;
            $vx = $vx_candidate;
            $vy = $vy_candidate;

            while(true) {
                $x = $x + $vx;
                $y = $y + $vy;

                if ($x>=$x1 && $x<=$x2 && $y<=$y2 && $y>=$y1) {       // we landed on the square
                    $landed[] = [$x, $y];
                    break;
                }
                elseif ($x>$x2 || $y<$y1) break;                      // we shot behind or under
                elseif ($vx === 0 && $x<$x1) break;                   // we came up short

                $vx = $vx > 0 ? $vx-1 : 0;
                $vy = $vy - 1;
            }
        }
    }
    return count($landed);
}

$time1 = microtime(true);

[$x1, $x2, $y1, $y2] = target_area();

$max_height = abs($y1) * (abs($y1)-1)/2;
output("17a = {$max_height}");

$vector_count = simulate($x1, $x2, $y1, $y2, $max_height);
output("17b = {$vector_count}");

$time2 = microtime(true);
output("time = " . ($time2-$time1) * 1000 . " ms");
