#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

const INFINITE = 999999999999999;

function load() : array
{
    $input = collect(input('input.txt'))->map(fn($i)=>str_split($i));
    return $input->toArray();
}

function expand(array $cave) : array
{
    $size = count($cave);
    $expanded = [];

    for($i=0;$i<5;$i++) {
        for($j=0;$j<5;$j++) {
            foreach($cave as $x => $row) {
                foreach ($row as $y => $risk) {
                    $risk = $risk+$i+$j;
                    while($risk > 9) $risk = $risk - 9;
                    $expanded[$x+($i*$size)][$y+($j*$size)] = $risk;
                }
            }
        }
    }
    return $expanded;
}

class Heap extends SplPriorityQueue
{
    function compare($priority1,$priority2) : int
    {
        return parent::compare($priority2,$priority1);
    }
}

class Dijkstra
{
    protected array $cave;

    public function __construct(array $cave)
    {
        $this->cave = $cave;
    }

    protected function neighbors(int $x, int $y, int $width, int $height, $visited) : array
    {
        $neighbors = [];

        foreach ([[1,0], [0,1], [-1,0], [0,-1]] as list($dx, $dy)) {
            if (isset($visited[$x+$dx][$y+$dy])) continue;
            if ($x+$dx >= 0 && $x+$dx < $width && $y+$dy >= 0 && $y+$dy < $height) {
                $neighbors[] = [$x+$dx, $y+$dy];
            }
        }

        return $neighbors;
    }

    function shortest($start) : int
    {
        $queue = (new Heap());
        $queue->insert($start, 0);

        $visited   = [];
        $distances = [0=>[0=>0]];

        $width     = count($this->cave[0]);
        $height    = count($this->cave);
        $end_x     = $height-1;
        $end_y     = $width-1;

        while ($queue->count() > 0) {
            list($x, $y) = $queue->extract();

            // woo, we found the shortest path
            if ($x == $end_x && $y == $end_y) {
                return $distances[$x][$y];
            }

            // find all the neighbors for the current position that haven't been visited yet
            $neighbors = $this->neighbors($x, $y, $width, $height, $visited);

            // foreach neighbor, calculate the new distance and check with previous known minimal distance
            foreach ($neighbors as list($nx, $ny)) {
                $distance   = $distances[$x][$y] + $this->cave[$nx][$ny];
                $distance_n = $distances[$nx][$ny] ?? INFINITE;
                if ($distance < $distance_n) {
                    $distances[$nx][$ny] = $distance;
                    $queue->insert([$nx, $ny], $distance);
                }
            }

            // mark current node as visited
            $visited[$x][$y] = true;
        }
        // sad panda, nothing found
        return -1;
    }
}

$cave         = load();
$minimum_risk = (new Dijkstra($cave))->shortest([0,0]);
$time2        = microtime(true);
solution($minimum_risk, $time1, $time2, '15a');

$time3        = microtime(true);
$cave         = expand($cave);
$minimum_risk = (new Dijkstra($cave))->shortest([0,0]);
$time4        = microtime(true);
solution($minimum_risk, $time1, $time2, '15b');
