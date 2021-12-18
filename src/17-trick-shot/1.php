<?php

$coords = [];
preg_match('/x=(-?\d+)..(-?\d+), y=(-?\d+)..(-?\d+)/', file_get_contents('inputs/input.txt'), $coords);

$tx = [$coords[1], $coords[2]];
$ty = [$coords[3], $coords[4]];
sort($tx);
rsort($ty);

// the velocity evolution is basically a nth triangle, so anything under that will never reach the target on x direction
$nthTriangle = fn ($i) => $i * ($i + 1) / 2;

if ($tx[0] < 0 && $tx[1] > 0) {
    // if target x0 is negative and x1 is positive, scan from x0 to x1
    $xRange = range($tx[0], $tx[1]);
} elseif ($tx[1] <= 0) {
    // if target x0 and x1 are negative, scan from 1st nth triangle < x0, to x1
    $x = 0;
    while (true) {
        $projectedX = $nthTriangle($x);
        if (ceil($projectedX) >= $tx[0] && floor($projectedX) <= $tx[1]) {
            break;
        }
        $x--;
    }
    $xRange = range($tx[0], $x);
    rsort($tx); // swap x0 and x1 in order to make an absolute comparison later
} else {
    // if target x0 and x1 are positive, scan from 1st nth triangle > x0, to x1
    $x = 0;
    while (true) {
        $projectedX = $nthTriangle($x);
        if (ceil($projectedX) >= $tx[0] && floor($projectedX) <= $tx[1]) {
            break;
        }
        $x++;
    }
    $xRange = range($x, $tx[1]);
}

var_dump($xRange);

$heights = [];
foreach($xRange as $vX) {
    $vY = -abs($ty[1]);
    while ($vY < abs($ty[1])) {
        $probe = ['vX' => $vX, 'vY' => $vY, 'x' => 0, 'y' => 0];
        $height = 0;
        while (true) {
            $probe['x'] += $probe['vX'];
            $probe['y'] += $probe['vY'];
            if ($probe['vX'] !== 0) $probe['vX'] += (($probe['vX'] > 0) ? -1 : 1);
            $probe['vY']--;
            $height = max($height, $probe['y']);
            if ($probe['y'] < $ty[1] || $probe['x'] > abs($tx[1])) {
                break;
            }
            if ($probe['y'] <= $ty[0] && $probe['x'] >= abs($tx[0])) {
                $heights[$vY . '_' . $vX] = $height;
                break;
            }
        }
        $vY++;
    }
}
arsort($heights);
var_dump($heights);
echo 'Part 1: ' . reset($heights).PHP_EOL;
echo 'Part 2: ' . count($heights);