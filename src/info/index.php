<?php // index.php
class GameInfo
{
    public $size;
    public $strategies;

    function __construct($size, $strategies)
    {
        $this->size = $size;
        $this -> strategies = $strategies;
    }
}

$strategies = array('Smart' => 'SmartStrategy', 'Random' => 'RandomStrategy');
$size = 15;
$info = new GameInfo($size, array_keys($strategies));

if($_SERVER['REQUEST_METHOD']){
    echo json_encode($info);
}
else{
    json_encode("Not Found");
}
