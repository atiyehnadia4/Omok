<?php

    abstract class MoveStrategy
    {
        public $board;

        function __construct($board)
        {
            $this->board = $board;
        }

        abstract function pickPlace();

        function toJson()
        {
            return array(‘name’ => get_class($this));
        }

        static function fromJson()
        {
           return new static();
        }
    }

