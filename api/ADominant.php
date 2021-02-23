<?php
class ADominant
{
    
    function getCityColonizer()
    {
        global $idPlayer;
        return selectFromTable(
                "city_colonize.*, city.name AS CityName, player.name AS PlayerName, city.x, city.y",
                "city_colonize JOIN city ON city.id_city = city_colonize.id_city_colonized JOIN player ON player.id_player = city_colonize.id_colonized",
                "city_colonize.id_colonizer = :idp", ["idp" => $idPlayer]);
    
    }
    
    function getCityColonized()
    {
        global $idPlayer;
        return selectFromTable(
                "city_colonize.*, city.name AS CityName, player.name AS PlayerName, city.x, city.y",
                "city_colonize JOIN city ON city.id_city = city_colonize.id_city_colonizer JOIN player ON player.id_player = city_colonize.id_colonizer",
                "city_colonize.id_colonized = :idp", ["idp" => $idPlayer]);
    
    }
    
    function abondonColonizedCity()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $Colonize = selectFromTable("*", "city_colonize",  "id_colonizer = :idp AND id_city_colonized = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        
        if(!count($Colonize))
            return ["state" => "error_0"];
        
        deleteTable("city_colonize", "id_colonizer = :idp AND id_city_colonized = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        LSaveState::saveCityState($idCity);
        LSaveState::afterCityColonized($idCity);
        LSaveState::afterCityColonizer($Colonize[0]["id_city_colonizer"]);
        
        LSaveState::afterCityColonizer($idCity);
        LSaveState::afterCityColonized($Colonize[0]["id_city_colonizer"]);
        
        return [
            "state" => "ok"
        ];
    }
    function fireColonizer()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $Colonize = selectFromTable("*", "city_colonize",  "id_colonized = :idp AND id_city_colonized = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        
        if(!count($Colonize))
            return ["state" => "error_0"];
        if(!LItem::useItem("freedom_help", 1))
            return ["state" => "error_1"];
        
        deleteTable("city_colonize", "id_colonizer = :idp AND id_city_colonized = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        LSaveState::saveCityState($idCity);
        LSaveState::afterCityColonized($idCity);
        LSaveState::afterCityColonizer($Colonize[0]["id_city_colonizer"]);
        
        LSaveState::afterCityColonized($Colonize[0]["id_city_colonizer"]);
        LSaveState::afterCityColonizer($idCity);
        
        
        return [
            "state" => "ok"
        ];
    }
    
    
}