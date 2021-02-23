<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
require_once '../../lib/LConfig.php';




DbConnect(4);

$Cities = selectFromTable("id_city, id_player", "city", "1 ORDER BY pop DESC");

foreach ($Cities as $one)
{
    global $idPlayer;
    $idPlayer = $one["id_player"];
    LSaveState::afterCityColonized($one["id_city"]);
    LSaveState::afterCityColonizer($one["id_city"]);
    echo "One City Done {$one["id_city"]} \n";
    
}


