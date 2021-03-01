
<?php // index.php
#Nadia Atiyeh CS 3360
#Worked in Collaboration with Christian Rocha

require "../play/Board.php";

define('STRATEGY', 'strategy'); // constant
define('BOARD_SIZE', 15);


if (!array_key_exists(STRATEGY, $_GET)) {
    $message = array("response" => false, "reason" => "Strategy not specified");
    echo json_encode($message);
    exit;
}
$strategy = $_GET[STRATEGY];

if(!($strategy == "Smart" || $strategy == "Random")){
    $message = array("response" => false, "reason" => "Strategy not recognized");
    echo json_encode($message);
}

else{
    $uniqueID = uniqid();
    $message =  array("response" => true, "pid" => $uniqueID);
    $board = new Board(BOARD_SIZE, $strategy, $uniqueID);
    saveGame($uniqueID, $board);
    echo json_encode($message);
}

function saveGame($uniqueID, $board){
    $path = "../data/" . $uniqueID . ".txt";
    $file = fopen($path, "w") or die("Not able to open file");
    fwrite($file, json_encode($board));
    fclose($file);
}


