<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';

DbConnect(1);
$City = selectFromTable("*", "city", "1");
foreach ($City as $one){
    $Unit = LWorld::getEmptyPlace(random_int(1, 10));
    updateTable("x = :x, y = :y", "city", "id_city = :idc", ["idc" => $one["id_city"], "x" => $Unit[0]["x"], "y" => $Unit[0]["y"]]);
    updateTable("ut = 60 + :ut", "world", "x = :x AND y = :y", ["ut" => $one["lvl"], "x" => $Unit[0]["x"], "y" => $Unit[0]["y"]]);
}

echo 'Done';


