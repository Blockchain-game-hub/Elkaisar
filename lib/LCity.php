<?php

class LCity
{
    
    static function isResourceTaken($ReqRes, $idCity)
    {
        global $idPlayer;
        if(!static::isResourceEnough($ReqRes, $idCity))
            return false;
        
       
        updateTable(
                implode(", ", array_map(function($e) use ($ReqRes){return "`$e` = `$e` - {$ReqRes[$e]}";}, array_keys($ReqRes))),
                        "city", "id_city = :idc AND id_player = :idp", ["idc" =>$idCity, "idp" => $idPlayer]
                        );
        return true;
    }
    
    static function addResource($gainRes, $idCity)
    {
        global $idPlayer;
        updateTable(
                implode(", ", array_map(function($e) use ($gainRes){return "`$e` = `$e` + {$gainRes[$e]}";}, array_keys($gainRes))),
                        "city", "id_city = :idc AND id_player = :idp", ["idc" =>$idCity, "idp" => $idPlayer]
                        );
        return true;
    }
    
    
    static function isResourceEnough($ReqRes, $idCity)
    {
        global $idPlayer;
        LSaveState::saveCityState($idCity);
        $cityRes = selectFromTable(
                implode(", ", array_map(function($e){return "`$e`";}, array_keys($ReqRes))),
                        "city", "id_city = :idc AND id_player = :idp", ["idc" =>$idCity, "idp" => $idPlayer]
                        );
        if(!count($cityRes))
            return false;
        
        foreach ($ReqRes as $res => $amount){
            if($cityRes[0][$res] <  $amount || $amount < 0)
                return false;
        }
        
        return true;
        
    }
    
