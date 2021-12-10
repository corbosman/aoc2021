#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$count = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
    ->map(fn($i)=>explode(' ', preg_split('/ \| /', $i)[1]))->flatten()
    ->filter(fn($i)=>in_array(strlen($i),[2,3,4,7]))
    ->count();

output("count={$count}");
