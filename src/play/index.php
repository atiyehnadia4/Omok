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
            echo json_encode($response);
            return false;
            exit;
        }

        if ($y < 0 || $y > 14) {
            $response = array('response' => false, "reason" => "Invalid y coordinate, $y");
            echo json_encode($response);
            return false;
            exit;
        }
        return true;
    }

    function isPidValid()
    {
        if (!array_key_exists('pid',$_GET)) {
            $response = array('response' => false, 'reason' => 'PID not specified');
            echo json_encode($response);
            exit;
        }
        $files = scandir(DATA);

        if (!in_array($_GET[PID] . '.txt', $files)) {
            $response = array('response' => false, 'reason' => 'PID not specified');
            echo json_encode($response);
            exit;
        }

    }

    function isMoveValid()
    {
        if (!array_key_exists(MOVE, $_GET)) {
            $response = array("response" => false, "reason" => "Move not well-formed");
            echo json_encode($response);
        }

        $move_list = explode(",",  $_GET['move']);
        if (sizeof($move_list) != 2) {
            $response = array("response" => false, "reason" => "Move not well-formed");
            echo json_encode($response);
        }
        else{
            if ($this->areCoorsInRange($move_list[0], $move_list[1])) {
                $this->x_player = $move_list[0];
                $this->y_player = $move_list[1];
            }

        }

    }

    function isPlaceEmpty()
    {
        $this->board = Board::getBoard($_GET[PID]);


        if ($this->board->boardPositions[$this->x_player][$this->y_player] != 0) {
            $response = array("response" => false, "reason" => "Move not allowed");
            echo json_encode($response) . "<br>";
        }
    }

    function start()
    {
        $this->isPidValid();
        $this->isMoveValid();
        $this->isPlaceEmpty();

        $this->game = new Game($this->board);
        $move_list = array($this->x_player, $this->y_player);
        $this->game->playerMove($move_list);
        $comp_coors = $this->game->computerMove();

        $this->x_comp = $comp_coors[0];
        $this->y_comp = $comp_coors[1];

        $ack_move = $this->game->toOutput(1, array($this->x_player, $this->y_player));
        $move = $this->game->toOutput(2, array($this->x_comp, $this->y_comp));
        $response = new Response(true, $ack_move, $move);
        echo json_encode($response);
    }
}

$indexStart = new Index();
$indexStart->start();