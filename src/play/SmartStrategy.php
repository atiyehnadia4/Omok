<?php

require_once('../play/MoveStrategy.php');

class SmartStrategy extends MoveStrategy{
    function pickPlace()
    {
        for ($i = 0; $i < $this->board->size; $i++) {
            for ($j = 0; $j < count($this->board->boardPositions[0]); $j++) {
                if ($this->board->boardPositions[$i][$j] == 1) {
                    # Check /
                    if ($this->countConsecutive($i, $j, -1, 1, 1) + $this->countConsecutive($i, $j, 1, -1, 1) >= 2) {
                        for ($s = 0; $s < 3; $s++) {
                            if ($this->board->boardPositions[$i + $s][$j - $s] == 0) {
                                $this->board->placeStone(2, $i + $s, $j - $s);
                                $this->board->updateFile();
                                return [$i + $s, $j - $s];
                            }
                            if ($this->board->boardPositions[$i - $s][$j + $s] == 0) {
                                $this->board->placeStone(2, $i - $s, $j + $s);
                                $this->board->updateFile();
                                return [$i - $s, $j + $s];
                            }
                        }

                    }

                    #Check \
                    if ($this->countConsecutive($i, $j, -1, -1, 1) + $this->countConsecutive($i, $j, 1, 1, 1) >= 2) {
                        for ($s = 0; $s < 3; $s++) {
                            if ($this->board->boardPositions[$i + $s ][$j + $s] == 0) {
                                $this->board->placeStone(2, $i + $s, $j + $s);
                                $this->board->updateFile();
                                return [$i + $s, $j + $s];
                            }
                            if ($this->board->boardPositions[$i - $s][$j - $s] == 0) {
                                $this->board->placeStone(2, $i - $s, $j - $s);
                                $this->board->updateFile();
                                return [$i - $s, $j - $s];
                            }
                        }


                    }
                    #Check --
                    if ($this->countConsecutive($i, $j, -1, 0, 1) + $this->countConsecutive($i, $j, 1, 0, 1) >= 2) {
                        for ($s = 0; $s < 3; $s++) {
                            if ($this->board->boardPositions[$i + $s + 4][$j] == 0) {
                                $this->board->placeStone(2, $i  + $s, $j );
                                $this->board->updateFile();
                                return [$i + $s, $j];
                            }
                            if ($this->board->boardPositions[$i - $s + 4][$j] == 0) {
                                $this->board->placeStone(2, $i - $s + 2 , $j);
                                $this->board->updateFile();
                                return [$i - $s, $j];
                            }
                        }

                    }
                    #Check |
                    if ($this->countConsecutive($i, $j, 0, -1, 1) + $this->countConsecutive($i, $j, 0, 1, 1) >= 2) {
                        for ($s = 0; $s < 3; $s++) {
                            if ($this->board->boardPositions[$i][$j + $s] == 0) {
                                $this->board->placeStone(2, $i, $j + $s);
                                $this->board->updateFile();
                                return [$i, $j + $s];
                            }
                            if ($this->board->boardPositions[$i][$j - $s] == 0) {
                                $this->board->placeStone(2, $i, $j - $s);
                                $this->board->updateFile();
                                return [$i, $j - $s];
                            }
                        }

                    }
                }
            }
        }

        $random = new RandomStrategy($this->board);
        return $random->pickPlace();
    }

    function countConsecutive($ox, $oy, $dx, $dy, $player){
        $x = $ox;
        $y = $oy;
        $count = 0;

        while(true){
            $x += $dx;
            $y += $dy;

            if(!empty($this->board->boardPositions[$x][$y]) && $this->board->boardPositions[$x][$y] == $player) {
                $count++;
            }
            else {
                break;
            }

        }
        return $count;
    }

}

