#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$count = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
            ->sliding(2)
            ->filter(fn($w)=>$w->first()<$w->last())
            ->count();

output($count);
