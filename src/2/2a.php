#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));

$position = $input->reduce(function($pos, $move) {
    [$direction, $distance] = explode(' ', $move);
    return match ($direction) {
        'forward'   => ['x' => $pos['x'] + (int) $distance, 'y' => $pos['y']],
        'up'        => ['x' => $pos['x'], 'y' => $pos['y'] - (int) $distance],
        'down'      => ['x' => $pos['x'], 'y' => $pos['y'] + (int) $distance]
    };
}, ['x' => 0, 'y' => 0]);

output($position['x'] * $position['y']);

