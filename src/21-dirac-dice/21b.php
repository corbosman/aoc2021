#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : array
{
    $file = input('inputs/input.txt');
    $player1 = $file[0][strlen($file[0])-1];
    $player2 = $file[1][strlen($file[1])-1];
    return [$player1, $player2];
}

/* generate a frequency analyses of all possible pairs during 3 quantum rolls */
class QuantumEntangled
{
    public array $rolls = [];

    public function __construct()
    {
        $rolls = [];
        for($i=1; $i<=3; $i++) {
            for($j=1; $j<=3; $j++) {
                for($k=1; $k<=3; $k++) {
                    $rolls[] = $i+$j+$k;
                }
            }
        }
        $this->rolls = array_count_values($rolls);
    }
}

class Game
{
    protected array $rolls;
    protected array $cache = [];

    public function __construct()
    {
        $this->rolls = (new QuantumEntangled)->rolls;
        return $this;
    }

    /* current player is 0 for player1, 1 for player 2 */
    public function play($player1_position, $player2_position, $current_player, $player1_score, $player2_score)
    {
        /* last universe, one of them wins, now unroll back to the beginning */
        if ($player1_score >= 21) return [1,0];
        if ($player2_score >= 21) return [0,1];

        /* from here on, tally all future wins */
        $wins = [0,0];

        /* if we have seen this exact board state before, just return the cached state */
        $state = serialize([$player1_position, $player2_position, $current_player, $player1_score, $player2_score]);
        $cache_hit = $this->cache[$state] ?? null;
        if ($cache_hit) return $cache_hit;

        /* now add up all 27 possible throws, optimized by frequency */
        foreach($this->rolls as $roll => $frequency) {

            /* calculate the score in this round */
            switch($current_player) {
                /* player 1 */
                case 0:
                    $player1_new_position = (($player1_position + $roll - 1) % 10) + 1;
                    $player1_new_score    = $player1_score + $player1_new_position;
                    $player2_new_score    = $player2_score;
                    $player2_new_position = $player2_position;
                    break;
                /* player 2 */
                default:
                    $player2_new_position = (($player2_position + $roll - 1) % 10) + 1;
                    $player2_new_score    = $player2_score + $player2_new_position;
                    $player1_new_score    = $player1_score;
                    $player1_new_position = $player1_position;
                    break;
            }

            /* swap players and go to the next turn */
            $next_player = $current_player === 0 ? 1 : 0;

            $total_wins_from_here = $this->play($player1_new_position, $player2_new_position, $next_player, $player1_new_score, $player2_new_score);

            /* add up the wins for this possible throw times the frequency of this throw */
            $wins[0] += $total_wins_from_here[0] * $frequency;
            $wins[1] += $total_wins_from_here[1] * $frequency;
        }

        // add this state to the cache */
        $this->cache[$state] = $wins;

        return $wins;
    }
}

$time1  = microtime(true);
[$player1, $player2] = load();
$wins = (new Game)->play($player1, $player2, 0, 0, 0);
$time2  = microtime(true);
solution(max($wins), $time1, $time2, '21b');

