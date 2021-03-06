#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function snailfish() : Collection
{
    return collect(input('inputs/input.txt'))->map(fn($line) => collect(str_split($line)));
}

// parse a line as an array as values and their depth in the array
function parse(Collection $line) : Collection
{
    $snailfish = [];
    $depth = 0;
    foreach($line as $l) {
        switch($l) {
            case '[':
                $depth += 1;
                break;
            case ']':
                $depth -= 1;
                break;
            case ',':
                break;
            default:
                $snailfish[] = [(int)$l, (int)$depth];
        }
    }
    return collect($snailfish);
}

function explode_snailfishes(Collection &$values) : bool
{
    foreach($values as $i => list($value, $depth)) {
        if ($depth > 4) {
            // current value added to previous value (if exists)
            if ($i > 0) $values->splice($i-1, 1, [[$values[$i-1][0] + $value, $values[$i-1][1]]]);
            // current value is added to the next value (if exists)
            if ($i+2<(count($values))) $values->splice($i+2, 1, [[$values[$i+2][0] + $values[$i+1][0], $values[$i+2][1]]]);
            // the current pair is imploded to a new pair with value 0 at a lower depth
            $values->splice($i, 2, [[0, $depth - 1 ]]);

            return true;
        }
    }
    return false;
}

function split_snailfishes(Collection &$values) : bool
{
    foreach($values as $i => $value) {
        // loop over all the value/depth pairs, the first pair we find that has a value higher than 10, replace it with
        // 2 new pairs at a higher depth
        if ($value[0] >= 10) {
            $values->splice($i, 1, [[floor($value[0]/2), $value[1]+1], [ceil($value[0]/2), $value[1]+1]]);
            return true;
        }
    }
    return false;
}

function calculate_magnitude(Collection $values) : int
{
    while(count($values) > 1) {
        foreach(range(0, count($values)-1) as $i) {
            if($values[$i][1] === $values[$i+1][1]) {
                $value = ($values[$i][0]*3) + ($values[$i+1][0]*2);
                $values->splice($i, 2, [[$value, $values[$i][1] - 1]]);
                break;
            }
        }
    }
    return $values[0][0];
}

function calculate_max_magnitude(Collection $snailfish) : int
{
    $magnitudes = [];
    foreach($snailfish as $i => $fish1) {
        foreach($snailfish as $j => $fish2) {
            if ($i === $j) continue;
            $pair = collect([$snailfish[$i], $snailfish[$j]]);
            $magnitudes[] = calculate_magnitude(process($pair));
        }
    }
    return max($magnitudes);
}

function process($input)
{
    // take the first line and parse it as an array of values and their depth
    $values = parse($input->first());

    // now parse the rest one by one and add the result
    foreach($input->splice(1) as $line) {
        $next_line = parse($line);

        // to combine the 2 lines, just add the array, and then increase the depth for all items with 1.
        $values = $values->concat($next_line)->map(fn($i) => [$i[0], $i[1]+1]);

        // now explode and/or split the resulting combined array
        while(explode_snailfishes($values) || split_snailfishes($values)) continue;
    }
    return $values;
}

$time1         = microtime(true);
$values        = process(snailfish());
$magnitude     = calculate_magnitude($values);
$time2         = microtime(true);
solution($magnitude, $time1, $time2, '18a');

$time1         = microtime(true);
$max_magnitude = calculate_max_magnitude(snailfish());
$time2         = microtime(true);
solution($max_magnitude, $time1, $time2, '18b');
