#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$input = collect(map(fn($line) => map('intval', str_split($line)), input('input.txt')));

function rating($numbers, $rating, $pos = 0) : int
{
    if (count($numbers) === 1) return bindec(implode('', $numbers[0]));

    $digit   = $rating === 'o2' ? 1 : 0;
    $bits    = map(fn(...$a) => $a, ...$numbers);
    $median  = count($numbers) / 2;
    $bit     = sum($bits[$pos]) >= $median ? (int)$digit : (int)!$digit;
    $numbers = $numbers->filter(fn($n) => $n[$pos] === $bit)->values();

    return rating($numbers, $rating, $pos+1);
}

$o2 = rating($input, 'o2');
$co2 = rating($input, 'co2');
$life_support = $o2*$co2;

$time2 = microtime(true);

solution($life_support, $time1, $time2, '3b');

