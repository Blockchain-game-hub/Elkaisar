<?php

class ACity
{
    
    function stablizingPop()
    {
        global $idPlayer;
        $Cities = selectFromTable("pop, pop_cap, pop_max, id_city, id_player", "city", "(pop > pop_max + 10 OR pop < pop_max - 10)  LIMIT 750");
        $CityPlayer = [];
        foreach ($Cities as $oneCity)
        {
           
            $idPlayer = $oneCity["id_player"];
            
            if($oneCity["pop"] > $oneCity["pop_max"])
                $this->pullPopDown ($oneCity);
            else 
                $this->pullPopUp ($oneCity);
            
            LSaveState::saveCityState($oneCity["id_city"]);
            LSaveState::resInState   ($oneCity["id_city"], "food");
            LSaveState::resInState   ($oneCity["id_city"], "wood");
            LSaveState::resInState   ($oneCity["id_city"], "stone");
            LSaveState::resInState   ($oneCity["id_city"], "metal");
            LSaveState::coinInState  ($oneCity["id_city"]);
            
            $CityPlayer[] = [
                "idPlayer" => $oneCity["id_player"],
                "idCity"   => $oneCity["id_city"]
            ];
        }
        
        return $CityPlayer;
    }
    
    private function pullPopUP($City)
    {
        $popToAdd = 0.05*$City["pop_max"];
        updateTable("pop = :p", "city", "id_city = :idc", ["p" => min($City["pop_max"], $popToAdd + $City["pop"]),  "idc" => $City["id_city"]]);
        
    }
    
    private function pullPopDown($City)
    {
        $popToAdd = 0.05*$City["pop_max"];
        updateTable("pop = :p", "city", "id_city = :idc", ["p" => max($City["pop_max"], $City["pop"] -  $popToAdd),  "idc" => $City["id_city"]]);
        
    }
    
    
    function stablizingLoy()
    {
        $Cities = selectFromTable("loy, loy_max, loy_lss, id_city, id_player", "city", "loy_max != loy LIMIT 500");
        
        $CityPlayer = [];
        
        foreach ($Cities as $one)
        {
            
            if($one["loy"] < $one["loy_max"])
                updateTable ("loy = :l", "city", "id_city = :idc", ["idc" => $one["id_city"], "l" => min($one["loy"] + 3 , 100, $one["loy_max"])]);
            else if($one["loy"] > $one["loy_max"])
                updateTable ("loy = :l", "city", "id_city = :idc", ["idc" => $one["id_city"], "l" => min(max($one["loy"] - 3 , $one["loy_max"]), 100)]);
            
            $CityPlayer[] = [
                "idPlayer" => $one["id_player"],
                "idCity"   => $one["id_city"]
            ];
        }
        
        return $CityPlayer;
    }
    
    function getWorldCity()
    {
        $offset = validateID($_POST["offset"]) * 250;
        return selectFromTable(
                "world.x, world.y, world.ut AS t,city.id_city AS idCity, city.name AS CityName, player.name AS PlayerName, "
                . "city.flag AS FlagName, player.guild AS GuildName, player.id_guild AS idGuild, city.lvl, player.id_player AS idPlayer", 
                "world JOIN city ON city.x = world.x AND city.y = world.y JOIN player ON player.id_player = city.id_player",
                "world.ut BETWEEN :s AND :e LIMIT 250 offset $offset",
                ["s" => WUT_CITY_LVL_0, "e" => WUT_CITY_LVL_3]);
    }
}