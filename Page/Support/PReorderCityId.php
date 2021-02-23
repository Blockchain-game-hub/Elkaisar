<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
DbConnect(4);

$Players = selectFromTable("id_player", "player", "1 ORDER BY id_player DESC");

queryExe("SET foreign_key_checks = 0");
$count = 0;
foreach ($Players as $onePlayer)
{
    
    $cities = selectFromTable("id_city", "city", "id_player = :idp ORDER BY id_city DESC", ["idp" => $onePlayer["id_player"]]);
    
    $index = count($cities);
    foreach ($cities as $oneCity)
    {
        
        $idCityNew = ($onePlayer["id_player"] - 1 ) * 10 + $index;
        $index--;
        
        
        updateTable("id_city         = :nc", "city", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_bar", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "build_army", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_building", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_building_lvl", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_jop", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_storage", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_study_acad", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_study_uni", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_theater", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_worker", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_wounded", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "city_wounded_fired", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "hero", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "hero_deleted", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "hero_theater", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city_to      = :nc", "market_buy_transmit", "id_city_to = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "market_deal", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "market_sell_deal", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city_from    = :nc", "market_transport", "id_city_from = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city_to      = :nc", "market_transport", "id_city_to = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city_from    = :nc", "market_transport_back", "id_city_from = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city_to      = :nc", "market_transport_back", "id_city_to = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "spy_city", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "study_tasks", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        updateTable("id_city         = :nc", "study_uni", "id_city = :idc", ["nc" => $idCityNew, "idc" => $oneCity["id_city"]]);
        
    }
    
    $count++;
    echo "done Player ".$onePlayer["id_player"]."With total of $count"."\n";
    
}


queryExe("SET foreign_key_checks = 1");

echo 'done';