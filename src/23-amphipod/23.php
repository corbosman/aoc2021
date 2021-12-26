#!/usr/bin/env php
<?php
use Tightenco\Collect\Support\Collection;

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

const INFINITE = 999999999999;
const A = 0;
const B = 1;
const C = 2;
const D = 3;

const COST    = [1,10,100,1000];
const CHAR    = ['A','B','C','D'];
const ROOMS   = [2,4,6,8];

function load() : array
{
    $i = input('inputs/input.txt');

    return [
        [ord($i[2][3])-ord('A'),ord($i[3][3])-ord('A'), ord($i[4][3])-ord('A'),ord($i[5][3])-ord('A')],
        [ord($i[2][5])-ord('A'),ord($i[3][5])-ord('A'), ord($i[4][5])-ord('A'),ord($i[5][5])-ord('A')],
        [ord($i[2][7])-ord('A'),ord($i[3][7])-ord('A'), ord($i[4][7])-ord('A'),ord($i[5][7])-ord('A')],
        [ord($i[2][9])-ord('A'),ord($i[3][9])-ord('A'), ord($i[4][9])-ord('A'),ord($i[5][9])-ord('A')],
    ];
}

class Heap extends SplMinHeap
{
    function compare($value1, $value2) : int
    {
        return $value2->total_energy <=> $value1->total_energy;
    }
}

class State
{
    public array $hallway;
    public array $rooms;
    public int $energy_for_this_move;
    public int $total_energy;

    public function __construct(array $rooms)
    {
        $this->rooms      = $rooms;
        $this->hallway    = array_fill(0, 11, null);
        $this->hallway[2] = $this->hallway[4] = $this->hallway[6] = $this->hallway[8] = false;
    }

    public function organised() : bool
    {
        for($i=A; $i<=D; $i++) {
            for($j=0;$j<count($this->rooms[A]); $j++) {
                if ($this->rooms[$i][$j] !== $i) return false;
            }
        }
        return true;
    }

    public function room_has_unwanted_guests($room) : bool
    {
        for($i=0;$i<count($this->rooms[$room]); $i++) {
            if ($this->rooms[$room][$i] !== null && $this->rooms[$room][$i] !== $room) return true;
        }
        return false;
    }

    public function move_from_room_to_hallway($room, $hallway_position) : void
    {
        $moves = 0;
        /* find the first position to move from the top */
        for($i=0; $i<count($this->rooms[A]); $i++) {
            if ($this->rooms[$room][$i] !== null) {
                $this->hallway[$hallway_position] = $this->rooms[$room][$i];
                $this->rooms[$room][$i] = null;
                $moves += $i + 1;
                break;
            }
        }
        $moves += abs($hallway_position - ROOMS[$room]);
        $this->energy_for_this_move = $moves * COST[$this->hallway[$hallway_position]];
    }

    public function move_from_hallway_to_room($hallway_position)
    {
        $moves = 0;
        $type = $this->hallway[$hallway_position];
        for($i=count($this->rooms[$type])-1; $i>=0; $i--) {
            if ($this->rooms[$type][$i] === null) {
                $this->rooms[$type][$i] = $type;
                $this->hallway[$hallway_position] = null;
                $moves += $i + 1;
                break;
            }
        }
        $moves += abs($hallway_position - ROOMS[$type]);
        $this->energy_for_this_move = $moves * COST[$type];
    }

    /* find all open positions in the hallway to the left of position */
    public function find_left_positions($start, $end = 0) : array
    {
        $positions = [];
        for($i=$start - 1; $i>= $end; $i--) {
            if ($this->hallway[$i] === false) continue; // cant move to spots above rooms
            if ($this->hallway[$i] !== null) break;     // uh oh, we ran into an obstacle
            $positions[] = $i;                          // we can move here
        }
        return $positions;
    }

    /* find all open positions in the hallway to the right of position */
    public function find_right_positions($start, $end = 11) : array
    {
        $positions = [];
        for ($i=$start + 1; $i<$end; $i++) {
            if ($this->hallway[$i] === false) continue; // cant move to spots above rooms
            if ($this->hallway[$i] !== null) break;     // uh oh, we ran into an obstacle
            $positions[] = $i;                          // we can move here
        }
        return $positions;
    }

