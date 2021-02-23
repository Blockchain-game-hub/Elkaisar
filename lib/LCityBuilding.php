<?php

class LCityBuilding
{
    
    static function getBuildingAtPlace($buildingPlace, $idCity)
    {
        if(!strlen($buildingPlace) || $buildingPlace == false || $buildingPlace == "false")
            return ["Type" => 0, "Lvl" => 0, "Place" => $buildingPlace];
        
        global $idPlayer;
        $building = selectFromTable(
                            "city_building.`$buildingPlace` AS Type, city_building_lvl.`$buildingPlace` AS Lvl", 
                            "city_building JOIN city_building_lvl ON city_building.id_city = city_building_lvl.id_city",
                            "city_building.id_city = :idc AND city_building.id_player = :idp", 
                            ["idc" => $idCity, "idp" => $idPlayer]);
        
        if(!count($building))
            return ["Type" => 0, "Lvl" => 0, "Place" => $buildingPlace];
        $building[0]["Place"] = $buildingPlace;
        return $building[0];
        
    }
    
    static function getTempleEffectRateOnArmy($idCity, $buildingPlace)
    {
        global $idPlayer;
        $cityHelpe = selectFromTable("helper", "city", "id_city = :idc And id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        if(!count($cityHelpe))
            return 0;
        else if($cityHelpe[0]["helper"] != CITY_HELPER_ARMY)
            return 0;
        
        $cityHelperBuilding = static::getBuildingAtPlace($buildingPlace, $idCity);
        
        if($cityHelperBuilding["Type"] != CITY_BUILDING_WORSHIP)
            return 0;
        
        return $cityHelperBuilding["Lvl"]*ARMY_TRAIN_TEMPLE_T_FAC/100;
        
    }
    
    static function canBuildArmy($idCity, $armyType)
    {
        global $idPlayer;
        $cityBuildingType = selectFromTable("*", "city_building", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]; 
        $cityBuildingLvl  = selectFromTable("*", "city_building_lvl", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]; 
        
        unset($cityBuildingType["id_city"]);
        unset($cityBuildingType["id_player"]);
        
        $buildingList = [];
        foreach ($cityBuildingType as $place => $type){
            
            if($type != CArmy::$BuildingTypeForArmy[$armyType])
                continue;
            if($cityBuildingLvl[$place] < CArmy::$BuildingMinLvlReq[$armyType])
                continue;
            
            $countOfBatchs = selectFromTable("COUNT(*) AS c","build_army",
                    "id_city = :idc AND id_player = :idp AND place = :pl",
                    ["idc" => $idCity, "idp" => $idCity, "pl" => $place])[0]["c"];
            
            if($countOfBatchs >= min($cityBuildingLvl[$place] , ARMY_MAX_NUM_BATCH))
                continue;
            
            $buildingList[] = [
                "Place" => $place,
                "Type"=> $type,
                "Lvl" => $cityBuildingLvl[$place]
            ];
        }
        
        return $buildingList;
        
    }
    
    static function buildingWithHeighestLvl($idCity, $buildingType)
    {
        global $idPlayer;
        $buildingWithHeighestLvl = [
                "Place" => "",
                "Type"=> "",
                "Lvl" => 0
            ];
        $cityBuildingType = selectFromTable("*", "city_building", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]; 
        $cityBuildingLvl  = selectFromTable("*", "city_building_lvl", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]; 
        
       
        unset($cityBuildingType["id_city"]);
        unset($cityBuildingType["id_playe"]);
        foreach ($cityBuildingType as $onePlace => $oneType){
            
            if($oneType != $buildingType) continue;
            
            if($buildingWithHeighestLvl["Lvl"] < $cityBuildingLvl[$onePlace])
               $buildingWithHeighestLvl = [
                        "Place" => $onePlace,
                        "Type"=> $oneType,
                        "Lvl" => $cityBuildingLvl[$onePlace]
                    ];
                
            
        }
        
        return $buildingWithHeighestLvl;
        
    }
    
    
    static function buildingPlaceExist($idCity, $buildingPlace)
    {
        $cityLvl = selectFromTable("lvl", "city", "id_city = :idc", ["idc" => $idCity]);  
        if(!count($cityLvl))
            return false;
        
        foreach (CCity::$CityBlocks as $lvl => $Block){
            
            if($lvl > $cityLvl[0]["lvl"])
                return false;
            
            foreach ($Block as $place){
                if($place == $buildingPlace)
                    return true;
            }
            
        }
        
        return false;
    }
}

