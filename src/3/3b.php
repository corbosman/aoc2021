#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(map(fn($line) => map('intval', str_split($line)), file('input.txt', FILE_IGNORE_NEW_LINES)));

$o2 = rating($input, 'o2');
$co2 = rating($input, 'co2');
$life_support = $o2*$co2;

output("o2={$o2} co2={$co2} life_support={$life_support}");

function rating($numbers, $rating, $pos = 0) : int
{
    if (count($numbers) === 1) return bindec(implode(null, $numbers[0]));

    $digit   = $rating === 'o2' ? 1 : 0;
    $bits    = map(fn(...$a) => $a, ...$numbers);
    $median  = count($numbers) / 2;
    $bit     = array_sum($bits[$pos]) >= $median ? (int)$digit : (int)!$digit;
    $numbers = $numbers->filter(fn($n) => $n[$pos] === $bit)->values();

    return rating($numbers, $rating, $pos+1);
}
