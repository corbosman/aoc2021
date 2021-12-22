#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1              = microtime(true);

function load() : array
{
    $file = input('inputs/input.txt');
    $player1 = $file[0][strlen($file[0])-1]-1;
    $player2 = $file[1][strlen($file[1])-1]-1;
    return [$player1, $player2];
}

class Dice
{
    public int $counter = 0;
    protected int $value = 0;

    public function roll() : int
    {
        $this->counter+=1;
        return ++$this->value;
    }
}

class Board
{
    public int $score = 0;
    public bool $winner = false;

    public function __construct(public int $position) {}

    public function move($count) : bool
    {
        $this->position = ($this->position + $count) % 10;
        $this->score += $this->position + 1;
        if ($this->score >= 1000) $this->winner = true;
        return $this->winner;
    }
}

class Game
{
    public function play(Dice $dice, Board $player1, Board $player2) : array
    {
        while(true) {
            $roll = $dice->roll() + $dice->roll() + $dice->roll();
            if ($player1->move($roll)) break;

            $roll = $dice->roll() + $dice->roll() + $dice->roll();
            if($player2->move($roll)) break;
        }
        return [$player1, $player2];
    }
}

[$player1_position, $player2_position] = load();

$dice    = new Dice;
$player1 = new Board($player1_position);
$player2 = new Board($player2_position);

[$player1, $player2] = (new Game)->play($dice, $player1, $player2);

$loser = $player1->winner ? $player2 : $player1;
$score = $dice->counter * $loser->score;

$time2              = microtime(true);
solution($score, $time1, $time2, '21a');
