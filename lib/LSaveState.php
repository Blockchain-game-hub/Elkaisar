<?php

class LSaveState
{
    
    static public $ResourceInEffct = [
        "food"  => ["ResIn" => "food_in",  "ResOut" => "food_out",  "Study" => "farming", "PlayerState" => "wheat", "LaborCap" => 10, "ResBarr" => WUT_RIVER_LVL_1 ." AND ".WUT_RIVER_LVL_10 ],
        "wood"  => ["ResIn" => "wood_in",  "ResOut" => "wood_out",  "Study" => "wooding", "PlayerState" => "wood",  "LaborCap" => 10, "ResBarr" => WUT_WOODS_LVL_1 ." AND ".WUT_WOODS_LVL_10 ],
        "stone" => ["ResIn" => "stone_in", "ResOut" => "stone_out", "Study" => "stoning", "PlayerState" => "stone", "LaborCap" => 10, "ResBarr" => WUT_DESERT_LVL_1." AND ".WUT_DESERT_LVL_10],
        "metal" => ["ResIn" => "metal_in", "ResOut" => "metal_out", "Study" => "mining",  "PlayerState" => "metal", "LaborCap" => 10, "ResBarr" => WUT_MOUNT_LVL_1 ." AND ".WUT_MOUNT_LVL_10 ]
    ];

   
    
    
   
    
    
    static  function saveCityState($idCity)
    {
        $now = time();
        
        return updateTable(
                "coin = GREATEST( LEAST( coin   + (CAST(coin_in   AS SIGNED) - CAST(coin_out   AS SIGNED))*($now - LS)/3600 , GREATEST( coin_cap , coin )  ) , 0)," 
                . " food  = GREATEST( LEAST( food   + (CAST(food_in   AS SIGNED) - CAST(food_out   AS SIGNED))*($now - LS)/3600 , GREATEST( food_cap  , food  )  ) , 0) , "
                . " wood  = GREATEST( LEAST( wood   + (CAST(wood_in   AS SIGNED) - CAST(wood_out   AS SIGNED))*($now - LS)/3600 , GREATEST( wood_cap  , wood  )  ) , 0) , "
                . " stone = GREATEST( LEAST( stone  + (CAST(stone_in  AS SIGNED) - CAST(stone_out  AS SIGNED))*($now - LS)/3600 , GREATEST( stone_cap , stone )  ) , 0) , "
                . " metal = GREATEST( LEAST( metal  + (CAST(metal_in  AS SIGNED) - CAST(metal_out  AS SIGNED))*($now - LS)/3600 , GREATEST( metal_cap , metal )  ) , 0) , " 
                . " LS = $now" , "city", "id_city = :idc", ["idc" => $idCity]);
        
    }
    
    
   
  
    
    static function foodOutState($idCity)
    { 
        global $idPlayer;
        $Heros = selectFromTable("hero_army.*", "hero_army JOIN hero ON hero.id_hero = hero_army.id_hero", "hero.id_city = :idc", ["idc" => $idCity]);
        
        $heroFood = 0;
        foreach ($Heros as $one){
            $heroFood += CArmy::$FoodEat[$one["f_1_type"]]*$one["f_1_num"];
            $heroFood += CArmy::$FoodEat[$one["f_2_type"]]*$one["f_2_num"];
            $heroFood += CArmy::$FoodEat[$one["f_3_type"]]*$one["f_3_num"];
            $heroFood += CArmy::$FoodEat[$one["b_1_type"]]*$one["b_1_num"];
            $heroFood += CArmy::$FoodEat[$one["b_2_type"]]*$one["b_2_num"];
            $heroFood += CArmy::$FoodEat[$one["b_3_type"]]*$one["b_3_num"];
        }
        
        /*if(existInTable("city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]))
            return updateTable(
               "food_out = "
               . "LEAST("
               . "(  army_a * 4 + army_b * 18 + army_c * 36 +  army_d * 5 + army_e * 20 + army_f * 150 + :hf + 0.03*food_in) *"
               . " ( 1 - (SELECT  supplying FROM  player_edu WHERE id_player = :idp)*3/100 ) - 0.03*food_in,"
               . " food_in)", 
               "city", "id_city = :idc", 
               ["idc" => $idCity, "idp"=> $idPlayer, "hf" =>$heroFood ]
               );
         */
            return updateTable(
               "food_out = "
               . "LEAST("
               . "(  army_a * 4 + army_b * 18 + army_c * 36 +  army_d * 5 + army_e * 20 + army_f * 150 + :hf) *"
               . " ( 1 - (SELECT  supplying FROM  player_edu WHERE id_player = :idp)*3/100 ),"
               . " food_in)", 
               "city", "id_city = :idc", 
               ["idc" => $idCity, "idp"=> $idPlayer, "hf" =>$heroFood ]
               );
    }
  

