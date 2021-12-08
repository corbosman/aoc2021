#!/usr/bin/env php
<?php

use Tightenco\Collect\Support\Collection;

require __DIR__ . '/../../vendor/autoload.php';

$count = collect(file('input.txt', FILE_IGNORE_NEW_LINES))
    ->map(fn($i)=>preg_split('/ \| /', $i))
    ->map(fn($i)=>decode(collect(explode(' ', $i[0])), collect(explode(' ', $i[1]))))
    ->sum();

output("count={$count}");

/* -------------------------------------- */


/**
 * Decode a line of input, returns decoded output value
 *
 * @param $input
 * @param $output
 * @return int
 */
function decode($input, $output) : int
{
    return decrypt($output, find_cipher($input));
}

/* find the cipher by applying deduction rules */
function find_cipher($input) : array
{
    $cipher = array_fill_keys(range('a','g'), null);        // substitution cipher
    $counter = $input->groupBy(fn($i)=>strlen($i));         // frequency analyses of each letter

    /* perform a set of rules to find our cipher */
    foreach(['a','g','d','b','e','f','c'] as $method) {
        $cipher = $method($cipher, $counter);
    }

    return array_flip($cipher);
}

/* decrypt an output string using the cipher */
function decrypt($output, $cipher) : int
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
        $carry .= $digits[$digit];                                                              // lookup digit from table
        return $carry;
    }, '');
}


/* all deduction rules */

/* a can be deduced from digit 1 and 7. */
function a($cipher, $counter) : array
{
    $cipher['a'] = array_values(array_diff(str_split($counter['3']->first()), str_split($counter['2']->first())))[0];
    return $cipher;
}

/* g can be deduced from merging digits 4 (4 letters) and 7 (3 letters). Compare with all 6 letter numbers, and if there is 1 letter difference, that is the g */
function g($cipher, $counter) : array
{
    $combined = array_unique(array_merge(str_split($counter['4']->first()), str_split($counter['3']->first())));

    foreach($counter['6'] as $possible_g) {
        $diff = array_diff(str_split($possible_g), $combined);
        if (count($diff) === 1) {
            $cipher['g'] = array_values($diff)[0];
            return $cipher;
        }
    }
    output('g failure!');
    exit;
}

/* d can be deduced from merging digit 1 (2 letters), with known letters a and g. Compare with all 5 letter numbers, and if there is 1 letter difference, that is d */
function d($cipher, $counter) : array
{
    $combined = array_merge(str_split($counter['2']->first()), [$cipher['a'], $cipher['g']]);
    foreach($counter['5'] as $possible_d) {
        $diff = array_diff(str_split($possible_d), $combined);
        if (count($diff) === 1) {
            $cipher['d'] = array_values($diff)[0];
            return $cipher;
        }
    }
    output('d failure!');
    exit;
}

/* b can be deduced by combining digit 1 (2 letters), with known letter d. Remaining letter for digit 4 is b */
function b($cipher, $counter) : array
{
    $combined = array_merge(str_split($counter['2']->first()), [$cipher['d']]);
    $cipher['b'] = array_values(array_diff(str_split($counter['4']->first()), $combined))[0];
    return $cipher;
}

/* e can be deduced by combining digit 1 (2 letters) with known letters abdg and comparing that with digit 8 (7 letters). */
function e($cipher, $counter) : array
{
    $combined = array_merge(str_split($counter['2']->first()), [$cipher['a'],$cipher['b'],$cipher['d'],$cipher['g']]);
    $cipher['e'] = array_values(array_diff(str_split($counter['7']->first()), $combined))[0];
    return $cipher;
}

/* f can be deduced by checking 6 letter digits with known letters abdeg.  */
function f($cipher, $counter) : array
{
    $combined = [$cipher['a'],$cipher['b'],$cipher['d'],$cipher['e'],$cipher['g']];
    foreach($counter['6'] as $possible_f) {
        $diff = array_diff(str_split($possible_f), $combined);
        if (count($diff) === 1) {
            $cipher['f'] = array_values($diff)[0];
            return $cipher;
        }
    }
    output('rule_cf failure!');
    exit;
}

/* c can deduced by checking which letter is missing */
function c($cipher, $counter) : array
{
    $combined = [$cipher['a'],$cipher['b'],$cipher['d'],$cipher['e'],$cipher['f'],$cipher['g']];
    $cipher['c'] = array_values(array_diff(str_split($counter['7']->first()), $combined))[0];
    return $cipher;
}
