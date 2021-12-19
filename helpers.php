<?php

use function Termwind\{render};

function solution($a, $time1, $time2, $puzzle = null)
{
    $options = getopt("q");
    $timing  = round(($time2-$time1)*1000,2);

    if (isset($options['q'])) {
        output("{$a},{$timing},{$puzzle}");
    } else {
        $puzzle    = $puzzle ?: basename($_SERVER["SCRIPT_FILENAME"], '.php');
        $title     = pathinfo($_SERVER['PWD'])['filename'];
        $time      = round(($time2-$time1)*1000,2);

        render(<<<HTML
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>PUZZLE</th>
                            <th class='text-center'>TITLE</th>
                            <th class='text-center'>ANSWER</th>
                            <th class='text-center'>RUNTIME</th>
                        </tr>
                    </thead>
                    <tr class='text-center'>
                        <td class='text-center text-blue-400'>{$puzzle}</td>
                        <th class='text-center text-yellow-300'>{$title}</th>
                        <th class='text-center text-green-400'>{$a}</th>
                        <th class='text-center text-green-400'>{$time} ms</th>
                    </tr>
                </table>
            </div>
        HTML);
    }

}

function output($str) : void
{
    print_r($str . "\n");
}

function input($path) : array
{
    return file(realpath(dirname($_SERVER['PHP_SELF'])) . '/' .  $path, FILE_IGNORE_NEW_LINES);
}


function map(callable $callback, array $array, array ...$arrays): array
{
    return array_map($callback, $array, ...$arrays);
}

function sum($array) : int|float
{
    return array_sum($array);
}

function reduce(array $array, callable $callback, mixed $initial) : mixed
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

function filter(array $array, callable $callback, $mode = 0) : array
{
    return array_filter($array, $callback, $mode);
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

