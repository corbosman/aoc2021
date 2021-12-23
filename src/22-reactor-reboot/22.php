#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
ini_set('memory_limit','2G');
CONST LIMIT = 50;

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : Collection
{
    return collect(input('inputs/input.txt'))->map(function($line) {
        preg_match('/^(.*) x=(-?[0-9]+)..(-?[0-9]+),y=(-?[0-9]+)..(-?[0-9]+),z=(-?[0-9]+)..(-?[0-9]+)$/', $line, $m);
        return [
            'command' => $m[1],
            'x' => [max($m[2], -LIMIT), min($m[3], LIMIT)],
            'y' => [max($m[4], -LIMIT), min($m[5], LIMIT)],
            'z' => [max($m[6], -LIMIT), min($m[7], LIMIT)],
        ];
    });
}

class Reactor
{
    public array $cubes = [];

    public function reboot($steps)
    {
        foreach($steps as $step) {
            for($x=$step['x'][0];$x<=$step['x'][1]; $x++) {
                for($y=$step['y'][0];$y<=$step['y'][1]; $y++) {
                    for($z=$step['z'][0];$z<=$step['z'][1]; $z++) {
                        if ($step['command'] === 'on') $this->cubes[$x][$y][$z] = 1;
                        if ($step['command'] === 'off') $this->cubes[$x][$y][$z] = 0;
                    }
                }
            }
        }
        return $this;
    }

    public function count_cubes() : int
    {
        $cubes = 0;
        foreach($this->cubes as $x => $xx) {
            foreach($xx as $y => $yy) {
                foreach($yy as $z => $state) {
                    $cubes += $this->cubes[$x][$y][$z];
                }
            }
        }
        return $cubes;
    }
}


$steps   = load();
$reactor = new Reactor;
$cubes   = $reactor->reboot($steps)->count_cubes();
$time2   = microtime(true);
solution($cubes, $time1, $time2, '22a');
