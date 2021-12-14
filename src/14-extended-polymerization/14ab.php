#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    $input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));
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
        $new_pairs = [];
        foreach($pairs as $pair => $count) {
            // we have an extra atom, add it to our list of atoms
            $atom = $rules[$pair][0][1];
            $atoms[$atom] = (isset($atoms[$atom])) ? $atoms[$atom] + $count : $count;

            // create new pairs based on original pair, NN => NC CN  , NN NN => NC CN NC CN
            foreach($rules[$pair] as $rule) {
                $new_pairs[$rule] = isset($new_pairs[$rule]) ? $new_pairs[$rule] + $count : $count;
            }
        }
        $pairs = $new_pairs;
    }
    return [$atoms, $pairs];
}

[$polymer, $rules] = load();
$pairs = pairs($polymer);
$atoms = atomize($polymer);

[$atoms, $pairs] = steps($rules, $pairs, $atoms, 10);
output("10=" . max($atoms)-min($atoms));

[$atoms, $pairs] = steps($rules, $pairs, $atoms, 30);
output("40=" . max($atoms)-min($atoms));
