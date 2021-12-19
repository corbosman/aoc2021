#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

$count = collect(input('input.txt'))
    ->map(fn($i)=>explode(' ', preg_split('/ \| /', $i)[1]))->flatten()
    ->filter(fn($i)=>in_array(strlen($i),[2,3,4,7]))
    ->count();

$time2 = microtime(true);
solution($count, $time1, $time2, '8a');
