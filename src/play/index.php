<?php

require "../play/Game.php";

define('PID', 'pid');
define('MOVE', 'move');
define('DATA', '../data/.');

class Response{
    public $response;
    public $ack_move;
    public $move;

    function __construct($response, $ack_move, $move){
        $this->response = $response;
        $this->ack_move = $ack_move;
        $this->move =$move;
    }
}

class Index
{
    public $x_player;
    public $y_player;
    public $x_comp;
    public $y_comp;
    public $board;
    public $game;

    function areCoorsInRange($x, $y)
    {
        if ($x < 0 || $x > 14) {
            $response = array('response' => false, "reason" => "Invalid x coordinate, $x");
            echo json_encode($response) . "<br>";
            return false;
        }

        if ($y < 0 || $y > 14) {
            $response = array('response' => false, "reason" => "Invalid y coordinate, $y");
            echo json_encode($response) . "<br>";
            return false;
        }
        return true;
    }

    function isPidValid()
    {
        if (!array_key_exists('pid',$_GET)) {
            $response = array('response' => false, 'reason' => 'PID not specified');
            echo json_encode($response) . "<br>";
            return false;

        }

        else if (!in_array($_GET[PID] . '.txt', scandir(DATA))) {
            $response = array('response' => false, 'reason' => 'PID not specified');
            echo json_encode($response) . "<br>";
            return false;

        }
        return true;

    }

    function isMoveValid()
    {
        if(!empty($_GET[MOVE])){
            $move_list = explode(",",  $_GET[MOVE]);
            if (!array_key_exists(MOVE, $_GET)) {
                $response = array("response" => false, "reason" => "Move not well-formed");
                echo json_encode($response) . "<br>";
                return false;
            }
            else if(sizeof($move_list) != 2) {
                $response = array("response" => false, "reason" => "Move not well-formed");
                echo json_encode($response) . "<br>";
                return false;
            }

            else{
                if ($this->areCoorsInRange($move_list[0], $move_list[1])) {
                    $this->x_player = $move_list[0];
                    $this->y_player = $move_list[1];
                }
                return true;
            }
        }
        return false;
    }

    function isPlaceEmpty()
    {
        if(!empty($_GET[PID])){
            $this->board = Board::getBoard($_GET[PID]);

            if (!empty($this->board->boardPositions[$this->x_player][$this->y_player]) != 0) {
                $response = array("response" => false, "reason" => "Move not allowed");
                echo json_encode($response) . "<br>";
                return false;
            }
        }
        return true;
    }

    function start()
    {
        if( $this->isPidValid() && $this->isMoveValid() && $this->isPlaceEmpty()){
            $this->game = new Game($_GET[PID]);
            $move_list = array($this->x_player, $this->y_player);
            $this->game->playerMove($move_list);
            $comp_coors = $this->game->computerMove();

            if(!empty($comp_coors)){
                $this->x_comp = $comp_coors[0];
                $this->y_comp = $comp_coors[1];
            }

            $ack_move = $this->game->toOutput(1, array($this->x_player, $this->y_player));
            $move = $this->game->toOutput(2, array($this->x_comp, $this->y_comp));
            $response = new Response(true, $ack_move, $move);
            echo json_encode($response);
        }
        $this->isPidValid();
        $this->isMoveValid() || $this->isPlaceEmpty();

    }
}

$indexStart = new Index();
$indexStart->start();