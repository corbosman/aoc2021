#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function get_scanner_report()
{
    $input = input('inputs/input.txt');
    $report = [];
    $scanner = -1;

    foreach($input as $i) {
        if ($i === '') continue;
        if (str_contains($i, 'scanner')) {
            $scanner += 1;
            continue;
        }
        preg_match_all('/(-?[0-9]+)/', $i, $m);
        $report[$scanner][] = [(int)$m[0][0], (int)$m[0][1], (int)$m[0][2]];
    }

    return collect($report);
}

function manhattan_distance(array $c1, array $c2) : int
{
    return abs($c1[0] - $c2[0]) + abs($c1[1] - $c2[1]) + abs($c1[2] - $c2[2]);
}

class Beacon
{
    public ?array $absolute_position = null;
    public ?array $rotations = null;

    public function __construct(public array $relative_position) {
        $this->rotate();
    }

    public function fix_position(array $position, int $rotation)
    {
        [$x, $y, $z] = $this->rotations[$rotation];
        $position = [$x + $position[0], $y + $position[1], $z + $position[2]];
        $this->absolute_position = $position;
    }

    // create all rotations for this beacon
    public function rotate()
    {
        [$x, $y, $z] = $this->relative_position;

        $this->rotations = [
            [ $x,  $y,  $z],   [ $x, -$z,  $y],   [ $x, -$y, -$z],
            [ $y,  $x, -$z],   [-$z,  $x, -$y],   [-$x, -$y,  $z],
            [-$x, -$z, -$y],   [-$x,  $y, -$z],   [-$x,  $z,  $y],
            [-$z, -$x,  $y],   [-$z,  $y,  $x],   [ $y,  $z,  $x],
            [ $y, -$x,  $z],   [ $z, -$x, -$y],   [-$y, -$x, -$z],
            [ $z, -$y,  $x],   [-$y, -$z,  $x],   [-$z, -$y, -$x],
            [-$y,  $z, -$x],   [ $z,  $y, -$x],   [ $y, -$z, -$x],
            [ $x,  $z, -$y],   [-$y,  $x,  $z],   [ $z,  $x,  $y],
        ];
    }

}

class Scanner
{
    public ?array $position = null;                 // absolute position if known
    public ?int $rotation   = null;                 // rotation of this scanner if known
    public Collection $beacons;                     // all beacons
    public ?Collection $rotated_beacons = null;     // a cache of all rotated beacon positions

    public function __construct(array $beacons) {
        $this->beacons = collect($beacons)->map(fn($beacon) => new Beacon($beacon));
        $this->rotated_beacons = $this->create_rotations();
    }

    // create a cache of all rotations for all beacons known to this scanner
    function create_rotations() : Collection
    {
        $rotations = [];
        for($i=0; $i<24; $i++) {
            foreach ($this->beacons as $beacon) {
                $rotations[$i][] = $beacon->rotations[$i];
            }
        }
        return collect($rotations);
    }

    /* fix this scanner to an absolute position */
    public function fix_position(array $position, int $rotation) : void
    {
        $this->position = $position;
        $this->rotation = $rotation;

        /* also fix the location for all beacons */
        foreach($this->beacons as $k => $beacon) {
            $beacon->fix_position($position, $rotation);
        }

    }

    // try to match our position to known located scanners to get an absolute position fix
    public function locate($located_scanners) : bool
    {
        foreach ($located_scanners as $located_scanner) {
            foreach ($this->rotated_beacons as $i => $rotation) {
                if (($position = $located_scanner->try_rotation($rotation, 3)) !== null) {
                    $this->fix_position($position, $i);
                    return true;
                }
            }
        }
        return false;
    }

    /* try to match beacons from a known scanner to another scanner */
    public function try_rotation(array $lost_beacons): ?array
    {
        $deltas = [];

        $located_beacons = $this->rotated_beacons[$this->rotation];

        foreach ($located_beacons as list($x1, $y1, $z1)) {
            foreach ($lost_beacons as list($x2, $y2, $z2)) {
                $delta = [$x1 - $x2, $y1 - $y2, $z1 - $z2];

                // this is a trick to get a unique index, as manhattan distance does not work.
                $index = implode(',', $delta);
                $deltas[$index] = isset($deltas[$index]) ? $deltas[$index] + 1 : 1;

                /* should be >= 12 but you can actually already use 3 */
                if ($deltas[$index] >= 3) return [$delta[0] + $this->position[0], $delta[1] + $this->position[1], $delta[2] + $this->position[2],];
            }
        }
        return null;
    }
}

class Probe
{
    public ?Collection $scanners = null;

    public function analyze($report) : Probe
    {
        $this->scanners = $report->map(fn($scanner) => new Scanner($scanner));
        $this->scanners->first()->fix_position([0,0,0], 0);
        return $this;
    }

    /* return all unique beacons in our fleet of beacons */
    public function beacons() : Collection
    {
        return $this->scanners->reduce(function($beacons, $scanner){
            return $beacons->concat($scanner->beacons);
        }, collect())->unique(fn($beacon) => $beacon->absolute_position);
    }

    /* try to locate all scanners */
    public function locate_scanners() : Probe
    {
        $located_scanners = $this->scanners->whereNotNull('position');
        $lost_scanners    = $this->scanners->whereNull('position');

        while (count($lost_scanners) > 0) {
            $lost_scanner = $lost_scanners->shift();
            $located = $lost_scanner->locate($located_scanners);

            if ($located) {
                $located_scanners->push($lost_scanner);              // scanner was located!
            } else {
                $lost_scanners->push($lost_scanner);                 // scanner not located, try again later
            }
        }
        return $this;
    }

    public function max_distance()
    {
        $distance = 0;
        foreach($this->scanners as $k1 => $scanner1) {
            foreach($this->scanners as $k2 => $scanner2) {
                if ($k1 === $k2) continue;
                $manhattan_distance = manhattan_distance($scanner1->position, $scanner2->position);
                if ($manhattan_distance > $distance) $distance = $manhattan_distance;
            }
        }

        return $distance;
    }
}

$time1   = microtime(true);
$report  = get_scanner_report();
$probe   = (new Probe)->analyze($report)->locate_scanners();
$beacons = $probe->beacons()->count();
$time2   = microtime(true);
$max_distance = $probe->max_distance();
$time3   = microtime(true);

solution($beacons, $time1, $time2, '19a');
solution($max_distance, $time2, $time3, '19b');
