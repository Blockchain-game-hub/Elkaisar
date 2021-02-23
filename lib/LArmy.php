<?php

class LArmy {

    static function checkIfArmyType($armyType) {
        return(
                $armyType == "army_a" || $armyType == "army_b" || $armyType == "army_c" ||
                $armyType == "army_d" || $armyType == "army_e" || $armyType == "army_f" ||
                $armyType == "wall_a" || $armyType == "wall_b" || $armyType == "wall_c" ||
                $armyType == "spies"
                );
    }

    static function checkIfDevideBy($divideBy) {
        return ( $divideBy == "none" || $divideBy == "time" || $divideBy == "amount" );
    }

    static function neededResources($armyType) {
        return CArmy::$ResourcseNeeded[$armyType];
    }

    static function getLastbatchArmyBuilding($idCity, $buildingPlace) {
        global $idPlayer;

        $lastBatchTime = selectFromTable(
                "time_end", "build_army",
                "id_player = :idp AND  place = :pl AND id_city = :idc ORDER BY time_end DESC LIMIT 1",
                ["idp" => $idPlayer, "pl" => $buildingPlace, "idc" => $idCity]);
        
        if(!count($lastBatchTime))
            return time();
        
        return $lastBatchTime[0]["time_end"];
    }
    
    static function buildingLvlEffect($buildingLvl)
    {
        if($buildingLvl >= 20)
            return ;
    }
    
    static function getBlockToSpeedUp($buildingPlace)
    {
        $blockPlaces = [];
        if($buildingPlace == "wall"){
            $blockPlaces = ["wall"];
        }else{
            $blockKey = explode("_", $buildingPlace);
            array_pop($blockKey);
            $blockBelong = implode("_", $blockKey);
            $blockPlaces = CArmy::$CommonBlocks[$blockBelong];
        }
        
        return $blockPlaces;
    }
    
    static function prepareHeroBattel(&$Heros)
    {
        
        foreach ($Heros as &$oneHero)
        {
            $all_cell_type = array_keys($oneHero["type"]);
            $all_cell_amount = array_keys($oneHero["pre"]);

            for ($index = 0 ; $index < count($all_cell_amount) ; $index++):

                if($oneHero["type"][$all_cell_type[$index]] != 0){
                    $oneHero["real_eff"][$index + 1]["attack"]     +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["attack"]  + $oneHero["point_atk"];
                    $oneHero["real_eff"][$index + 1]["def"]        +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["def"]     + $oneHero["point_def"];
                    $oneHero["real_eff"][$index + 1]["vit"]        +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["vit"]; 
                    $oneHero["real_eff"][$index + 1]["dam"]        +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["dam"] ; 
                    $oneHero["real_eff"][$index + 1]["break"]      +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["break"]; 
                    $oneHero["real_eff"][$index + 1]["anti_break"] +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["anti_break"];
                    $oneHero["real_eff"][$index + 1]["strike"]     +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["strike"]; 
                    $oneHero["real_eff"][$index + 1]["immunity"]   +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["immunity"]; 
                    $oneHero["real_eff"][$index + 1]["unit"]        =  $oneHero["pre"][$all_cell_amount[$index]];
                    $oneHero["real_eff"][$index + 1]["armyType"]    =  $oneHero["type"][$all_cell_amount[$index]];
                    $oneHero["resource_capacity"]                  +=  CArmy::$ArmyPower[$oneHero["type"][$all_cell_type[$index]]]["res_cap"]*$oneHero["pre"][$all_cell_amount[$index]];
                } 

            endfor;

            
            
        }
        
    }
}
