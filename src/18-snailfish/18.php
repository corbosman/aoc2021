#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function snailfish() : Collection
{
    return collect(file('inputs/input_e12.txt', FILE_IGNORE_NEW_LINES))->map(fn($line) => collect(str_split($line)));
}

// parse a line as an array as values and their depth in the array
function parse($line) : Collection
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

function explode_snailfishes(Collection $values) : array
{
    $exploded = false;
    $values = $values->toArray();
    foreach($values as $i => list($value, $depth)) {
        if ($depth > 4) {
            if ($i > 0) {
                $values[$i-1][0] += $value;
            }
            if ($i+2<(count($values))) {
                $values[$i+2][0] += $values[$i+1][0];
            }
            array_splice($values, $i, 2, [[0, $depth - 1 ]]);
            $values = array_values($values);
            $exploded = true;
            break;
        }
    }
    $values = collect($values);
    return [$values, $exploded];
}

function split_snailfishes($values) : array
{
    $split = false;
    $values = $values->toArray();
    foreach($values as $i => $value) {
        if ($value[0] >= 10) {
            $replacement = [[floor($value[0]/2), $value[1]+1], [ceil($value[0]/2), $value[1]+1]];
            array_splice($values, $i, 1, $replacement);
            $values = array_values($values);
            $split = true;
            break;
        }
    }
    return [collect($values), $split];
}

function calculate_magnitude(Collection $values) : int
{
    $values = $values->toArray();
    while(count($values) > 1) {
        foreach(range(0, count($values)-1) as $i) {
            // find a matching pair
            if($values[$i][1] === $values[$i+1][1]) {
                $value = ($values[$i][0]*3) + ($values[$i+1][0]*2);
                array_splice($values, $i, 2, [[$value, $values[$i][1] - 1]]);
                break;
            }
        }
    }
    return $values[0][0];
}

function calculate_max_magnitude(Collection $snailfish) : int
{
    $magnitudes = [];
    foreach($snailfish as $i => $fish) {
        foreach($snailfish as $j => $fish) {
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
        while(true) {
            [$values, $exploded] = explode_snailfishes($values);
            if ($exploded) continue;

            [$values, $split] = split_snailfishes($values);
            if ($split) {
                continue;
            }

            break;
        }
    }
    return $values;
}

$time1         = microtime(true);
$values        = process(snailfish());
$magnitude     = calculate_magnitude($values);
$max_magnitude = calculate_max_magnitude(snailfish());
$time2         = microtime(true);

output("17a={$magnitude}");
output("17b={$max_magnitude}");
output("time = " . round(($time2-$time1) * 1000,0) . " ms");
