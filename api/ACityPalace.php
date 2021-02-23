<?php

class ACityPalace
{
    function getCityGarrison()
    {
        global $idPlayer;
        
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
      
        return LCity::getCityGarrison($xCoord, $yCoord);
    
    }
    function updateTaxs()
    {
        global $idPlayer;
        $idCity     = validateID($_POST["idCity"]);
        $newTaxRate = validateID($_POST["newTaxRate"]);
        if($newTaxRate < 0 || $newTaxRate > 100)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $City = selectFromTable("pop, pop_cap, taxs, helper, id_city", "city", "id_city = :idc", ["idc" => $idCity])[0];
        $City["taxs"] = $newTaxRate;
        updateTable("taxs = :t, loy_max = 100 - :nt, pop_max = :px", "city", "id_city = :idc AND id_player = :idp", ["idc" =>$idCity, "idp" => $idPlayer, "t" => $newTaxRate, "nt"=> $newTaxRate, "px" => LCity::maxPop($City)]);
        LSaveState::saveCityState($idCity);
        LSaveState::coinInState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]
        ];
    }
    function updateProductionRate()
    {
        global $idPlayer;
        $idCity     = validateID($_POST["idCity"]);
        $foodRate   = validateID($_POST["foodRate"]);
        $woodRate   = validateID($_POST["woodRate"]);
        $stoneRate  = validateID($_POST["stoneRate"]);
        $metalRate  = validateID($_POST["metalRate"]);

        if(!is_numeric($foodRate)  || $foodRate  > 100 || $foodRate  < 0) return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!is_numeric($woodRate)  || $woodRate  > 100 || $woodRate  < 0) return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!is_numeric($stoneRate) || $stoneRate > 100 || $stoneRate < 0) return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(!is_numeric($metalRate) || $metalRate > 100 || $metalRate < 0) return ["state" => "error_3", "TryToHack" => TryToHack()];


        LSaveState::saveCityState($idCity);

        $quary = "food_rate = :fr, wood_rate = :wr, stone_rate = :sr, metal_rate = :mr";
        $upParm =[
            "fr"  => $foodRate,
            "wr"  => $woodRate,
            "sr"  => $stoneRate,
            "mr"  => $metalRate,
            "idc" => $idCity,
            "idp" => $idPlayer
        ];


        updateTable($quary, "city_jop", "id_city =  :idc AND id_player = :idp", $upParm);

        LSaveState::resInState($idCity, "food");
        LSaveState::resInState($idCity, "wood");
        LSaveState::resInState($idCity, "stone");
        LSaveState::resInState($idCity, "metal");

        return [
            "state"   => "ok",
            "City"    => selectFromTable("*", "city", "id_city = :idc"    , ["idc" => $idCity])[0],
            "CityJop" => selectFromTable("*", "city_jop", "id_city = :idc", ["idc"=> $idCity])[0]
        ];
    }
    
    function updateName()
    {
        global $idPlayer;
        $idCity   = validateID($_POST["idCity"]);
        $newName  = validatePlayerWord($_POST["NewName"]);
       
        if(mb_strlen($newName) > 10)
            return ["state" => "error_0"]; 
        
        updateTable("name = :n", "city", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity, "n" => $newName]);
        LSaveState::saveCityState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]
        ];
    }
    
    function expandCity()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $City = selectFromTable("lvl, x, y", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        
        if(!count($City))
            return ["state" => "error_0"];
        if($City[0]["lvl"] >= 3)
            return ["state" => "error_1"];
        if(!LItem::useItem("expan_plan", pow(2, $City[0]["lvl"])))
            return ["state" => "error_2"];
        
        
        $Palace = LCityBuilding::getBuildingAtPlace("palace", $idCity);
        if($Palace["Lvl"] < ($City[0]["lvl"] + 1)*4)
            return ["state" => "error_3"];
        
        updateTable("ut = :nt, l = :l", "world", "x = :x AND y =  :y", ["nt" => WUT_CITY_LVL_0 + $City[0]["lvl"],"l" => $City[0]["lvl"] + 1, "x" => $City[0]["x"], "y" => $City[0]["y"]]);
        updateTable("lvl = :nl", "city", "id_city = :idc AND id_player = :idp", ["nl" => $City[0]["lvl"] + 1, "idp" => $idPlayer, "idc" => $idCity]);
        
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]
        ];
    
    }
    function barryAbandon()
    {
        global $idPlayer;
        $xCoord  = validateID($_POST["xCoord"]);
        $yCoord  = validateID($_POST["yCoord"]);
        $Barrary = selectFromTable("*", "city_bar", "x_coord = :x AND y_coord = :y", ["x" => $xCoord, "y" => $yCoord]);
        
        if(!count($Barrary))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Barrary[0]["id_player"] != $idPlayer)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        deleteTable("city_bar", "x_coord = :x AND y_coord = :y AND id_player = :idp", ["x" => $xCoord, "y" => $yCoord, "idp" => $idPlayer]);
        
        LSaveState::saveCityState($Barrary[0]["id_city"]);
        LSaveState::resInState($Barrary[0]["id_city"], "food");
        LSaveState::resInState($Barrary[0]["id_city"], "wood");
        LSaveState::resInState($Barrary[0]["id_city"], "stone");
        LSaveState::resInState($Barrary[0]["id_city"], "metal");
        
        return [
            "state" => "ok"
        ];
        
    }
    function removeHeroFromGarrison()
    {
        global $idPlayer;
        $idHero     = validateID($_POST["idHero"]);
        $idCity     = validateID($_POST["idCity"]);
        $Hero       = selectFromTable("*", "world_unit_garrison", "id_hero = :idh", ["idh" => $idHero]);
        $CityCoords = selectFromTable("x, y", "city", "id_city = :idc", ["idc" => $idCity]);
        
        
        if(!count($Hero))
            return [ "state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($CityCoords))
            return [ "state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["id_player"] != $idPlayer && $CityCoords[0]["id_city"] != $idCity)
            return [ "state" => "error_2", "TryToHack" => TryToHack()];
        
        deleteTable("world_unit_garrison", "id_hero = :idh", ["idh" => $idHero]);
        updateTable("in_city = :ic", "hero", "id_hero = :idh", ["idh" => $idHero, "ic" => HERO_IN_CITY]);
        
        return [
            "state"    => "ok",
            "Garrison" => LCity::getCityGarrison($CityCoords[0]["x"], $CityCoords[0]["y"]),
            "Hero"     => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idHero])[0]
        ];
    }
    
    function  addCityGarrison()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable(
                "hero.in_city, city.x, city.y",
                "hero JOIN city On city.id_city = hero.id_city", "hero.id_hero = :idh AND hero.id_player = :idp",
                ["idh" => $idHero, "idp" => $idPlayer]
                );
        $lastOrd = selectFromTable("ord", "world_unit_garrison", "x_coord = :x AND y_coord = :y ORDER BY ord DESC LIMIT 1", ["x"=>$Hero[0]["x"], "y"=>$Hero[0]["y"]]);
        $ord = 0;
        
        
        if(!count($Hero))
            return ["state" => "error_0", "TryTOHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_1", "TryTohack" => TryToHack()];
        
        if(count($lastOrd))
            $ord = $lastOrd[0]["ord"] + 1;
        
        insertIntoTable(
                "x_coord = :x , y_coord = :y, id_hero = :idh, id_player = :idp, ord = :o",
                "world_unit_garrison", 
                ["x"=>$Hero[0]["x"], "y" =>$Hero[0]["y"], "idh"=>$idHero, "idp"=>$idPlayer, "o" => $ord]
                );
        
        return [
            "state" => "ok",
            "Garrison" => LCity::getCityGarrison($Hero[0]["x"], $Hero[0]["y"])
        ];
    }
    
    function reordCityGarrison()
    {
        global $idPlayer;
        $idHero          = validateID($_POST["idHero"]);
        $ordDirection    = validateGameNames($_POST["Direction"]);
        $Hero            = selectFromTable("*", "world_unit_garrison", "id_hero = :idh", ["idh" => $idHero]);
        $City            = selectFromTable(
                "city.id_city, city.x, city.y", 
                "world_unit_garrison JOIN city ON city.x = world_unit_garrison.x_coord AND city.y = world_unit_garrison.y_coord",
                "world_unit_garrison.id_hero = :idh", ["idh" => $idHero]);
        
        if(!count($City))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        if($ordDirection == "up")
            $theOtherHero = selectFromTable ("*", "world_unit_garrison", "x_coord = :x AND y_coord = :y AND ord < :o ORDER BY ord DESC LIMIT 1", ["x" => $Hero[0]["x_coord"], "y" => $Hero[0]["y_coord"], "o" => $Hero[0]["ord"]]);
        else 
            $theOtherHero = selectFromTable ("*", "world_unit_garrison", "x_coord = :x AND y_coord = :y AND ord > :o ORDER BY ord ASC LIMIT 1", ["x" => $Hero[0]["x_coord"], "y" => $Hero[0]["y_coord"], "o" => $Hero[0]["ord"]]);
        
        if(!count($theOtherHero))
            return [
                "state" => "ok",
                "Garrison" => LCity::getCityGarrison($Hero[0]["x_coord"], $Hero[0]["y_coord"])
            ];
        updateTable("ord = :o", "world_unit_garrison", "id_hero = :idh", ["o" => $Hero[0]["ord"], "idh" => $theOtherHero[0]["id_hero"]]);
        updateTable("ord = :o", "world_unit_garrison", "id_hero = :idh", ["o" => $theOtherHero[0]["ord"], "idh" => $Hero[0]["id_hero"]]);
        
        return [
                "state" => "ok",
                "Garrison" => LCity::getCityGarrison($Hero[0]["x_coord"], $Hero[0]["y_coord"])
            ];
    }
    
}

