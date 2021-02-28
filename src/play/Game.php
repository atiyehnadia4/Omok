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

    function __construct($board)
    {
        $this->board = $board;
        $this->p_win = false;
        $this->c_win = false;
        $this->isDraw = false;
    }

    public function playerMove($move)
    {
        if ($this->board->getStone($move[0], $move[1]) != 0) {
            return false;
        }
        $this->board->placeStone(1, $move[0], $move[1]);
        $this->playerWin(1, $move[0], $move[1]);
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
        $this->playerWin(2, $bot_move[0], $bot_move[1]);
        if ($this->c_win) {
            $this->isDraw = $this->checkDraw();
        }
        $this->board->updateFile();
        return $bot_move;
    }

    function playerWin($player, $x, $y)
    {
        if ($this->horizontalWin($player, $x) || $this->verticalWin($player, $y) || $this->diagonalWin($player, $x, $y)) {
            return true;
        }
        else {
            return false;
        }

    }

    function horizontalWin($player, $x)
    {
        $temp = [15];
        for ($i = 0; $i < 15; $i++) {
            $temp[$i] = ($this->board->getStone($x, $i)); //populate temp with row
        }
        for ($i = 0; $i < 15; $i++) {
            $count = 0;
            if ($temp[$i] == $player) {
                $count++;
                for ($j = 0; $j < 5; $j++) {
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

    function verticalWin($player, $y)
    {       //checks up and down in row to check if there is a 5 streak
        $temp = [15];
        for($i = 0; $i < 15; $i++){
            $temp[$i] = $this->board->getStone($i, $y);
        }
        for($i = 0; $i < 15; $i++){
            $count = 0;
            if ($temp[$i] == $player) {
                $count++;
                for ($j = 0; $j < 5; $j++) {
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
        for ($i = 0; $i < 5; $i++) {
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
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->p_win, 'isDraw' => $this->isDraw, 'row' => []);
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
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            } else {
                $result = array('x' => $x, 'y' => $y, 'isWin' => $this->c_win, 'isDraw' => $this->isDraw, 'row' => []);
                return $result;
            }
        }
    }


}

