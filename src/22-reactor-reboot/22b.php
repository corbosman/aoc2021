#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : array
{
    return collect(input('inputs/input.txt'))->map(function($line) {
        preg_match('/^(.*) x=(-?[0-9]+)..(-?[0-9]+),y=(-?[0-9]+)..(-?[0-9]+),z=(-?[0-9]+)..(-?[0-9]+)$/', $line, $m);
        return [$m[1], (int)$m[2], (int)$m[3], (int)$m[4], (int)$m[5], (int)$m[6], (int)$m[7]];
    })->toArray();
}

class Reactor
{
    public array $cubes = [];

    public function reboot(array $steps) : self
    {
        foreach($steps as $step) {
            $this->step($step);
        }

        return $this;
    }

    protected function step(array $step)
    {
        $command = array_shift($step);
        [$x1, $x2, $y1, $y2, $z1, $z2] = $step;

        foreach($this->cubes as $i => $cube) {
            [$cx1, $cx2, $cy1, $cy2, $cz1, $cz2] = $cube;

            /* no match, next! */
            if ($x1 > $cx2 || $x2 < $cx1 ||      // x does not overlap
                $y1 > $cy2 || $y2 < $cy1 ||      // y does not overlap
                $z1 > $cz2 || $z2 < $cz1)        // z does not overlap
                continue;

            // Always remove the overlap, no matter if we're on or off.
            // We will add the full new cube after to replace it
            $this->remove_overlap($step, $cube, $i);
        }

        if ($command == 'on') $this->cubes[] = $step;
    }

    protected function remove_overlap($step, $cube, $i) : void
    {
        [$x1, $x2, $y1, $y2, $z1, $z2]       = $step;
        [$cx1, $cx2, $cy1, $cy2, $cz1, $cz2] = $cube;

        if ($x2 < $cx2) {
            $this->cubes[] = [$x2 + 1, $cx2, $cy1, $cy2, $cz1, $cz2];
        }

        if ($x1 > $cx1) {
            $this->cubes[] = [$cx1, $x1 - 1, $cy1, $cy2, $cz1, $cz2];
        }

        if ($y2 < $cy2) {
            $this->cubes[] = [max($x1, $cx1), min($cx2, $x2), $y2 + 1, $cy2, $cz1, $cz2];
        }

        if ($y1 > $cy1) {
            $this->cubes[] = [max($cx1, $x1), min($cx2, $x2), $cy1, $y1 - 1, $cz1, $cz2];
        }

        if ($z2 < $cz2) {
            $this->cubes[] = [max($cx1, $x1), min($cx2, $x2), max($cy1, $y1), min($cy2, $y2), $z2 + 1, $cz2];
        }

        if ($z1 > $cz1) {
            $this->cubes[] = [max($cx1, $x1), min($cx2, $x2), max($cy1, $y1), min($cy2, $y2), $cz1, $z1 - 1];
        }
        unset($this->cubes[$i]);
    }

    public function count_cubes() : int
    {
        return array_reduce($this->cubes, function($cubes, $cube) {
            return $cubes + ($cube[1] - $cube[0] + 1) * ($cube[3] - $cube[2] + 1) * ($cube[5] - $cube[4] + 1);
        }, 0);
    }
}

$steps   = load();
$reactor = new Reactor;
$cubes   = $reactor->reboot($steps)->count_cubes();
$time2   = microtime(true);
solution($cubes, $time1, $time2, '22b');
