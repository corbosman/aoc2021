#!/usr/bin/env php
<?php
use cash\LRUCache;

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : array
{
    $file = input('inputs/input.txt');
    $player1 = $file[0][strlen($file[0])-1]-1;
    $player2 = $file[1][strlen($file[1])-1]-1;
    return [$player1, $player2];
}
