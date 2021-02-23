<?php

class ACityBuilding
{
    
    public function getAllCityBuilding()
    {
        
        global $idPlayer;
        $idCities = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        $playerCities = [];
        foreach ($idCities as $oneId)
        {
            $playerCities[$oneId["id_city"]] = $this->getCityBuilding($oneId["id_city"]);
        }
        return $playerCities;
    }
    
    function getCityBuilding($idCity = 0)
    {
        global $idPlayer;
        if(!$idCity)
            $idCity = validateID ($_GET["idCity"]);
        $CB = selectFromTable("*", "city_building", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0];
        $CBL = selectFromTable("*", "city_building_lvl", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0];
        unset($CB["id_player"]);
        unset($CB["id_city"]);
        unset($CBL["id_player"]);
        unset($CBL["id_city"]);
        return [
                "lvl"=>$CBL,
                "type"=>$CB
            ];
            
    }
    
    function constructNewBuilding()
    {
        
        global $idPlayer;
        $idCity        = validateID($_POST["idCity"]);
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $buildingType  = validateID($_POST["buildingType"]);
        $Building      = LCityBuilding::getBuildingAtPlace($buildingPlace, $idCity);
       
        
        if($Building["Type"] != 0)
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        if(!LCityBuilding::buildingPlaceExist($idCity, $buildingPlace))
            return ["state" => "error_5", "TryToHack" => TryToHack()]; 
        if( $buildingType > CITY_BUILDING_HOSPITAL && LCityBuilding::buildingWithHeighestLvl($idCity, $buildingType)["Lvl"] > 0)
            return ["state" => "error_6", "TryToHack" => TryToHack()];
        
        updateTable("`$buildingPlace` = $buildingType", "city_building", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        
        $Res =  $this->upgrade(); 
        if($Res["state"] != "ok")
            updateTable("`$buildingPlace` = 0", "city_building", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        return $Res;
    
    }



    function upgrade()
    {
        global $idPlayer;
        $idCity        = validateID($_POST["idCity"]);
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $templePlace   = validateGameNames($_POST["templePlace"]);
        $Building      = LCityBuilding::getBuildingAtPlace($buildingPlace, $idCity);
        $Temple        = LCityBuilding::getBuildingAtPlace($templePlace,   $idCity);
        $TempleHelper  = selectFromTable("helper", "city", "id_city = :idc", ["idc" => $idCity])[0]["helper"];
        $countBuilder  = selectFromTable("COUNT(*) AS c", "city_worker", "id_city = :idc", ["idc" => $idCity])[0]["c"];
        $motiv         = selectFromTable("motiv", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["motiv"];
        $upgradeReq    = LBuilding::fulfillCondition($idCity, $Building);
        $lvlReq        = selectFromTable("*", "building_upgrade_req", "building_type = :bt AND building_lvl = :bl", ["bt" => $Building["Type"], "bl" => $Building["Lvl"]]);
        
        if($countBuilder >= 3)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($countBuilder > 0)
            if($motiv < time())    return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($upgradeReq == false)   return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($Building["Lvl"] >= 30) return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        $timeRequired = json_decode($lvlReq[0]["lvl_req"], true)["time"];
        if($TempleHelper == CITY_HELPER_BUILD)
            $timeRequired -= $timeRequired*$Temple["Lvl"]*CITY_HELPER_BUILD_RATE/100;
        $TotalTime = time() + $timeRequired;
        insertIntoTable(
                "id_city = :idc ,  id_player = :idp , place = :pl ,  time_start = :ts , lvl_to = :lt , time_end = :te,  type = :ty , state= 'up', time_end_org = :tor",
                "city_worker",
                [
                    "idc"=> $idCity, "idp" => $idPlayer, "pl" => $buildingPlace, 
                    "ts" => time(), "lt"=>$Building["Lvl"] + 1, "te"=> $TotalTime,
                    "ty" => $Building["Type"], "tor"=> $TotalTime
                ]);
        
        return [
            "state" => "ok",
            "list"  => selectFromTable("*", "city_worker", "id_city = :idc" , ["idc" => $idCity])
        ];
    }
    
    
    function downgrade()
    {
        global $idPlayer;
        $idCity        = validateID($_POST["idCity"]);
        $templePlace   = validateGameNames($_POST["templePlace"]);
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $countBuilder  = selectFromTable("COUNT(*) AS c", "city_worker", "id_city = :idc", ["idc" => $idCity])[0]["c"];
        $Building      = LCityBuilding::getBuildingAtPlace($buildingPlace, $idCity);
        $motiv         = selectFromTable("motiv", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["motiv"];
        $Temple        = LCityBuilding::getBuildingAtPlace($templePlace,   $idCity);
        $TempleHelper  = selectFromTable("helper", "city", "id_city = :idc", ["idc" => $idCity])[0]["helper"];
        $lvlReq        = selectFromTable("*", "building_upgrade_req", "building_type = :bt AND building_lvl = :bl", ["bt" => $Building["Type"], "bl" => min($Building["Lvl"] -1, 29)]);
        
        
        if(!count($lvlReq))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($countBuilder >= 3)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($countBuilder > 0)
            if($motiv < time())
                return ["state" => "error_2", "TryToHack" => TryToHack()];
            
        $lvlReq = json_decode($lvlReq[0]["lvl_req"], true); 
        $timeRequired = $lvlReq["time"];
        
        if($TempleHelper == CITY_HELPER_BUILD)
            $timeRequired -= $timeRequired*$Temple["Lvl"]*CITY_HELPER_BUILD_RATE/100;
        
        $TotalTime = (time() + $timeRequired/2);
        insertIntoTable(
                "id_city = :idc ,  id_player = :idp , place = :pl ,  time_start = :ts , lvl_to = :lt , time_end = :te,  type = :ty , state= 'down', time_end_org = :tor",
                "city_worker",
                [
                    "idc"=> $idCity, "idp" => $idPlayer, "pl" => $buildingPlace, 
                    "ts" => time(), "lt"=>$Building["Lvl"] - 1, "te"=> $TotalTime,
                    "ty" => $Building["Type"], "tor"=> $TotalTime
                ]);
        
        return [
            "state" => "ok",
            "list"  => selectFromTable("*", "city_worker", "id_city = :idc" , ["idc" => $idCity])
        ];
        
    }
    
    
    function speedUp()
    {
        global $idPlayer;
        $idCity        = validateID($_POST["idCity"]);
        $idWorking     = validateGameNames($_POST["idWorking"]);
        $itemUsed      = validateGameNames($_POST["itemToUse"]);
        $now = time();
        
        if(!LItem::useItem($itemUsed))
            return ["state" => "error_0"];
        if($itemUsed == "archit_a") $equation = "time_end - 15*60";
        elseif($itemUsed == "archit_b") $equation = "time_end - 60*60";
        elseif($itemUsed == "archit_c") $equation = "time_end - 3*60*60";
        elseif($itemUsed == "archit_d") $equation = "time_end - (time_end - $now)*0.9"; 
        else return ["state" => "error_1"]; 
        
        
        
        updateTable(
                " time_end = $equation",
                "city_worker",
                "id_city = :idc AND id_player = :idp  AND id = :id",
                ["idc" => $idCity, "idp" => $idPlayer, "id" => $idWorking]);
        return [
            "state" => "ok",
            "list"  => selectFromTable("*", "city_worker", "id_city = :idc" , ["idc" => $idCity])
        ];
    
    }
    
    
    function cancelUpgradeing()
    {
        global $idPlayer;
        $idWorlking      = validateID($_POST["idWorking"]);
        $upgaradingBuild = selectFromTable("*", "city_worker", "id = :idw", ["idw" => $idWorlking]);
        $lvlReq          = json_decode(selectFromTable("*", "building_upgrade_req", "building_type = :bt AND building_lvl = :bl", ["bt" => $upgaradingBuild[0]["type"], "bl" => $upgaradingBuild[0]["lvl_to"] - 1])[0]["lvl_req"], true);
        unset($lvlReq["condetion"]);
        unset($lvlReq["time"]);
        if(!count($upgaradingBuild))
            return ["state" => "error_0"];
        LSaveState::saveCityState($upgaradingBuild[0]["id_city"]);
        if(deleteTable("city_worker", "id = :idw AND id_player = :idp", ["idw" => $idWorlking, "idp" => $idPlayer]))
            LCity::addResource($lvlReq, $upgaradingBuild[0]["id_city"]);
        
        return [
            "state"   => "ok",
            "list"    => selectFromTable("*", "city_worker", "id_city = :idc" , ["idc" => $upgaradingBuild[0]["id_city"]]),
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $upgaradingBuild[0]["id_city"]])[0]
        ];
    }
    
    function explodeBuilding()
    {
       $BuildingPlace = validateGameNames($_POST["BuildingPlace"]);
       $idCity = validateID($_POST["idCity"]);
       $Building = LCityBuilding::getBuildingAtPlace($BuildingPlace, $idCity);
       
       if(!LCityBuilding::buildingPlaceExist($idCity, $BuildingPlace))
            return ["state" => "error_2", "TryToHack" => TryToHack()]; 
        if(!LItem::useItem("powder_keg"))
            return ["state" => "error_0"];
        if($Building["Type"] >= CITY_BUILDING_PALACE)
            return ["state" => "error_1",  "TryToHack" => TryToHack()];
            
        updateTable("`$BuildingPlace` = 0", "city_building", "id_city = :idc", ["idc" =>$idCity]);
        updateTable("`$BuildingPlace` = 0", "city_building_lvl", "id_city = :idc", ["idc" =>$idCity]); 
        deleteTable("build_army", "id_city = :idc AND place = :pl", ["idc" => $idCity, "pl" =>$BuildingPlace]);

        $BuildingTask = [
            "state"   => "down",
            "id_city" => $idCity,
            "lvl_to"  => 0,
            "type"    => $Building["Type"]
        ];
        LBuilding::buildingUpgraded($BuildingTask);
            
   
        return [
            "state" => "ok"
        ];
        
    }
    
}


