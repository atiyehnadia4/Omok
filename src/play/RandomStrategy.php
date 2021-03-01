<?php

require_once('../play/MoveStrategy.php');

class RandomStrategy extends MoveStrategy
{
    function pickPlace()
    {
        do {
            $x = rand(0, 14);
            $y = rand(0, 14);
        }

        while ($this->board->boardPositions[$x][$y] != 0);
        $this->board->placeStone(2, $x, $y);
        $this->board->updateFile();
        return [$x, $y];
    }

}

