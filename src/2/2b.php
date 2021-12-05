#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));

$position = $input->reduce(function($pos, $move) {
    [$direction, $distance] = explode(' ', $move);
    return match ($direction) {
        'forward'   => ['x' => $pos['x'] + (int)$distance, 'y' => ($pos['y'] + ((int)$distance * $pos['a'])), 'a' => $pos['a']],
        'up'        => ['x' => $pos['x'], 'y' => $pos['y'], 'a' => $pos['a'] - (int)$distance],
        'down'      => ['x' => $pos['x'], 'y' => $pos['y'], 'a' => $pos['a'] + (int)$distance]
    };
}, ['x' => 0, 'y' => 0, 'a' => 0]);

output($position['x'] * $position['y']);
