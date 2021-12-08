#!/usr/bin/env php
<?php

use Tightenco\Collect\Support\Collection;

require __DIR__ . '/../../vendor/autoload.php';

$count = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
    ->map(fn($i)=>preg_split('/ \| /', $i))
    ->map(fn($i)=>[collect(explode(' ', $i[0])), collect(explode(' ', $i[1]))])
    ->map(fn($i)=>decrypt(find_cipher($i[0]), $i[1]))
    ->sum();

output("count={$count}");

/* -------------------------------------- */

/* find the substitution cipher by applying deduction rules */
function find_cipher($input) : array
{
    $cipher = array_fill_keys(range('a','g'), null);     // substitution cipher
    $freq = $input->groupBy(fn($i)=>strlen($i));         // frequency analyses of each letter

    /* perform a set of rules to fill our cipher */
    foreach(['a','g','d','b','e','f','c'] as $method) {
        $cipher[$method] = $method($cipher, $freq);
    }

    return array_flip($cipher);
}

/* decrypt an output string using the cipher */
function decrypt($cipher, $output) : int
{
    /* our plaintext digits */
    $digits = [
        'abcefg'  => 0,
        'cf'      => 1,
        'acdeg'   => 2,
        'acdfg'   => 3,
        'bcdf'    => 4,
        'abdfg'   => 5,
        'abdefg'  => 6,
        'acf'     => 7,
        'abcdefg' => 8,
        'abcdfg'  => 9
    ];

    return (int) $output->reduce(function($carry, $code) use ($cipher, $digits) {
        $digit = collect(map(fn($i)=>$cipher[$i], str_split($code)))->sort()->implode(null);    // character representation of this digit
        return $carry . $digits[$digit];                                                        // lookup table
    }, '');
}

/* all deduction rules */
function a($c, $f) { return find($f['2'][0], $f['3']);}
function b($c, $f) { return find($f['2'][0].$c['d'], $f['4']);}
function c($c, $f) { return find($c['a'].$c['b'].$c['d'].$c['e'].$c['f'].$c['g'], $f['7']);}
function d($c, $f) { return find($f['2'][0].$c['a'].$c['g'], $f['5']); }
function e($c, $f) { return find($f['2'][0].$c['a'].$c['b'].$c['d'].$c['g'],$f['7']);}
function f($c, $f) { return find($c['a'].$c['b'].$c['d'].$c['e'].$c['g'], $f['6']);}
function g($c, $f) { return find($f['4'][0].$f['3'][0], $f['6']);}

function find($str, $freq) {
    foreach($freq as $possible) {
        $diff = str_diff(str_uniq($str), $possible);
        if (strlen($diff) === 1) return $diff[0];
    }
}


