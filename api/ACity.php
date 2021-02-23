<?php

class ACity {

    function getAllCities() {
        global $idPlayer;


        return 
            selectFromTable(
                    "*",
                    "city",
                    "id_player = :idp LIMIT " . PLAYER_LIMIT_CITY_COUNT,
                    ["idp" => $idPlayer])
            ;
    }
    
    function resetCityHelper()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $currentCityHelper = selectFromTable("helper", "city", "id_city = :idc AND id_player = :idp", ["idp" => $idPlayer, "idc" => $idCity]);
        if(!count($currentCityHelper))
            return ["state" => "error_0"];
        if($currentCityHelper[0]["helper"] == CITY_HELPER_NONE)
            return ["state" => "error_1"];
        if(!LItem::useItem("help_house_chng"))
            return ["state" => "error_2"];
        
        updateTable("helper = :nh", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer, "nh" => CITY_HELPER_NONE]);
    
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0] 
        ];
    }
    function changeCityHelper()
    {
        global $idPlayer;
        $idCity            = validateID($_POST["idCity"]);
        $newHelper         = validateID($_POST["newHelper"]);
        $currentCityHelper = selectFromTable("helper", "city", "id_city = :idc AND id_player = :idp", ["idp" => $idPlayer, "idc" => $idCity]);
        if(!count($currentCityHelper)) return ["state" => "error_0"];
        if($currentCityHelper[0]["helper"] != CITY_HELPER_NONE)
            return ["state" => "error_1"];
            
        updateTable("helper = :nh", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer, "nh" => $newHelper]);
        
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0] 
        ];
        
    }
    
    function cureCityWounded()
    {
        global $idPlayer;
        $idCity   = validateID($_POST["idCity"]);
        $armyType = validateGameNames($_POST["armyType"]);
        $armyTypes = [ "army_a","army_b","army_c" ,"army_d","army_e","army_f"];
        if(!in_array($armyType, $armyTypes)) return ["state" => "error_0"];
        $cureUnitPrice = CArmy::$CurePrice[$armyType];
        $armyAmount = selectFromTable("`$armyType`", "city_wounded", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0][$armyType];
        if(!array_key_exists($armyType, CArmy::$CurePrice))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        LSaveState::saveCityState($idCity);
        if(!LCity::isResourceTaken(["coin" => $cureUnitPrice*$armyAmount], $idCity))
                return ["state" => "error_1", "coinAmount" => $cureUnitPrice*$armyAmount];
        
        updateTable("`$armyType` = 0 ", "city_wounded", "id_city = :idc  AND id_player = :idp AND `$armyType` = :aa", ["idc"=> $idCity, "idp" => $idPlayer, "aa" => $armyAmount]);
        updateTable("`$armyType` = `$armyType` + :aa", "city", "id_city = :idc AND id_player = :idp", ["aa" => $armyAmount, "idc" => $idCity, "idp" => $idPlayer]);
        
        LSaveState::foodOutState($idCity);
        LSaveState::saveCityState($idCity);
        
        return [
            "state" => "ok",
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityWounded" => selectFromTable("*", "city_wounded", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0],
            "coinAmount" => $cureUnitPrice*$armyAmount
        ];
    }
    
    function fireCityWounded()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $armyType = validateGameNames($_POST["armyType"]);
        
        if(!in_array($armyType, array_keys(CArmy::$ResourcseNeeded))) 
            return ["state" => "error_0", "TryTOHack" => TryToHack()];
        $armyAmount = selectFromTable("`$armyType`", "city_wounded", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        
        if(!count($armyAmount))
            return ["state" => "error_1", "TryTOHack" => TryToHack()];
        
        $gainRes = [
            "food"  => CArmy::$ResourcseNeeded[$armyType]["food"] * $armyAmount[0][$armyType]/3,
            "wood"  => CArmy::$ResourcseNeeded[$armyType]["wood"] * $armyAmount[0][$armyType]/3,
            "stone" => CArmy::$ResourcseNeeded[$armyType]["stone"]* $armyAmount[0][$armyType]/3,
            "metal" => CArmy::$ResourcseNeeded[$armyType]["metal"]* $armyAmount[0][$armyType]/3,
            "coin"  => CArmy::$ResourcseNeeded[$armyType]["coin"] * $armyAmount[0][$armyType]/3,
        ];
        
        
        
        LSaveState::saveCityState($idCity);
        LCity::addResource($gainRes, $idCity);
        
        updateTable("`$armyType` = 0 ", "city_wounded", "id_city = :idc  AND id_player = :idp", ["idc"=> $idCity, "idp" => $idPlayer]);
        
        insertIntoTable(
                "id_player = :idp, id_city = :idc, army_type = :at, amount = :a", 
                "city_wounded_fired", ["idp" => $idPlayer, "idc" => $idCity, "at" => $armyType, "a" => $armyAmount[0][$armyType]]
                );
        LSaveState::saveCityState($idCity);
        return [
            "state" => "ok",
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0],
            "cityWounded" => selectFromTable("*", "city_wounded", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0]
        ];
    }
    
    
    
    function fireArmy()
    {
        global $idPlayer;
        $idCity     = validateID($_POST["idCity"]);
        $armyType   = validateGameNames($_POST["armyType"]);
        $amount     = validateID($_POST["amount"]);
        $cityArmy   = selectFromTable("army_a, army_b, army_c, army_d, army_e, army_f, wall_a, wall_b, wall_c, spies", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        $resNeeded  = LArmy::neededResources($armyType);
         
        if(!count($cityArmy))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!$resNeeded)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($amount <= 0 || $amount > $cityArmy[0][$armyType])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        $Res = [
            "food"  => $resNeeded["food"] *$amount*0.3, "wood"  => $resNeeded["wood"] *$amount*0.3,
            "stone" => $resNeeded["stone"]*$amount*0.3, "metal" => $resNeeded["metal"]*$amount*0.3,
            "pop"   => $resNeeded["pop"]  *$amount*0.3, "coin"  => $resNeeded["coin"] *$amount*0.3
        ];
        
        LCity::addResource($Res, $idCity);
        updateTable("$armyType = $armyType - $amount", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
    
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]
        ];
    }

    function refreshCityBase()
    {
        
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        $City = selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        
        if(!count($City))
            TryToHack ();
        
        return $City[0];
    
    }
    
    function searchCity()
    {
        
        $NameSeg = validatePlayerWord($_GET["CityName"]);
        
        return
            selectFromTable(
                    "player.name AS PlayerName, player.id_player AS idPlayer, city.name AS CityName, city.id_city AS idCity, player.avatar",
                    "city JOIN player ON city.id_player = player.id_player", "city.name LIKE :s", ["s" => "%$NameSeg%"]);
        
        
        
    }
    
   
    
}
