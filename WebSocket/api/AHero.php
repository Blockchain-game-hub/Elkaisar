<?php

class AHero
{
    
    function addPower()
    {
        $Heros = selectFromTable("id_hero, id_player, power, power_max", "hero", "power < power_max  LIMIT 500");
        $Players = [];
        foreach ($Heros as $oneHero)
        {
            if(!array_key_exists($oneHero["id_player"], $Players))
                $Players[$oneHero["id_player"]] = [];
            
            
            $powerToAdd = min(1 + $oneHero["power"], $oneHero["power_max"]);
            updateTable("power = :p", "hero", "id_hero = :idh", ["idh" => $oneHero["id_hero"], "p" => $powerToAdd]);
            
            
            $Players[$oneHero["id_player"]][] = [
                "idHero" => $oneHero["id_hero"],
                "power" => $powerToAdd
            ];
        }
        return $Players;
        
    }
    
}