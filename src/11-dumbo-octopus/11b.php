#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

const SIZE  = 10;
const STEPS = 100;
const MAX   = SIZE*SIZE;

$octopuses = collect(file('input.txt', FILE_IGNORE_NEW_LINES))->map(fn($i)=>str_split($i))->flatten()->map(fn($i)=>(int)$i);
$sum = -1;

for($i=1;; $i++) {
    $octopuses->transform(fn($i)=>$i+1);
    $octopuses = flash($octopuses);
    if ($octopuses->sum() === 0) break;
}

output($i);

function flash($octopuses)
{
    do {
        $flashers = $octopuses->filter(fn($v, $k)=>$v>9);

        foreach($flashers as $k=>$flasher) {
            if ($k-SIZE-1 >= 0 and $octopuses[$k-SIZE-1] !== 0 and $k % SIZE > ($k-SIZE-1) % SIZE) $octopuses[$k-SIZE-1] += 1;   // top left
            if ($k-SIZE >= 0 and $octopuses[$k-SIZE] !== 0) $octopuses[$k-SIZE] += 1;                                            // top
            if ($k-SIZE+1 >= 0 and $octopuses[$k-SIZE+1] !== 0 and $k % SIZE < ($k-SIZE+1) % SIZE) $octopuses[$k-SIZE+1] += 1;   // top right
            if ($k-1 >= 0 and $octopuses[$k-1] !== 0 and $k % SIZE > ($k-1) % SIZE) $octopuses[$k-1] += 1;                       // left
            if ($k+1 < MAX and $octopuses[$k+1] !== 0 and $k % SIZE < ($k+1) % SIZE) $octopuses[$k+1] += 1;                      // right
            if ($k+SIZE-1 < MAX and $octopuses[$k+SIZE-1] !== 0 and $k % SIZE > ($k+SIZE-1) % SIZE) $octopuses[$k+SIZE-1] += 1;  // bottom left
            if ($k+SIZE < MAX and $octopuses[$k+SIZE] !== 0) $octopuses[$k+SIZE] += 1;                                           // bottom
            if ($k+SIZE+1 < MAX and $octopuses[$k+SIZE+1] !== 0 and $k % SIZE < ($k+SIZE+1) % SIZE) $octopuses[$k+SIZE+1] += 1;  // bottom right

            $octopuses[$k] = 0;
        }
    } while ($flashers->count() > 0);

    return $octopuses;
}
