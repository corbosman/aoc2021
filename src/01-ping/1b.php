#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$time1=microtime(true);

$output = collect(input('input.txt'))
    ->sliding(3)
    ->map(fn($w)=>$w->sum())
    ->sliding(2)
    ->filter(fn($w)=>$w->first()<$w->last())
    ->count();

$time2=microtime(true);

solution($output, $time1, $time2, '1b');
