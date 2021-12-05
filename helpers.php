<?php

function map(callable $callback, array $array, array ...$arrays): array {
    return array_map($callback, $array, ...$arrays);
}

function output($str) : void {
    print_r($str . "\n");
}

function sum($array) : int|float {
    return array_sum($array);
}
