<?php

ini_set("memory_limit", "-1");
set_time_limit(0);
require_once '../lib/LConfig.php';
require_once '../config.php';
require_once '../base.php';

DbConnect(1);
$File = json_decode(file_get_contents("../js-0.0.1/json/worldUnitData.json"), true);
$UnitArmy = [];
foreach ( $File as $unitType =>  $Unit){
    if($Unit["lvlChange"]== true){
        
        $oneCoord = selectFromTable("x, y", "world", "ut = $unitType LIMIT 1");
        $UnitArmy[$unitType] = [];
        
        $Army = selectFromTable("*", "world_unit_hero", "x = :x AND y = :y", ["x" => $oneCoord[0]["x"], "y" => $oneCoord[0]["y"]]);
        
        foreach ($Army as $oneArmy){
            if(!isset($UnitArmy[$unitType][$oneArmy["lvl"]])){
                $UnitArmy[$unitType][$oneArmy["lvl"]][0] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][1] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][2] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][3] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][4] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][5] = 0;
                $UnitArmy[$unitType][$oneArmy["lvl"]][6] = 0;
            }
            
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["f_1_type"]] +=  $oneArmy["f_1_num"];
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["f_2_type"]] +=  $oneArmy["f_2_num"];
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["f_3_type"]] +=  $oneArmy["f_3_num"];
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["b_1_type"]] +=  $oneArmy["b_1_num"];
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["b_2_type"]] +=  $oneArmy["b_2_num"];
            $UnitArmy[$unitType][$oneArmy["lvl"]][$oneArmy["b_3_type"]] +=  $oneArmy["b_3_num"];
            
        }
        
    }
}
foreach ($UnitArmy as $K => $AA){
    foreach ($UnitArmy[$K] as $KK =>  $BB){
        unset($UnitArmy[$K][$KK][0]);
    }
}

print_r(json_encode($UnitArmy));

