<?php

function map(callable $callback, array $array, array ...$arrays): array {
    return array_map($callback, $array, $arrays);
}
