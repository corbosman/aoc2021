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

function print_risk_map($cave, $distances)
{
    for($i=0;$i<count($cave); $i++){
        echo sprintf("%5s", $i);
    }
    echo "\n--------------------------------------------------------\n";
    for($x=0; $x<count($distances); $x++) {
        for($y=0; $y<count($cave[0]); $y++) {
            $risk = $distances[$x][$y] ?? '.';
            echo sprintf("%5s", $risk);
        }
        echo " | $x";
        echo "\n";
    }
    echo "\n";
}
