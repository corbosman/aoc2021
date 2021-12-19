#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$time1=microtime(true);

$a = collect(input('/input.txt'))
            ->sliding(2)
            ->filter(fn($w)=>$w->first()<$w->last())
            ->count();

$time2=microtime(true);

solution($a, $time1, $time2, '1a');
