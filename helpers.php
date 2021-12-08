<?php

function map(callable $callback, array $array, array ...$arrays): array
{
    return array_map($callback, $array, ...$arrays);
}

function output($str) : void
{
    print_r($str . "\n");
}

function sum($array) : int|float
{
    return array_sum($array);
}

function str_diff($str1, $str2) : string
{
    return strlen($str1) > strlen($str2) ?
        implode('', array_values(array_diff(str_split($str1), str_split($str2)))) :
        implode('', array_values(array_diff(str_split($str2), str_split($str1))));
}

function str_uniq($str) : string
{
    return implode('',array_unique(str_split($str)));
}

