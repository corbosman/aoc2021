#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

const SIZE  = 10;

$octopuses = collect(input('input.txt'))->map(fn($i)=>str_split($i))->map(fn($i)=>collect(map(fn($j)=>(int)$j, $i)));

for($i=1;; $i++) {
    $octopuses->transform(fn($i)=>$i->transform(fn($j)=>$j+1));
    $octopuses = flash($octopuses);
    if ($octopuses->flatten()->sum() === 0) break;
}

$time2 = microtime(true);
solution($i, $time1, $time2, '11b');

function flash($octopuses)
{
    do {
        $flashers = $octopuses->map(fn($i)=>$i->filter(fn($v, $k)=>$v>9))->filter(fn($i)=>$i->count() > 0);

        foreach($flashers as $x => $r) {
            foreach($r as $y => $v) {
                foreach(range(-1,1) as $d1) {
                    foreach(range(-1,1) as $d2) {
                        if ($d1 === 0 and $d2 === 0) continue;
                        if ($x + $d1 >= 0 and $y + $d2 >= 0 and $x + $d1 < SIZE and $y + $d2 < SIZE and $octopuses[$x+$d1][$y+$d2] !== 0) {
                            $octopuses[$x + $d1][$y + $d2] += 1;
                        }
                    }
                }
                $octopuses[$x][$y] = 0;
            }
        }
    } while ($flashers->count() > 0);

    return $octopuses;
}
