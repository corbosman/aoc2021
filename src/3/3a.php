#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = map(fn($line) => map('intval', str_split($line)), file('input.txt', FILE_IGNORE_NEW_LINES));
$median = count($input) / 2;
$gamma = bindec(implode(null,map(fn($a) => array_sum($a) > $median ? 1 : 0, map(fn(...$a) => $a, ...$input))));
$epsilon = $gamma ^ (pow(2, strlen(decbin($gamma))) - 1);
output($gamma * $epsilon);

