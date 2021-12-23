#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : Collection
{
    return collect(input('inputs/input_e2.txt'))->map(function($line) {
        preg_match('/^(.*) x=(-?[0-9]+)..(-?[0-9]+),y=(-?[0-9]+)..(-?[0-9]+),z=(-?[0-9]+)..(-?[0-9]+)$/', $line, $m);
        return [
            'command' => $m[1],
            'x' => [(int)$m[2], (int)$m[3]],
            'y' => [(int)$m[4], (int)$m[5]],
            'z' => [(int)$m[6], (int)$m[7]],
            'outside_boundary' => $m[2]>50||$m[3]>50||$m[4]>50||$m[5]>50||$m[6]>50||$m[7]>50
        ];
    });
}

class Reactor
{
    public array $cubes = [];

    public function reboot($steps)
    {
        foreach($steps as $step) {
            foreach(range($step['x'][0], $step['x'][1]) as $x) {
                foreach(range($step['y'][0], $step['y'][1]) as $y) {
                    foreach(range($step['z'][0], $step['z'][1]) as $z) {
                        if ($step['command'] === 'on') $this->turn_on($x, $y, $z);
                        if ($step['command'] === 'off') $this->turn_off($x, $y, $z);
                    }
                }
            }
        }
        return $this;
    }

    protected function turn_on($x, $y, $z)
    {
       $this->cubes[$x][$y][$z] = true;
    }

    protected function turn_off($x, $y, $z)
    {
        unset($this->cubes[$x][$y][$z]);
    }

    public function cubes() : array
    {
        $cubes = [];
        foreach($this->cubes as $x => $xx) {
            foreach($xx as $y => $yy) {
                foreach($yy as $z => $state) {
                    if ($state === true) $cubes[] = [$x, $y, $z];
                }
            }
        }
        return $cubes;
    }
}


$input = load();
$steps = $input->filter(fn($step) => !$step['outside_boundary']);

$reactor = new Reactor;
$cubes = $reactor->reboot($steps)->cubes();
dd($cubes);
$time2              = microtime(true);
solution(0, $time1, $time2, '22a');
