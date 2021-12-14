#!/usr/bin/env php
<?php

use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    $input = collect(file('input_e.txt', FILE_IGNORE_NEW_LINES));
    return [
        collect(str_split($input->first())),
        $input->splice(2)->mapWithKeys(fn($i)=>[$i[0].$i[1] => $i[6]])
    ];
}

function atoms(Collection $polymer) : Collection
{
    return $polymer->groupBy(fn($i)=>$i)->map(fn($i) => $i->count());
}

function insert(Collection $polymer, Collection $pairs) : Collection
{
    return $polymer->reduce(function($p,$atom, $cur) use ($polymer, $pairs) {
        if (!isset($polymer[$cur+1])) return $p->merge($atom);
        $inserted_atom = $pairs[$atom.$polymer[$cur+1]];
        return $p->merge([$atom, $inserted_atom]);
    }, collect());
}

function chain(Collection $polymer, Collection $pairs, int $steps) : Collection
{
    for($i=0; $i<$steps; $i++) {
        dump($i);
        $polymer = insert($polymer, $pairs);
    }
    return $polymer;
}

[$polymer, $pairs] = load();
$polymer = chain($polymer, $pairs, 40);
$atoms   = atoms($polymer);

output($atoms->max()-$atoms->min());