    static function getBuildingAtPlace($buildingPlace, $idCity)
    {
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
    
    static function getResources($idCity)
    {
        global $idPlayer;
        
        $cityRes =  selectFromTable(
                        "food, wood, stone, metal, coin, pop", 
                        "city", 
                        "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        if(!count($cityRes))
            return [
                "food"=>0, "wood"=>0, "stone"=>0, "metal"=>0, "coin"=>0, "pop" => 0
            ];
        
        return $cityRes[0];
    }
    
    static function getCityGarrison($xCoord, $yCoord)
    {
        return selectFromTable(
                    "hero.name AS HeroName, player.name AS LordName, hero.lvl, hero.avatar, world_unit_garrison.*",
                    "hero JOIN world_unit_garrison ON world_unit_garrison.id_hero = hero.id_hero JOIN player ON world_unit_garrison.id_player = player.id_player", 
                    "world_unit_garrison.x_coord = :x AND world_unit_garrison.y_coord = :y ORDER BY world_unit_garrison.ord ASC", 
                    ["x" => $xCoord, "y" => $yCoord]);
        
    }
    
    public static function refreshPopCap($idCity){
        
        $cityBuilding     = selectFromTable("*", "city_building", "id_city = :idc", ["idc" => $idCity])[0];
        $cityBuildingLvl = selectFromTable("*", "city_building_lvl", "id_city = :idc", ["idc" => $idCity])[0];

        $totalCap = 300;
       
        
        foreach ($cityBuilding  as $key => $value){
            
            if($value != CITY_BUILDING_COTTAGE)
                continue;
               
            if($key == "id_player")
                continue;
            else if($key == "id_city")
                continue;
            
            $totalCap += CCity::$CottagePopCap[$cityBuildingLvl[$key] - 1];
            
        }
        $City = selectFromTable("pop, pop_cap, taxs, helper", "city", "id_city = :idc", ["idc" => $idCity])[0];
        updateTable("pop_cap = :c, pop_max = :px", "city" ,"id_city = :idc", ["c" => $totalCap, "idc" => $idCity, "px" => LCity::maxPop($City)]);
    }
    
    static function refreshStoreCap($idCity){
        global $idPlayer;
        
        $cityBuilding     = selectFromTable("*", "city_building", "id_city = :idc", ["idc" => $idCity])[0];
        $cityBuildingLvl  = selectFromTable("*", "city_building_lvl", "id_city = :idc", ["idc" => $idCity])[0];
        $StudyLvl         = selectFromTable("storing", "player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        $totalCap = 0;
        
        foreach ($cityBuilding  as $key => $value){
            if($value != CITY_BUILDING_STORE)
                continue;
            if($key == "id_player")
                continue;
            else if($key == "id_city")
                continue;
            $totalCap += CCity::$StorageCap[$cityBuildingLvl[$key] - 1];
            
        }
        
        if(count($StudyLvl))
            $totalCap += $totalCap*$StudyLvl[0]["storing"]*0.05;
        
        
        updateTable("total_cap = :c", "city_storage" ,"id_city = :idc", ["c" => $totalCap, "idc" => $idCity]);
    }
    
    static function addCity($xCoord, $yCoord, $CityName = "1")
    {
        global $idPlayer;
        
        $cityCount = selectFromTable("COUNT(*) AS c", "city", "id_player = :idp", ["idp" => $idPlayer])[0]["c"];
        $idCity = ($idPlayer - 1)*10 + $cityCount + 1;
        
       insertIntoTable("id_player  = :idp, id_city = :idc ,x = :xc , y = :yc  , name = :n", "city", ["idp" => $idPlayer, "idc" => $idCity, "xc" => $xCoord, "yc" => $yCoord, "n" => $CityName]);
        
        if($idCity <= 0)
            return ;
        insertIntoTable("id_city = :idc, id_player = :idp", "city_building",     ["idc" => $idCity, "idp" => $idPlayer]);
        insertIntoTable("id_city = :idc, id_player = :idp", "city_building_lvl", ["idc" => $idCity, "idp" => $idPlayer]);
        insertIntoTable("id_city = :idc, id_player = :idp", "city_wounded", ["idc" => $idCity, "idp" => $idPlayer]);
        insertIntoTable("id_city = :idc, id_player = :idp", "city_jop", ["idc" => $idCity, "idp" => $idPlayer]);
        insertIntoTable("id_city = :idc, id_player = :idp", "city_storage", ["idc" => $idCity, "idp" => $idPlayer]);
        insertIntoTable("id_city = :idc, id_player = :idp", "city_theater", ["idc" => $idCity, "idp" => $idPlayer]);
        updateTable    ("t = 17 ,  ut = :t", "world", "x= :x AND y = :y", ["x" => $xCoord, "y" => $yCoord, "t" => WUT_CITY_LVL_0]);
        
        
        $Player = selectFromTable("city_flag, id_guild", "player", "id_player = :idp", ["idp" => $idPlayer])[0];
        (new LWebSocket())->send(json_encode([
            "url" => "World/refreshWorldCities",
            "data" => []]));
        (new LWebSocket())->send(json_encode([
            "url" => "World/refreshWorldCitiesForPlayers",
            "data" => [
                "idCity"    => $idCity,
                "idPlayer"  => $idPlayer,
                "CityFlag"  => $Player["city_flag"],
                "idGuild"   => $Player["id_guild"],
                "xCoord"    => $xCoord,
                "yCoord"    => $yCoord
            ]]));
        
        
        updateTable( "guild_num = (SELECT COUNT(*) FROM guild), city_num = (SELECT count(*) from city )", "server_data", "1");
        return $idCity;
        
    }
    
    static function maxPop($City)
    {
        
        $WarShip = 0;
        if($City["helper"] == CITY_HELPER_POP)
            $WarShip = LCityBuilding::buildingWithHeighestLvl ($City["id_city"], CITY_BUILDING_WORSHIP)["Lvl"];
        
        return ceil($City["pop_cap"] + ($City["pop_cap"]*0.03*$WarShip) - (($City["taxs"]*$City["pop_cap"])/100 ));
        
    }
}