    static function getConsoleEffect($idCity)
    {
      $consoleEffect = 0;
        
        $console_effect = selectFromTable("point_a, point_a_plus, medal_ceasro", "hero JOIN city ON city.console = hero.id_hero JOIN hero_medal ON hero_medal.id_hero = city.console", "city.id_city = :idc", ["idc" => $idCity]);
        if(count($console_effect) > 0){
            if($console_effect[0]["medal_ceasro"] > time()){
                $consoleEffect = ($console_effect[0]["point_a"] + $console_effect[0]["point_a_plus"])*1.25*0.5 ;
            }else{
                $consoleEffect = ($console_effect[0]["point_a"] + $console_effect[0]["point_a_plus"])*0.5;
            }

        }  
        return $consoleEffect/100;
        
    }
  
    static function getResFromColonize($idCity)
    {
        $Cities = selectFromTable("id_city_colonized", "city_colonize", "id_city_colonizer = :idc", ["idc" => $idCity]);
        $Res = [
            "food" => 0,
            "wood" => 0,
            "stone" => 0,
            "metal" => 0,
            "coin"  => 0
        ];
        foreach ($Cities as $oneCity)
        {
            $City = selectFromTable("food_in, wood_in, stone_in, metal_in, coin_in", "city", "id_city = :idc", ["idc" => $oneCity["id_city_colonized"]])[0];
            
            $Res["food"]  += $City["food_in"];
            $Res["wood"]  += $City["wood_in"];
            $Res["stone"] += $City["stone_in"];
            $Res["metal"] += $City["metal_in"];
            $Res["coin"]  += $City["coin_in"];
        }
        return $Res;
    }
    
    
    static function afterCityColonizer($idColonizer)
    {
        LSaveState::saveCityState ($idColonizer);
        LSaveState::coinInState   ($idColonizer);
        LSaveState::resInState    ($idColonizer, "food");
        LSaveState::resInState    ($idColonizer, "wood");
        LSaveState::resInState    ($idColonizer, "stone");
        LSaveState::resInState    ($idColonizer, "metal");
    }
    
    static function afterCityColonized($idColonized)
    {
        
        LSaveState::saveCityState($idColonized);
        LSaveState::foodOutState ($idColonized);
        LSaveState::coinOutState ($idColonized);
        LSaveState::resOutState  ($idColonized, "wood");
        LSaveState::resOutState  ($idColonized, "stone");
        LSaveState::resOutState  ($idColonized, "metal");
    }
    
