<?php

class LBuilding
{
    static function isConditionsTrue($idCity, $Building)
    {
        
        $lvlReq = selectFromTable("*", "building_upgrade_req", "building_type = :bt AND building_lvl = :bl", ["bt" => $Building["Type"], "bl" => $Building["Lvl"]]);
       
        if(!count($lvlReq))
            return false;
        $condetion = json_decode($lvlReq[0]["lvl_req"], true);
        
        foreach ($condetion["condetion"] as $one)
        {
            if($one["type"] == "building")
                if(LCityBuilding::buildingWithHeighestLvl($idCity, $one["BuildingType"])["Lvl"] < $one["lvl"])
                    return false;
            if($one["type"] == "population")
                if(selectFromTable("pop", "city", "id_city = :idc",["idc" => $idCity])[0]["pop"] < $one["amount"])
                    return false;
            if($one["type"] == "item")
                if(LItem::getAmount($one["item"]) < $one["amount"])
                    return false;
            
        }
        return true;
    }
    
    static function fulfillCondition($idCity, $Building)
    {
        if(!static::isConditionsTrue($idCity, $Building))
            return false;
        
        
        $lvlReq = selectFromTable("*", "building_upgrade_req", "building_type = :bt AND building_lvl = :bl", ["bt" => $Building["Type"], "bl" => $Building["Lvl"]]);
        
        $condetion = json_decode($lvlReq[0]["lvl_req"], true);
        $ReqRes = $condetion;
        unset($ReqRes["condetion"]);
        unset($ReqRes["time"]);
        
        if(!LCity::isResourceTaken($ReqRes, $idCity))
            return false;
        
        foreach ($condetion["condetion"] as $one)
        {
            if($one["type"] == "item")
                if(!LItem::useItem($one["item"], $one["amount"]))
                    return false;
            
        }
        return $lvlReq[0];
        
    }
    
    
    
    static function buildingUpgraded(&$BuildingTask)
    {
        $GaindePres = 0;
        if($BuildingTask["state"] == "up")
        {
            $GaindePres = LPrestige::buildGainPrestige(["Lvl" => $BuildingTask["lvl_to"] -1, "Type" => $BuildingTask["type"]]);
            LPrestige::addPres($BuildingTask["id_player"], $GaindePres);
        }
        updateTable("exp = exp + :e", "hero", "id_hero = (SELECT console FROM city WHERE id_city = :idc)", ["e" =>$GaindePres*2, "idc" => $BuildingTask["id_city"]]);
        
       
        if($BuildingTask["type"] == CITY_BUILDING_COTTAGE)
            LCity::refreshPopCap($BuildingTask["id_city"]);
        
        else if($BuildingTask["type"] == CITY_BUILDING_STORE)
            LSaveState::storeRatio($BuildingTask["id_city"]);
            
        else if($BuildingTask["type"] == CITY_BUILDING_THEATER)
            updateTable("lvl = :l", "city_theater", "id_city = :idc", ["l" => $BuildingTask["lvl_to"], "idc" => $BuildingTask["id_city"]]);
        
        else if($BuildingTask["type"] == CITY_BUILDING_PALACE){
            LSaveState::saveCityState($BuildingTask["id_city"]);
            updateTable("coin_cap = :cc", "city", "id_city = :idc", ["cc" => CCity::$PalaceCoinCap[$BuildingTask["lvl_to"] - 1], "idc" => $BuildingTask["id_city"]]);
            
        }else if($BuildingTask["type"] == CITY_BUILDING_WALL){
            
            $wall_cap = 10000* ($BuildingTask["lvl_to"]);
            updateTable("wall_cap = :c", "city", "id_city = :idc", ["c" => $wall_cap, "idc" => $BuildingTask["id_city"]]);
            
        }
        
        $BuildingTask["prestige"] = $GaindePres;
        
    }
    
}

