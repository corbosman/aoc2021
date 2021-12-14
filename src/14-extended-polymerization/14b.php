#!/usr/bin/env php
<?php

use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    $input = collect(file('input.txt', FILE_IGNORE_NEW_LINES));
    return [
        collect(str_split($input->first())),
        $input->splice(2)->mapWithKeys(fn($i)=>[$i[0].$i[1] => [$i[0].$i[6], $i[6].$i[1]]])->toArray()
    ];
}

function atomize($polymer) : array
{
    return $polymer->reduce(function($atoms, $atom) {
        $atoms[$atom] = !isset($atoms[$atom]) ? 1 : $atoms[$atom] + 1;
        return $atoms;
    },[]);
}

function pairs($polymer)
{
    return $polymer->sliding(2)->map->implode(null)->groupBy(fn($i)=>$i)->map->count()->toArray();
}

function steps($rules, $pairs, $atoms, $steps)
{
    for ($i=1;$i<=$steps;$i++) {
        $new_pairs = [];
        foreach($pairs as $pair => $count) {
            // we have an extra atom
            $atom = $rules[$pair][0][1];
            $atoms[$atom] = (isset($atoms[$atom])) ? $atoms[$atom] + $count : $count;

            // create new pairs based on original pair
            foreach($rules[$pair] as $rule) {
                if(isset($new_pairs[$rule])) {
                    $new_pairs[$rule] += $count;
                } else {
                   $new_pairs[$rule] = $count;
                }
            }
        }
        $pairs = $new_pairs;
    }
    return $atoms;
}

[$polymer, $rules] = load();
$pairs = pairs($polymer);
$atoms = atomize($polymer);
$atoms = steps($rules, $pairs, $atoms, 40);

output(max($atoms)-min($atoms));
