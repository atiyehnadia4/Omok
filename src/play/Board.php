<?php
    class Board
    {
        public $size;
        public $pid;
        public $strategy;
        public $boardPositions;

        function __construct($size = 15, $strategy = '', $pid = '')
        {
            $this->size = $size;
            $this->pid = $pid;
            $this->strategy = $strategy;
            $this->boardPositions = array_fill(0, $size, array_fill(0, $size, 0));
        }

        function updateFile()
        {
            $path = "../data/" . $this ->pid . ".txt";
            $file = fopen($path, "w") or die ("Unable to open file!");
            fwrite($file, json_encode($this));
            fclose($file);

        }

        function getStone($x, $y)
        {
            if ($x < 0 || $x > 15 || $y < 0 || $y > 15) {
                return null;
            }
            else {
                if(!empty($this->boardPositions[$x][$y])){
                    return $this->boardPositions[$x][$y];
                }
            }
            return false;
        }

        function placeStone($player, $x, $y)
        {
            $this->boardPositions[$x][$y] = $player;
            $this->updateFile();
        }

        static function fromJson($json){
            $obj = json_decode($json, true);
            $board = new Board();
            $board->size = $obj['size'];
            $board->boardPositions = $obj['boardPositions'];
            $board->pid = $obj['pid'];
            $board->strategy = $obj['strategy'];

            return $board;
        }

        static function getBoard($pid){
            $path = "../data/" . $pid . ".txt";
            $file = fopen($path, "r") or die("Cannot read file");
            $json = fread($file, filesize($path));
            fclose($file);

            return self::fromJson($json);
        }
    }