#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$output = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
            ->sliding(3)
            ->map(fn($w)=>$w->sum())
            ->sliding(2)
            ->filter(fn($w)=>$w->first()<$w->last())
            ->count();

output($output);
