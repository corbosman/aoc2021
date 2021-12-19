#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1=microtime(true);

$input = collect(input('input.txt'));

$position = $input->reduce(function($pos, $move) {
    [$direction, $distance] = explode(' ', $move);
    return match ($direction) {
        'forward'   => ['x' => $pos['x'] + (int) $distance, 'y' => $pos['y']],
        'up'        => ['x' => $pos['x'], 'y' => $pos['y'] - (int) $distance],
        'down'      => ['x' => $pos['x'], 'y' => $pos['y'] + (int) $distance]
    };
}, ['x' => 0, 'y' => 0]);

$time2=microtime(true);
solution($position['x'] * $position['y'], $time1, $time2, '2a');

