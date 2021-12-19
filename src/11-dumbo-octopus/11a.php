#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

const SIZE  = 10;
const STEPS = 100;

$octopuses = collect(input('input.txt'))->map(fn($i)=>str_split($i))->map(fn($i)=>collect(map(fn($j)=>(int)$j, $i)));
$flashes = 0;

for($i=1; $i<=STEPS; $i++) {
    $octopuses->transform(fn($i)=>$i->transform(fn($j)=>$j+1));
    [$octopuses, $flashes] = flash($octopuses, $flashes);
}

$time2 = microtime(true);
solution($flashes, $time1, $time2, '11a');

function flash($octopuses, $flashes) : array
{
    do {
        $flashers = $octopuses->map(fn($i)=>$i->filter(fn($v, $k)=>$v>9))->filter(fn($i)=>$i->count() > 0);

        foreach($flashers as $x => $r) {
            foreach($r as $y => $f) {
                foreach(range(-1,1) as $d1) {
                    foreach(range(-1,1) as $d2) {
                        if ($d1 === 0 and $d2 === 0) continue;
                        if ($x + $d1 >= 0 and $y + $d2 >= 0 and $x + $d1 < SIZE and $y + $d2 < SIZE and $octopuses[$x+$d1][$y+$d2] !== 0) {
                            $octopuses[$x + $d1][$y + $d2] += 1;
                        }
                    }
                }
                $octopuses[$x][$y] = 0;
                $flashes++;
            }
        }
    } while ($flashers->count() > 0);

    return [$octopuses, $flashes];
}
