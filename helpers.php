<?php

function output($str) : void
{
    print_r($str . "\n");
}

function map(callable $callback, array $array, array ...$arrays): array
{
    return array_map($callback, $array, ...$arrays);
}

function sum($array) : int|float
{
    return array_sum($array);
}

function reduce(array $array, callable $callback, mixed $initial) : array
{
    return array_reduce($array, $callback, $initial);
}

function reverse(array $array) : array
{
    return array_reverse($array);
}

function merge(...$arrays) : array {
    return array_merge(...$arrays);
}

function filter(array $array, callable $callback) : array
{
    return array_filter($array, $callback);
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

