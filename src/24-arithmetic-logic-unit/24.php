#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;

const UPPER  = 0;   /* this is an alu block that increases the value of Z */
const DOWNER = 1;   /* this is an alu block that decreases the value of Z */

require __DIR__ . '/../../vendor/autoload.php';

function load() : Collection
{
    return collect(input('inputs/input.txt'))->chunk(18)->map(fn($c) => $c->values())->map(function($alu) {
        if (str_contains($alu[5], '-')) return [DOWNER, (int)substr($alu[5], 6), (int)substr($alu[15], 6)];
        return [UPPER, (int)substr($alu[5], 6), (int)substr($alu[15], 6)];
    });
}

class CrackSerial {
    public function crack($alu, $part = 'a') : string
    {
        $min = $part === 'a' ? 9 : 1;
        $max = $part === 'a' ? 1 : 9;

        /* read the strategy.md for explanation of this loop */
        foreach($this->serials($min, $max) as $w) {
            $z = 0;
            $serial = [];
            $w_index = 0;
            foreach($alu as $step => $a) {
                if ($a[0] === UPPER) {
                    $z = ($z*26) + $w[$w_index] + $a[2];
                    $serial[] = $w[$w_index++];
                } else {
                    $w_generated = ($z % 26) + $a[1];
                    if ($w_generated < 1 || $w_generated > 9) continue 2;
                    $serial[] = $w_generated;
                    $z = floor($z / 26);
                }
            }
            if ($z == 0) return implode('', $serial);
        }
        return "meh";
    }

    public function serials($min = 1, $max = 9) : Generator
    {
        foreach (range($min, $max) as $a)
            foreach (range($min, $max) as $b)
                foreach (range($min, $max) as $c)
                    foreach (range($min, $max) as $d)
                        foreach (range($min, $max) as $e)
                            foreach (range($min, $max) as $f)
                                foreach (range($min, $max) as $g)
                                    yield [$a,$b,$c,$d,$e,$f,$g];
    }
}

$alu    = load();
$time1  = microtime(true);
$high   = (new CrackSerial())->crack($alu, 'a');
$time2  = microtime(true);
$low    = (new CrackSerial())->crack($alu, 'b');
$time3  = microtime(true);

solution($high, $time1, $time2, '24a');
solution($low, $time2, $time3, '24b');
