<?php

class ACityHero
{
    function addFromTheater()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable("*", "hero_theater", "id_hero = :idh", ["idh"=>$idHero]);
        $HeroCount = selectFromTable("COUNT(*) AS c", "hero", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]["c"];
        if(!count($Hero))
            return ["state" =>"error_0", "TryToHack"=> TryToHack()];
        if($HeroCount > CITY_HERO_MAX_COUNT)
            return ["state" =>"error_1", "MaxCount" => CITY_HERO_MAX_COUNT];
        /**$idCity, $lvl, $avatar, $name, $ultraPoint = 0*/
        $idNewHero = LHero::addNew(
                        $Hero[0]["id_city"],
                        $Hero[0]["hero_lvl"], 
                        $Hero[0]["hero_image"],
                        CHero::$HeroNames[$Hero[0]["hero_name"]], 
                        $Hero[0]["ultra_p"]);
        if(!$idNewHero)
            return ["state" =>"error_2"];
        
        deleteTable("hero_theater", "id_hero = :idh", ["idh" => $idHero]);   
        LSaveState::saveCityState($Hero[0]["id_city"]);
        LSaveState::coinOutState($Hero[0]["id_city"]);
        
        return [ 
            "state"        => "ok", 
            "Hero"         => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idNewHero])[0],
            "TheaterHeros" => selectFromTable("hero_name as name, hero_lvl AS lvl, hero_image AS avatar, hero_theater.*", "hero_theater", "id_city = :idc", [ "idc" => $Hero[0]["id_city"]]),
            "City"         => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
            ]; 
    }
    
    
    function getHeroTheater()
    {
        
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        
        return selectFromTable("hero_name as name, hero_lvl AS lvl, hero_image AS avatar, hero_theater.*", "hero_theater", "id_city = :idc ORDER BY ord", ["idc" => $idCity]);
    
    }
    function getCityFullHero()
    {
        return [
            "Hero"      => $this->getCityHero(),
            "HeroArmy"  => $this->getCityHeroArmy(),
            "HeroMedal" => $this->getCityHeroMedal(),
            "HeroEquip" => $this->getCityHeroEquip(),
        ];
    }
    function getCityHero()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "hero", "id_city = :idc AND id_player = :idp ORDER BY ord", ["idc" => $idCity, "idp" => $idPlayer]);
    }
    
    function getCityHeroArmy()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("hero_army.*", "hero_army JOIN hero ON hero.id_hero = hero_army.id_hero", "hero.id_city = :idc AND hero.id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
    }
    
    function getCityHeroMedal()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("hero_medal.*", "hero_medal JOIN hero ON hero.id_hero = hero_medal.id_hero", "hero.id_city = :idc AND hero.id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
    }
    function getCityHeroEquip()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("equip.*, hero.id_city", "equip JOIN hero ON hero.id_hero = equip.id_hero", "hero.id_city = :idc AND hero.id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
    }
    
    
    function refreshHeroTheater()
    {
        
        global $idPlayer;
        
        $idCity = validateID($_POST["idCity"]);
        
       
        $cityTheater = selectFromTable("lvl , last_update", "city_theater", "id_city = :idc", ["idc" => $idCity]);

        if(!count($cityTheater))
            return ["state" => "error_0"];
       
        $heros = selectFromTable("hero_name as name, hero_lvl AS lvl, hero_image AS avatar, hero_theater.*", "hero_theater", "id_city = :idc", [ "idc" => $idCity]);
        $cityTheater[0]["lvl"] = min($cityTheater[0]["lvl"], 30);
        
        
        $refreshTime = max(120 - 4*$cityTheater[0]["lvl"] , 6)*60;

        if((time() < $cityTheater[0]["last_update"] + $refreshTime))
            return [
                "state" => "ok",
                "HeroList" =>$heros,
                "lastUpdate" => $cityTheater[0]["last_update"],
                "lvl" => $cityTheater[0]["lvl"],
                "refreshTime" => $refreshTime
            ];

          

        

        // maximum number of new heros  in theater
        $maxNumHeros = min($cityTheater[0]["lvl"] , 10);
        $number_of_new_heros = min(floor((time() - $cityTheater[0]["last_update"] + 120)/$refreshTime) , $maxNumHeros); 




        $num_to_insert = $maxNumHeros - count($heros) ;
        $num_to_update = $number_of_new_heros - $num_to_insert;


        
        return $this->heroTheaterRef($num_to_update, $num_to_insert, $cityTheater);

    
    }
    
    function refreshHeroTheaterWithLetter()
    {
        
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        
        
        $cityTheater = selectFromTable("lvl , last_update", "city_theater", "id_city = :idc", ["idc" => $idCity]);

        if(!count($cityTheater))
            return ["state" => "error_0"];
        if(!LItem::useItem("rec_letter"))
            return ["state" => "error_1"];
        
       deleteTable("hero_theater", "id_city = :idc", ["idc" => $idCity]);
        
        
       return $this->heroTheaterRef(0,  min($cityTheater[0]["lvl"] , 10), $cityTheater);

    
    }
    
    private function heroTheaterRef($num_to_update, $num_to_insert, $cityTheater)
    {
        $idCity = $_POST["idCity"];
        $TeaterLvl = min($cityTheater[0]["lvl"], 30);
        if($num_to_update > 0)
            updateTable(
                    "hero_name = FLOOR(RAND()*25), hero_lvl =  FLOOR(1+RAND() * 5 *{$TeaterLvl}) ,"
                    . "hero_image = FLOOR( RAND()*19), ultra_p = FLOOR( GREATEST( CEIL( 1 + RAND()*-90) , 0) * (3 + RAND()*4))",
                    "hero_theater",
                    "id_city = :idc ORDER BY time_stamp ASC LIMIT $num_to_update", ["idc" => $idCity]);

           


        

        for($index = 0 ;  $index < $num_to_insert ; $index++):
            
            insertIntoTable(
                    "hero_name = FLOOR(RAND()*25) , hero_lvl = FLOOR(1+RAND() * 5 *{$TeaterLvl}) , hero_image = FLOOR( RAND()*19),"
                    . "ultra_p = FLOOR( GREATEST( CEIL( 1 + RAND()*-90) , 0) * (3 + RAND()*4)) , id_city = :idc",
                    "hero_theater", ["idc" => $idCity]
                    );

            

        endfor;


        if($num_to_update + $num_to_insert >= 1)
            updateTable("last_update = ".time(), "city_theater", "id_city = :idc", ["idc" => $idCity]);
        





        $heros = selectFromTable("hero_name as name, hero_lvl AS lvl, hero_image AS avatar, hero_theater.*", "hero_theater", "id_city = :idc", [ "idc" => $idCity]);

        return [
                    "state" => "ok",
                    "HeroList" =>$heros,
                    "lastUpdate" => time(),
                    "refreshTime" => max(120 - 4*$TeaterLvl , 6)*60,
                    "lvl" => $TeaterLvl
                ];
        
    }
    
    
    function getHeroArmy()
    {
        
        global $idPlayer;
        $idHero = validateID($_GET["idHero"]);
        
        return selectFromTable("*", "hero_army", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer])[0];
    
    }
}
