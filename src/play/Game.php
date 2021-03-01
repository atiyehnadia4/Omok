<?php

require 'MoveStrategy.php';
require 'RandomStrategy.php';
require 'SmartStrategy.php';
require "Board.php";

class Game
{
    public $board;
    public $p_win;
    public $c_win;
    public $isDraw;
    public $winning_row;

    function __construct($pid)
    {
        $this->board = Board::getBoard($pid);
        $this->p_win = false;
        $this->c_win = false;
        $this->isDraw = false;
        $this->winning_row = [];
    }

    public function playerMove($move)
    {
        if ($this->board->getStone($move[0], $move[1]) != 0) {
            return false;
        }
        $this->board->placeStone(1, $move[0], $move[1]);
        $this->p_win = $this->checkForWin(1);
        if (!$this->p_win) {
            $this->isDraw = $this->checkDraw();
        }
        return true;
    }

    public function computerMove()
    {
        $strategy = $this->board->strategy . "Strategy";
        $strategyClass = new $strategy($this->board);
        $bot_move = $strategyClass->pickPlace();
        $this->c_win = $this->checkForWin(2);
        if ($this->c_win) {
            $this->isDraw = $this->checkDraw();
        }
        $this->board->updateFile();
        return $bot_move;
    }

    function checkForWin($player){
        for($i = 0; $i < $this->board->size; $i++){
            for($j = 0; $j < count($this->board->boardPositions[0]); $j++){
                if($this->board->boardPositions[$i][$j] == $player){
                    # Check \
                    array_push($this->winning_row, array($i => $j));
                    if($this->countConsecutive($i, $j, -1,1, $player) + $this->countConsecutive($i, $j, 1,-1, $player) >= 4){
                        return true;
                    }
                    #Check /
                    if($this->countConsecutive($i, $j, -1,-1, $player) + $this->countConsecutive($i, $j, 1,1, $player) >= 4){
                        return true;
                    }
                    #Check --
                    if($this->countConsecutive($i, $j, -1,0, $player) + $this->countConsecutive($i, $j, 1,0, $player) >= 4){
                        echo $this->countConsecutive($i, $j, -1,0, $player) + $this->countConsecutive($i, $j, 1,0, $player);
                        return true;
                    }
                    #Check |
                    if($this->countConsecutive($i, $j, 0,-1, $player) + $this->countConsecutive($i, $j, 0,1, $player) >= 4){
                        return true;
                    }
                }
            }
        }

        return false;
    }

    function countConsecutive($ox, $oy, $dx, $dy, $player){
        $x = $ox;
        $y = $oy;
        $count = 0;

        while(true){
            $x += $dx;
            $y += $dy;

            if(!empty($this->board->boardPositions[$y][$x]) && $this->board->boardPositions[$y][$x] == $player) {
                array_push($this->winning_row, array($y => $x));
                $count++;
            }
            else {
                break;
            }

        }
        return $count;
    }

    function checkDraw()
    {
        for ($i = 0; $i < 15; $i++) {
            for ($j = 0; $j < 15; $j++) {
                if ($this->board->boardPositions[$i][$j] == 0) {  // since 0 is an empty space, if an empty space is seen the program knows the game is not done
                    return false;
                }
            }
        }
        return true;
    }

    function toOutput($player, $move)
    {
        $x = $move[0];
        $y = $move[1];

        if ($player == 1) {
            if ($this->isDraw) {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->p_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            } elseif ($this->p_win) {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->p_win, 'isDraw' => $this->isDraw, 'row' => array_slice($this->winning_row, 0, 5));
                return $result;
            } else {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->p_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            }
        }
        if ($player == 2) {
            if ($this->isDraw) {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            } elseif ($this->c_win) {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => array_slice($this->winning_row, 0, 5));
                return $result;
            } else {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            }
        }
    }

}


