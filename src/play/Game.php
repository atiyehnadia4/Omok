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
                    if($this->countConsecutive($i, $j, -1,1, $player) + $this->countConsecutive($i, $j, 1,-1, $player) >= 4){
                        return true;
                    }
                    #Check /
                    if($this->countConsecutive($i, $j, -1,-1, $player) + $this->countConsecutive($i, $j, 1,1, $player) >= 4){
                        return true;
                    }
                    #Check --
                    if($this->countConsecutive($i, $j, -1,0, $player) + $this->countConsecutive($i, $j, 1,0, $player) >= 4){
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
                $count++;
                array_push($this->winning_row, $y, $x);
            }
            else {
                break;
            }

        }
        return $count;
    }


    function horizontalWin($player, $x)
    {
        $temp = [15];
        for ($i = 0; $i < 15; $i++) {
            $temp[$i] = ($this->board->getStone($x, $i)); //populate temp with row
        }
        for ($i = 1; $i < 15; $i++) {
            $count = 0;
            if ($temp[$i] == $player) {
                $count++;
                for ($j = 1; $j < 6; $j++) {
                    if ($count == 5) {
                        array_push($this->winning_row, $x, $j-4, $x, $j-3, $x,  $j-2, $x,  $j-1, $x, $j);
                        if ($player == 1) {
                            $this->p_win = true;

                        }
                        else {
                            $this->c_win = true;
                        }
                        return true;
                    }
                    if (!empty($temp[$i + $j]) == $player) {
                        $count++;
                    }
                }
            }
        }

        return false;
    }

    function verticalWin($player, $y)
    {       //checks up and down in row to check if there is a 5 streak
        $temp = [15];
        for($i = 0; $i < 15; $i++){
            $temp[$i] = $this->board->getStone($i, $y);
        }
        for($i = 1; $i < 15; $i++){
            $count = 0;
            if ($temp[$i] == $player) {
                $count++;
                for ($j = 1; $j < 6; $j++) {
                    if ($count == 5) {
                        if ($player == 1) {
                            $this->p_win = true;
                        } else {
                            $this->c_win = true;
                        }
                        return true;
                    }
                    if (!empty($temp[$i + $j]) == $player) {
                        $count++;
                    }
                }

            }
        }
        return false;
    }

    function diagonalWin($player, $x, $y)
    {
        $temp = [5];
        $count = 0;
        for ($i = 0; $i < 5; $i++) {  //top right
            $temp[$i] = $this->board->getStone($x + $i, $y + $i);  //populates array
        }
        if($temp[0] == $player) {
            $count=1;
            for ($i = 1; $i < 5; $i++) {
                if ($count == 5) {
                    if ($player == 1) {
                        $this->p_win = true;
                    } else {
                        $this->c_win = true;
                    }
                    return true;
                }
                if ($temp[$i] == $player) {       //checks array
                    $count++;
                }
            }
        }
        $temp1 = [5];
        $count = 0;
        for ($i = 0; $i < 5; $i++) {             //top left
            $temp1[$i] = $this->board->getStone($x - $i, $y + $i);  //populates array
        }
        for ($i = 0; $i < 5; $i++) {
            if ($count == 5) {
                if ($player == 1) {
                    $this->p_win = true;
                } else {
                    $this->c_win = true;
                }
                return true;
            }
            if ($temp1[$i] == $player) {       //checks array
                $count++;
            }
        }

        $temp2 = [5];
        $count = 0;
        for ($i = 0; $i < 5; $i++) {             //bottom left
            $temp2[$i] = $this->board->getStone($x - $i, $y - $i);  //populates array
        }
        for ($i = 0; $i < 5; $i++) {
            if ($count == 5) {
                if ($player == 1) {
                    $this->p_win = true;
                } else {
                    $this->c_win = true;
                }
                return true;
            }
            if ($temp2[$i] == $player) {       //checks array
                $count++;
            }
        }

        $temp3 = [5];
        $count = 0;
        for ($i = 0; $i < 5; $i++) {             //top right
            $temp3[$i] = $this->board->getStone($x - $i, $y + $i);  //populates array
        }

        for ($i = 0; $i < 5; $i++) {
            if ($count == 5) {
                if ($player == 1) {
                    $this->p_win = true;
                } else {
                    $this->c_win = true;
                }
                return true;
            }
            if ($temp3[$i] == $player) {       //checks array
                $count++;
            }
        }
        return false;
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
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->p_win, 'isDraw' => $this->isDraw, 'row' => $this->winning_row);
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
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => $this->winning_row);
                return $result;
            } else {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            }
        }
    }

}


