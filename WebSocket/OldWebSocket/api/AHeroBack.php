<?php

class AHeroBack
{
    
    function Back()
    {
        global $idPlayer;
        $now = time() + 1;
        $Heros = selectFromTable(
                "hero_back.*, hero.id_hero ,hero.name AS HeroName, city.x, city.y, city.name AS CityName, hero.id_city",
                "hero_back JOIN hero ON hero.id_hero = hero_back.id_hero JOIN city ON city.id_city = hero.id_city",
                "time_back <= $now");
        
        $HerosToSend = [];
        foreach ($Heros as $one){
            
            $idPlayer = $one["id_player"];
           
            if($one["task"] == BATTEL_TASK_SUPPORT)
                $HerosToSend[] = $this->Support ($one);
            else if($one["task"] == BATTEL_TASK_HERO_TRANS)
                $HerosToSend[] = $this->TransHero ($one);
            else
                $HerosToSend[] = $this->backHome ($one);
            
            
            deleteTable("hero_back", "id_hero = :idh", ["idh" => $one["id_hero"]]);
            
        }

        return $HerosToSend;
        
    }
    
    
    private function Support($Hero) 
    {
        updateTable("in_city = :inc", "hero", "id_hero = :idh", ["idh" => $Hero["id_hero"], "inc" => HERO_IN_GARISON]);
        $ord = 0;
        $LastHero = selectFromTable("ord", "world_unit_garrison", "x_coord = :x AND y_coord = :y ORDER BY ord DESC LIMIT 1", ["x" => $Hero["y_to"], "y" => $Hero["x_to"]]);
        if(!count($LastHero))
            $ord = $LastHero[0]["ord"] + 1;
        insertIntoTable(
                "x_coord = :x , y_coord = :y , id_hero = :idh , id_player = :idp", 
                "world_unit_garrison", 
                [
                    "x"   => $Hero["x_to"], 
                    "y"   => $Hero["y_to"],
                    "idh" => $Hero["id_hero"], 
                    "idp" => $Hero["id_player"]  
                ]);
            return [
                "idPlayer" => $Hero["id_player"],
                "inCity"   => HERO_IN_GARISON,
                "idHero"   => $Hero["id_hero"],
                "HeroName" => $Hero["HeroName"],
                "CityName" => $Hero["CityName"],
                "xTo"      => $Hero["x_to"],
                "yTo"      => $Hero["y_to"],
                "xFrom"    => $Hero["x_from"],
                "yFrom"    => $Hero["y_from"],
                "Task"     => $Hero["task"]
            ];
    }
    
    private function TransHero($Hero)
    {
        
        $CityTo = selectFromTable(
                "id_city",
                "city", " x = :x AND y = :y AND id_player = :idp",
                ["x" => $Hero["x_to"], "y" => $Hero["y_to"], "idp" => $Hero["id_player"]]
                );
        
        if(!count($CityTo)){
            updateTable("in_city = :inc", "hero", "id_hero = :idh", ["idh" => $Hero["id_hero"], "inc" => HERO_IN_CITY]);
            deleteTable("hero_back",  "id_hero = :idh", ["idh" => $Hero["id_hero"]]);
            return [
                "idPlayer" => $Hero["id_player"],
                "inCity"   => HERO_IN_CITY,
                "idHero"   => $Hero["id_hero"],
                "HeroName" => $Hero["HeroName"],
                "CityName" => $Hero["CityName"],
                "xTo"      => $Hero["x_to"],
                "yTo"      => $Hero["y_to"],
                "xFrom"    => $Hero["x_from"],
                "yFrom"    => $Hero["y_from"],
                "Task"     => $Hero["task"]
            ];
        }
        

        $idCity = $CityTo[0]["id_city"];
        $LastOrder = selectFromTable("ord", "hero", "id_city = :idc ORDER BY ord DESC", ["idc" => $idCity]);
        
        updateTable("id_city = :idc, in_city = :inc, ord = :o", "hero", "id_hero = :idh", ["idc" => $idCity, "inc" => HERO_IN_CITY, "idh" => $Hero["id_hero"], "o" => count($LastOrder) ? $LastOrder[0]["ord"] + 1 : 1]);
        
        LHero::reOrderHero($idCity);
        LHero::reOrderHero($Hero["id_city"]);
        LSaveState::foodOutState($idCity);
        LSaveState::coinOutState($idCity);
        LSaveState::foodOutState($Hero["id_city"]);
        LSaveState::coinOutState($Hero["id_city"]);
        
        return [
                "idPlayer" => $Hero["id_player"],
                "inCity"   => HERO_IN_CITY,
                "idHero"   => $Hero["id_hero"],
                "HeroName" => $Hero["HeroName"],
                "CityName" => $Hero["CityName"],
                "xTo"      => $Hero["x_to"],
                "yTo"      => $Hero["y_to"],
                "xFrom"    => $Hero["x_from"],
                "yFrom"    => $Hero["y_from"],
                "Task"     => $Hero["task"]
            ];
    }
    
    private function backHome($Hero)
    {
        
       updateTable("in_city = :inc", "hero", "id_hero = :idh", ["idh" => $Hero["id_hero"], "inc" => HERO_IN_CITY]);
        
        return [
                "idPlayer" => $Hero["id_player"],
                "inCity"   => HERO_IN_CITY,
                "idHero"   => $Hero["id_hero"],
                "HeroName" => $Hero["HeroName"],
                "CityName" => $Hero["CityName"],
                "xTo"      => $Hero["x_to"],
                "yTo"      => $Hero["y_to"],
                "xFrom"    => $Hero["x_from"],
                "yFrom"    => $Hero["y_from"],
                "Task"     => $Hero["task"]
            ];
    }
}