    public function amphipods_that_can_go_home() : array
    {
        $amphipods = [];

        foreach($this->hallway as $position => $amphipod) {
            if ($amphipod === null  || $amphipod === false || $this->room_has_unwanted_guests($amphipod)) continue;

            if ($this->hallway_blocked($position, ROOMS[$amphipod])) continue;

            $amphipods[] = $position;
        }
        return $amphipods;
    }

    /* check to see if there is an obstacle in the hallway between 2 positions */
    public function hallway_blocked($start, $end) : bool
    {
        $start = ($start < $end) ? $start+1 : $start-1;
        for($i=min($start, $end); $i<max($start,$end); $i++) {
            if ($this->hallway[$i] !== null && $this->hallway[$i] !== false) return true;
        }
        return false;
    }

    /* string representation of this state, needed for dijkstra */
    public function id() : string
    {
        $string = array_merge($this->hallway, $this->rooms[A], $this->rooms[B],
                              $this->rooms[C], $this->rooms[D]);
        return implode('', array_map(fn($s) => $s===false || $s===null ? '.' : CHAR[$s], $string));
    }
}

class Burrow
{
    public function organise(array $rooms, $part) : int
    {
        $queue   = new Heap();
        $visited = [];
        $energy  = [];

        if ($part == 'a') {
            foreach($rooms as $i => $room) {
                array_splice($rooms[$i],1,2);
            }
        }

        $first_state = new State($rooms);
        $energy[$first_state->id()] = $first_state->total_energy = 0;
        $queue->insert($first_state);

        while($queue->count()) {
            $burrow = $queue->extract();

            // print_burrow($burrow);
            /* we found the lowest energy! */
            if ($burrow->organised()) return $energy[$burrow->id()];

            $id = $burrow->id();
            $neighbors = $this->neighbors($burrow, $visited);

            foreach($neighbors as $next_burrow) {
                /* how much energy have we spent so far moving to this next spot */
                $total_energy_moving_to_neighbor = $energy[$id] + $next_burrow->energy_for_this_move;

                /* is there a previous energy level for that move */
                $next_id = $next_burrow->id();
                $previous_total_energy_for_that_neighbor = $energy[$next_id] ?? INFINITE;
                if ($total_energy_moving_to_neighbor < $previous_total_energy_for_that_neighbor) {
                    $energy[$next_id] = $total_energy_moving_to_neighbor;
                    $next_burrow->total_energy = $total_energy_moving_to_neighbor;
                    $queue->insert($next_burrow);
                }
            }
            $visited[$id] = true;
        }

        return -1;
    }

    public function neighbors(State $burrow, array $visited) : array
    {
        $neighbors = [];

        /* move from room to hallway */
        foreach($burrow->rooms as $room => $ap) {
            /* no amphipods that dont belong */
            if (!$burrow->room_has_unwanted_guests($room)) continue;

            $free_hallway_positions = array_merge($burrow->find_left_positions(ROOMS[$room]),
                                                  $burrow->find_right_positions(ROOMS[$room]));

            foreach($free_hallway_positions as $position) {
                $next_burrow = clone($burrow);
                $next_burrow->move_from_room_to_hallway($room, $position);
                if (isset($visited[$next_burrow->id()])) continue;
                $neighbors[] = $next_burrow;
            }
        }
        /* move from hallway to room */
        foreach($burrow->amphipods_that_can_go_home() as $position) {
            $next_burrow = clone($burrow);
            $next_burrow->move_from_hallway_to_room($position);
            if (isset($visited[$next_burrow->id()])) continue;
            $neighbors[] = $next_burrow;
        }
        return $neighbors;
    }

    public function print_(State $state)
    {
        $id = $state->id();
        $id[2] = $id[4] = $id[6] = $id[8] = ' ';
        echo "#############\n";
        echo "#".substr($id, 0, 11)."#\n";
        echo "###".$id[11]."#".$id[13]."#".$id[15].'#'.$id[17]."###\n";
        echo "###".$id[12]."#".$id[14]."#".$id[16].'#'.$id[18]."###\n";
        echo "  #########  \n\n";
    }
}

$rooms  = load();
$energy = (new Burrow())->organise($rooms,'a');
$time2   = microtime(true);
solution($energy, $time1, $time2, '23a');

$energy = (new Burrow())->organise($rooms,'b');
$time3  = microtime(true);
solution($energy, $time2, $time3, '23b');



