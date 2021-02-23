<?php

class ACityMarketTrans
{
    function getCityTransportResource()
    {
        $idCity   = validateID($_GET["idCity"]);
        return LCityMarket::getTransList($idCity);
    }
    function getCityTransportBackResource()
    {
        $idCity   = validateID($_GET["idCity"]);
        return LCityMarket::getTransBackList($idCity);
    }


    function transportResource(){
       
        global $idPlayer;
        $food          = validateID($_POST["food"]      );
        $wood          = validateID($_POST["wood"]      );
        $stone         = validateID($_POST["stone"]     );
        $metal         = validateID($_POST["metal"]     );
        $coin          = validateID($_POST["coin"]      );
        $idCityTo      = validateID($_POST["idCityTo"]  );
        $idCityFrom    = validateID($_POST["idCityFrom"]);
        $Market        = LCityBuilding::getBuildingAtPlace("market", $idCityFrom);
        $OutTransCount = selectFromTable("COUNT(*) as count", "market_transport", "id_city_from = :idc", ["idc" => $idCityFrom])[0]['count'] +
        selectFromTable("COUNT(*) as count", "market_transport_back", "id_city_to = :idc", ["idc" => $idCityFrom])[0]['count'] ;
        

        if($food < 0 || $wood < 0 || $stone < 0 || $metal < 0 || $coin < 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($OutTransCount >= min($Market["Lvl"], MARKET_MAX_OUT_TRANS))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($food + $wood + $stone  + $metal + $coin > $Market["Lvl"]*100000)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(!LCity::isResourceTaken([ "food"  => $food, "wood"  => $wood, "stone" => $stone, "metal" => $metal, "coin"  => $coin, ], $idCityFrom))
            return ["state" => "error_3"];
        
        $distance = LWorld::distanceBetweenCities($idCityFrom, $idCityTo);
        $timeArrive = time() + $distance/300;
        $loseRatio = max(20 - $Market["Lvl"] , 0);
        $loseRatio += max(floor($distance/1200000) - $Market["Lvl"] , 0);
        
        $quary = "id_city_from = :idcf , id_city_to = :idct , time_arrive = :ta, food = :f, wood = :w , stone = :s, metal = :m , coin = :c, lose_ratio = :lr";
        
        insertIntoTable($quary, 'market_transport', 
                [
                    "idcf" => $idCityFrom, "idct" => $idCityTo,
                    "ta" => $timeArrive, "f" => $food, "w" => $wood,
                    "s" => $stone, "m" => $metal, "c" =>  $coin, "lr" => $loseRatio
                ]); 
        
        return [
                'state'=>"ok",
                "cityRes"=> selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCityFrom])[0],
                "transList" => LCityMarket::getTransList($idCityFrom)
                ];
        
    }
    
    function speedUpTransport(){
       
        global $idPlayer;
        $idTrans = validateID($_POST["idTrans"]);
        $idCity  = validateID($_POST["idCity"]);
        $Trans   = selectFromTable(
                "market_transport.*", 
                "market_transport JOIN city ON city.id_city = market_transport.id_city_to OR city.id_city = market_transport.id_city_from",
                "market_transport.id_trans = :id AND city.id_player = :idp", ["idp" => $idPlayer , "id" => $idTrans]);
        if(!count($Trans))
            return ["state" => "error_0"];
        if(($Trans[0]["acce"] != 0))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!LItem::useItem("shopping_car"))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        updateTable("time_arrive = time_arrive - (time_arrive - :t)/2 , acce  = 1", "market_transport", "id_trans = :id", ["t" => time(), "id" => $idTrans]);
        return [
                'state'=>"ok",
                "transList" => LCityMarket::getTransList($idCity)
                ];
    }
    
}