    static function resInState($idCity , $res)
    {
        global $idPlayer;
        $ColoEff = static::getResFromColonize($idCity);
        $cityJops       = selectFromTable("*", "city_jop", "id_city = $idCity")[0];
        $realJopNum = [];
        
        $takenPop = 0;
        
        $realJopNum["food"]  = $cityJops["food"]*$cityJops["food_rate"]/100;
        $realJopNum["wood"]  = $cityJops["wood"]*$cityJops["wood_rate"]/100;
        $realJopNum["stone"] = $cityJops["stone"]*$cityJops["stone_rate"]/100;
        $realJopNum["metal"] = $cityJops["metal"]*$cityJops["metal_rate"]/100;
        
        
        if($res == "food")
            $takenPop    = 0 ;
        elseif( $res == "wood")
            $takenPop = $realJopNum["food"] ;
        else if($res == "stone")
            $takenPop = $realJopNum["food"] + $realJopNum["wood"] ;
        else if($res == "metal")
            $takenPop = $realJopNum["food"] + $realJopNum["wood"] + $realJopNum["stone"] ;
            
        $matrialEff = selectFromTable(static::$ResourceInEffct[$res]["PlayerState"], "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0];
        
        $ratProMat  = $matrialEff[static::$ResourceInEffct[$res]["PlayerState"]] > time() ? 0.25 : 0 ;
        $barEff     = selectFromTable("IFNULL(SUM(world.l),0 ) as lvlsum", "world JOIN city_bar ON world.x = city_bar.x_coord AND  world.y = city_bar.y_coord", "city_bar.id_city = :idc AND world.ut BETWEEN  ".static::$ResourceInEffct[$res]["ResBarr"], ["idc" => $idCity])[0]["lvlsum"]*0.03;
        $PlayerStudy = selectFromTable(static::$ResourceInEffct[$res]["Study"], "player_edu", " id_player = :idp", ["idp" => $idPlayer])[0][static::$ResourceInEffct[$res]["Study"]]*0.07;
       
        $quary = static::$ResourceInEffct[$res]["ResIn"]." = (LEAST( GREATEST(pop - $takenPop , 0) , {$realJopNum[$res]} ) + 15)  *:lc* ( 1 + :ce + $ratProMat + $barEff + $PlayerStudy ) + ". $ColoEff[$res];
            
        
        
    
        return updateTable( $quary, "city", "id_city = :idc", ["lc" => static::$ResourceInEffct[$res]["LaborCap"], "idc" => $idCity, "ce" => static::getConsoleEffect($idCity)]);
    }
    
    static function resOutState($idCity , $res)
    {
        return;
        if(existInTable("city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]))
            return updateTable( static::$ResourceInEffct[$res]["ResOut"]." = ".static::$ResourceInEffct[$res]["ResIn"]."*0.03", "city", "id_city = :idc", ["idc" => $idCity]);
    }
    
    
    
    
    static function coinInState($idCity)
    {
        global $idPlayer;
        $matrialEffect = selectFromTable("coin", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0];
        $ColoEff = static::getResFromColonize($idCity);
        $ratio_pro_mat = $matrialEffect["coin"] > time() ? 0.25 : 0 ;
        $ConsoleEff = static::getConsoleEffect($idCity);
        $statment = " coin_in = ( (SELECT accounting FROM player_edu WHERE id_player = :idp)*10/100) * (taxs/100)*pop + (taxs/100)*pop + :ce *(taxs/100)*pop   + (taxs/100)*pop*$ratio_pro_mat + {$ColoEff["coin"]}";
        updateTable($statment, "city", "id_city = :idc ", ["idp" => $idPlayer, "idc" => $idCity, "ce" =>$ConsoleEff]);
        
    }
    
    static function coinOutState($idCity)
    {
       
        global $idPlayer;
        $totalLvls = selectFromTable(" SUM(lvl) as c", "hero", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]["c"];
        
       /* if(existInTable("city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]))
            return updateTable("coin_out = LEAST(coin_in + coin_in*0.03, :c)", "city", "id_city = :idc", ["c" => $totalLvls*10, "idc" => $idCity]);*/
        
        return updateTable("coin_out = LEAST(coin_in, :c)", "city", "id_city = :idc", ["c" => $totalLvls*10, "idc" => $idCity]);
    }
    
    public static function storeRatio($idCity){
        
        static::saveCityState($idCity);
        LCity::refreshStoreCap($idCity);
        
        $storage = selectFromTable("*", "city_storage", "id_city = :idc", ["idc" => $idCity]);
         
        $food_cap  = $storage[0]["total_cap"]*$storage[0]["food_storage_ratio"]/100;
        $wood_cap  = $storage[0]["total_cap"]*$storage[0]["wood_storage_ratio"]/100;
        $stone_cap = $storage[0]["total_cap"]*$storage[0]["stone_storage_ratio"]/100;
        $metal_cap = $storage[0]["total_cap"]*$storage[0]["metal_storage_ratio"]/100;
        
        $quary = "food_cap = {$food_cap} , wood_cap = $wood_cap , metal_cap = $metal_cap ,"
                . " stone_cap = $stone_cap";
        
        updateTable($quary, "city", "id_city = :idc", ["idc" => $idCity]);
        
    }

    
    
}

