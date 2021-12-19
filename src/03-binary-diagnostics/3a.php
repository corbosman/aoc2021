#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$time1 = microtime(true);

$input = map(fn($line) => map('intval', str_split($line)), input('input.txt'));
$median = count($input) / 2;
$gamma = bindec(implode('',map(fn($a) => array_sum($a) > $median ? 1 : 0, map(fn(...$a) => $a, ...$input))));
$epsilon = $gamma ^ (pow(2, strlen(decbin($gamma))) - 1);

$time2 = microtime(true);

solution($gamma * $epsilon, $time1, $time2, '3a');

