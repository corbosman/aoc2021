#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function load() : array
{
    $input = collect(input('input.txt'));
    return [
        collect(str_split($input->first())),
        $input->splice(2)->mapWithKeys(fn($i)=>[$i[0].$i[1] => [$i[0].$i[6], $i[6].$i[1]]])->toArray()
    ];
}

function atomize(iterable $polymer) : array
{
    return $polymer->countBy()->toArray();
}

function pairs(iterable $polymer) : array
{
    return $polymer->sliding(2)->map->implode(null)->countBy()->toArray();
}

function steps(iterable $rules, iterable $pairs, iterable $atoms, int $steps) : array
{
    for ($i=1;$i<=$steps;$i++) {
        foreach($pairs as $pair => $count) {
            // we have an extra atom, add it to our list of atoms
            $atom = $rules[$pair][0][1];
            $atoms[$atom] = (isset($atoms[$atom])) ? $atoms[$atom] + $count : $count;

            // remove count from this pair
            $pairs[$pair] -= $count;

            // create new pairs based on original pair, NN => NC CN  , NN NN => NC CN NC CN
            foreach($rules[$pair] as $rule) {
                $pairs[$rule] = isset($pairs[$rule]) ? $pairs[$rule] + $count : $count;
            }
        }
    }
    return [$atoms, $pairs];
}

[$polymer, $rules] = load();
$pairs = pairs($polymer);
$atoms = atomize($polymer);
$time2 = microtime(true);
[$atoms, $pairs] = steps($rules, $pairs, $atoms, 10);
solution(max($atoms)-min($atoms), $time1, $time2, '14a');

$time3 = microtime(true);
[$atoms, $pairs] = steps($rules, $pairs, $atoms, 30);
$time4 = microtime(true);

solution(max($atoms)-min($atoms), $time3, $time4, '14b');
