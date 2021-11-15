<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../../base.php';
require_once '../../../config.php';
require_once '../../../configHome.php';
require_once './ATransferPlayer.php';

DbConnect(2);
$Cities = selectFromTable("id_city, x, y, id_player, lvl", "city", "1");
updateTable("t = 0, ut = 0, l = 0", "world", "t IN (17, 18, 19, 20)");
foreach ($Cities as $oneCity)
{
    
    
    $EmputyUnit = selectFromTable("x, y", "world", "ut = 0 ORDER BY RAND() LIMIT 1");
    updateTable("x = :x, y = :y", "city", "id_city = :idc", [ "x" => $EmputyUnit[0]["x"], "y" => $EmputyUnit[0]["y"], "idc" => $oneCity["id_city"]]);
    updateTable("t = :t, ut = :ut", "world", "x = :x AND y = :y", ["t" => 17 + $oneCity["lvl"], "ut" => 60 + $oneCity["lvl"], "x" => $EmputyUnit[0]["x"], "y" => $EmputyUnit[0]["y"]]);
    
    echo "City {$oneCity["id_city"]} Removed From World For Player {$oneCity["id_player"]}\n";
}