<?php

function print_cave($cave, $risk_map)
{
    for($x=0; $x<count($cave); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            $risk = $risk_map[$x][$y] ?? '.';
            echo sprintf("%3s", $risk);
        }
        echo "\n";
    }
}
