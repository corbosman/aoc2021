#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function target_area()
{
    preg_match_all('/(\d+)/', file('inputs/input.txt', FILE_IGNORE_NEW_LINES)[0], $matches);
    return map(fn($i)=>(int)$i, $matches[0]);
}

[$x1, $x2, $y1, $y2]= target_area();

// 16a denk methode.
// 1. Het hoogste punt bereik je, als in het vak de hoogste verticale snelheid bereikt wordt.
// 2. Dat bereik je als je in 1 stap van 0 naar -10 kan (het laagste punt van het vlak).
// 3. Nu moet je dus omhoog werken -10 + 10 + 9 + 8 + 7 .... tot je + 0 bereikt (na 0 versnel je weer de andere kant op).
// 4. Dit is -10 + (sum range(1-10))
// 5. Dit is de formule n*(n-1)/2


output(-$y1 + -$y1*(-$y1-1)/2);
