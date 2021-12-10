#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

$input = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>collect(str_split($i)));
$width = count($input[0])-1;
$height = count($input)-1;

$level = $input->mapWithKeys(fn($r,$i) => [$i => $r->filter(fn($h, $j) => ($i == 0 or $h < $input[$i-1][$j]) and
                                                                          ($j == 0 or $h < $input[$i][$j-1]) and
                                                                          ($j == $width or $h < $input[$i][$j+1]) and
                                                                          ($i == $height or $h < $input[$i+1][$j]))]
)->flatten()->map(fn($i)=>$i+1)->sum();

output($level);
