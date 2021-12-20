#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;
require __DIR__ . '/../../vendor/autoload.php';

function get_report()
{
    $input = input('inputs/input_e.txt');
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
    public ?string $id = null;
    public ?array $absolute_position = null;
    public Collection $distances;
    public ?int $max_distance = null;

    public function __construct(public array $relative_position) {
        $this->distances = collect();
    }

    public function set_absolute_position(?array $pos = null)
    {
        $this->absolute_position = $pos ?: $this->relative_position;
    }

    public function set_id($id)
    {
        $this->id = $id;
    }
}

class Scanner
{
    public ?array $position = null;
    public Collection $beacons;

    public function __construct(array $beacons) {
        $this->beacons = collect($beacons)->map(fn($beacon) => new Beacon($beacon));
    }

    public function set_position(array $position)
    {
        $this->position = $position;
    }

    public function calculate_beacon_distances()
    {
        foreach($this->beacons as $b1) {
            foreach($this->beacons as $b2) {
                $distance = manhattan_distance($b1->relative_position, $b2->relative_position);
                if ($distance !== 0) $b1->distances->push($distance);
            }
            $b1->distances = $b1->distances->sort()->values();
            $b1->max_distance = $b1->distances->max();
        }
    }
}

class Fleet
{
    public function __construct(public Collection $scanners)
    {
        $this->scanners = $this->scanners->map(fn($scanner) => new Scanner($scanner));

        // designate the first scanner as absolutely positioned
        $this->scanners->first()->set_position([0,0,0]);                        // value of first scanner is known
        $this->scanners->first()->beacons->each->set_absolute_position();       // set all absolute positions for these beacons
        $this->scanners->first()->beacons->each(function($beacon) {             // give them all a uniq id
            $id = uniqid(true);
            $beacon->set_id($id);
        });
    }

    public function beacons()
    {
        return $this->scanners->reduce(function($beacons, $scanner){
            return $beacons->concat($scanner->beacons);
        }, collect());
    }

    public function locate_scanners() : Fleet
    {
        $scanners = $this->scanners->whereNull('position');
        while($scanners->count() > 0) {
            $scanner = $scanners->pop();

        }
        return $this;
    }

    public function identify_beacons()
    {
        $this->scanners->each->calculate_beacon_distances();

        $beacons = $this->beacons();
        $identified_beacons   = $beacons->whereNotNull('id');
        $unidentified_beacons = $beacons->whereNull('id');

        while($unidentified_beacons->count() > 0) {
            dump("unidentified = {$unidentified_beacons->count()} , identified = {$identified_beacons->count()}");

            // take a beacon from the stack
            $unidentified_beacon = $unidentified_beacons->shift();

            // compare it with known beacons
            $identified = false;
            foreach ($identified_beacons as $identified_beacon) {
                $count = $unidentified_beacon->distances->intersect($identified_beacon->distances)->count();
                // dump("count = {$count}");
                if ($count >= 10) {
                    dump("found match");
                    $unidentified_beacon->set_id($identified_beacon->id);
                    $identified_beacons->push($unidentified_beacon);
                    dump("setting existing id {$identified_beacon->id} to unidentified beacon");
                    $identified = true;
                }
            }
            if (!$identified) {
                // give this beacon an id
                $id = uniqid(true);
                $unidentified_beacon->set_id($id);
                dump("set new id {$id} to unidentified beacon");
                // and push it to the identifier beacons stack
                $identified_beacons->push($unidentified_beacon);
            }
        }
        $group = $identified_beacons->groupBy('id');
        dd($group);

        $unique_ids = $identified_beacons->pluck('id');
        dump($unique_ids->unique()->count());
        //dd($identified_beacons);

    }
}
$time1         = microtime(true);
$report = get_report();
$fleet  = (new Fleet($report))->locate_scanners();
$fleet->identify_beacons();

$uniq = $fleet->beacons()->pluck('id')->unique();
dd($uniq->count());

//dd($fleet);

$time2         = microtime(true);
solution(0, $time1, $time2, '19a');

