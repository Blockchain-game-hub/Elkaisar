<?php

class AHeroArmy
{
    
    function transArmyFromHeroToCity()
    {
        
        global $idPlayer;
        $idHero    = validateID($_POST["idHero"]);
        $ArmyPlace = validateGameNames($_POST["ArmyPlace"]);
        $amount    = validateID($_POST["amount"]);
        
        $Hero = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero Join hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!isset($Hero[0][($ArmyPlace."_num")]))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0][($ArmyPlace."_type")] < 1 || $Hero[0][($ArmyPlace."_type")] > 6)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($amount < 0)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        if($Hero[0][($ArmyPlace."_num")] < $amount)
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_5", "TryToHack" => TryToHack()];
        
        updateTable(
                "`".CArmy::$ArmyCityPlace[$Hero[0][($ArmyPlace."_type")]]."` = `".CArmy::$ArmyCityPlace[$Hero[0][($ArmyPlace."_type")]]."` + :a",
                "city", "id_city = :idc AND id_player = :idp", ["idc" => $Hero[0]["id_city"], "idp" => $idPlayer, "a" => $amount]);
        
        if($Hero[0][($ArmyPlace."_num")] == $amount)
            updateTable ("`".$ArmyPlace."_type` = 0, `".$ArmyPlace."_num` = 0", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        else 
            updateTable ("`".$ArmyPlace."_num` = `".$ArmyPlace."_num` - :a", "hero_army", "id_hero = :idh", ["idh" => $idHero, "a" => $amount]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroArmy" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero])[0],
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    }
    
    function transArmyFromCityToHero()
    {
        
        global $idPlayer;
        $idHero    = validateID($_POST["idHero"]);
        $ArmyPlace = validateGameNames($_POST["ArmyPlace"]);
        $ArmyType  = validateGameNames($_POST["ArmyType"]);
        $amount    = validateID($_POST["amount"]);
        
        $Hero = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $CityArmy = selectFromTable("`$ArmyType`", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0][$ArmyType];
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!isset($Hero[0][($ArmyPlace."_num")]))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0][($ArmyPlace."_type")] != 0 && $ArmyType != CArmy::$ArmyCityPlace[$Hero[0][($ArmyPlace."_type")]])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($amount < 0)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        if(LHero::emptyPlacesSize($idHero) < $amount*CArmy::$ArmyCap[CArmy::$ArmyCityToArmyHero[$ArmyType]])
            return ["state" => "error_4", "TryToHack" => TryToHack(), "emptySize" => LHero::emptyPlacesSize($idHero)];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_5", "TryToHack" => TryToHack()];
        if($CityArmy < $amount)
            return ["state" => "error_6", "TryToHack" => TryToHack()];
        
        updateTable(
                "`$ArmyType` = `$ArmyType` - :a",
                "city", "id_city = :idc AND id_player = :idp", ["idc" => $Hero[0]["id_city"], "idp" => $idPlayer, "a" => $amount]);
        
        
        updateTable ("`".$ArmyPlace."_type` = :t, `".$ArmyPlace."_num` = `".$ArmyPlace."_num` + :a", "hero_army", "id_hero = :idh", ["idh" => $idHero, "t" => CArmy::$ArmyCityToArmyHero[$ArmyType], "a" => $amount]);
        
        
        LSaveState::saveCityState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroArmy" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero])[0],
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    }
    
    
    function transArmyFromHeroToHero()
    {
        global $idPlayer;
        $idHeroFrom    = validateID($_POST["idHeroFrom"]);
        $idHeroTo      = validateID($_POST["idHeroTo"]);
        $ArmyPlaceTo   = validateGameNames($_POST["ArmyPlaceTo"]);
        $ArmyPlaceFrom = validateGameNames($_POST["ArmyPlaceFrom"]);
        $amount        = validateGameNames($_POST["amount"]);
        $HeroFrom      = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHeroFrom, "idp" => $idPlayer]);
        $HeroTo        = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHeroTo, "idp" => $idPlayer]);
       
        if(!count($HeroFrom) || !count($HeroTo))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!isset($HeroTo[0][($ArmyPlaceTo."_num")]) || !isset($HeroFrom[0][($ArmyPlaceFrom."_num")]))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($HeroTo[0][($ArmyPlaceTo."_type")] != 0 && $HeroFrom[0][($ArmyPlaceFrom."_type")] != $HeroTo[0][($ArmyPlaceTo."_type")])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($amount < 0 || $amount > $HeroFrom[0][($ArmyPlaceFrom."_num")])
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        if(LHero::emptyPlacesSize($idHeroTo) < $amount*CArmy::$ArmyCap[$HeroFrom[0][($ArmyPlaceFrom."_type")]])
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        if($HeroFrom[0]["in_city"] != HERO_IN_CITY || $HeroTo[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_5", "TryToHack" => TryToHack()];
        
        if($HeroFrom[0][($ArmyPlaceFrom."_num")] == $amount)
            updateTable ("`".$ArmyPlaceFrom."_type` = :t, `".$ArmyPlaceFrom."_num` = `".$ArmyPlaceFrom."_num` - :a", "hero_army", "id_hero = :idh", ["idh" => $idHeroFrom, "t" => 0, "a" => $amount]);
        else 
            updateTable ("`".$ArmyPlaceFrom."_type` = :t, `".$ArmyPlaceFrom."_num` = `".$ArmyPlaceFrom."_num` - :a", "hero_army", "id_hero = :idh", ["idh" => $idHeroFrom, "t" => $HeroFrom[0][($ArmyPlaceFrom."_type")], "a" => $amount]);
        updateTable ("`".$ArmyPlaceTo."_type`   = :t, `".$ArmyPlaceTo."_num`   = `".$ArmyPlaceTo."_num`   + :a", "hero_army", "id_hero = :idh", ["idh" => $idHeroTo,   "t" => $HeroFrom[0][($ArmyPlaceFrom."_type")], "a" => $amount]);
        return [
            "state" => "ok",
            "HeroArmyFrom" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHeroFrom])[0],
            "HeroArmyTo"   => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHeroTo])[0]
        ];
    }
    
    function swapHeroArmy()
    {
        global $idPlayer;
        $idHeroLeft    = validateID($_POST["idHeroLeft"]);
        $idHeroRight   = validateID($_POST["idHeroRight"]);
        $HeroLeft      = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHeroLeft,  "idp" => $idPlayer]);
        $HeroRight     = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHeroRight, "idp" => $idPlayer]);
        $HeroLeftCap   = LHero::heroFullCap($idHeroLeft);
        $HeroLeftFill  = LHero::filledPlacesSize($idHeroLeft);
        $HeroRightCap  = LHero::heroFullCap($idHeroRight);
        $HeroRightfill = LHero::filledPlacesSize($idHeroRight);
        
        if(!count($HeroLeft) || !count($HeroRight))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($HeroLeftCap < $HeroRightfill || $HeroRightCap < $HeroLeftFill)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($HeroLeft[0]["in_city"] != HERO_IN_CITY || $HeroRight[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        $quary_1 = "f_1_type = {$HeroLeft[0]["f_1_type"]} , f_1_num = {$HeroLeft[0]["f_1_num"]}, "
                . " f_2_type = {$HeroLeft[0]["f_2_type"]} , f_2_num = {$HeroLeft[0]["f_2_num"]}, "
                . " f_3_type = {$HeroLeft[0]["f_3_type"]} , f_3_num = {$HeroLeft[0]["f_3_num"]}, "
                . " b_1_type = {$HeroLeft[0]["b_1_type"]} , b_1_num = {$HeroLeft[0]["b_1_num"]}, "
                . " b_2_type = {$HeroLeft[0]["b_2_type"]} , b_2_num = {$HeroLeft[0]["b_2_num"]}, "
                . " b_3_type = {$HeroLeft[0]["b_3_type"]} , b_3_num = {$HeroLeft[0]["b_3_num"]}";
        
        $quary_2 = "f_1_type = {$HeroRight[0]["f_1_type"]} , f_1_num = {$HeroRight[0]["f_1_num"]}, "
                  ."f_2_type = {$HeroRight[0]["f_2_type"]} , f_2_num = {$HeroRight[0]["f_2_num"]}, "
                  ."f_3_type = {$HeroRight[0]["f_3_type"]} , f_3_num = {$HeroRight[0]["f_3_num"]}, "
                  ."b_1_type = {$HeroRight[0]["b_1_type"]} , b_1_num = {$HeroRight[0]["b_1_num"]}, "
                  ."b_2_type = {$HeroRight[0]["b_2_type"]} , b_2_num = {$HeroRight[0]["b_2_num"]}, "
                  ."b_3_type = {$HeroRight[0]["b_3_type"]} , b_3_num = {$HeroRight[0]["b_3_num"]}";
          updateTable($quary_2, "hero_army", "id_hero = :idh AND id_player = :idp", ["idh" => $idHeroLeft,  "idp" => $idPlayer]);
          updateTable($quary_1, "hero_army", "id_hero = :idh AND id_player = :idp", ["idh" => $idHeroRight, "idp" => $idPlayer]);
          return [
                "state" => "ok",
                "HeroArmyLeft"  => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHeroLeft])[0],
                "HeroArmyRight" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHeroRight])[0]
          ];
    }

    function clearHeroArmy()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable("hero.id_city, hero.in_city, hero_army.*", "hero Join hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        $cityArmy = [0=>0, "army_a" =>0, "army_b" => 0, "army_c" => 0, "army_d" => 0, "army_e" => 0, "army_f" => 0];
        
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["f_1_type"]]] += $Hero[0]["f_1_num"];
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["f_2_type"]]] += $Hero[0]["f_2_num"];
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["f_3_type"]]] += $Hero[0]["f_3_num"];
        
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["b_1_type"]]] += $Hero[0]["b_1_num"];
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["b_2_type"]]] += $Hero[0]["b_2_num"];
        $cityArmy[CArmy::$ArmyCityPlace[$Hero[0]["b_3_type"]]] += $Hero[0]["b_3_num"];
        
        updateTable(
                "army_a = army_a + :aa, army_b = army_b + :ab, "
                . "army_c = army_c + :ac, army_d = army_d + :ad, "
                . "army_e = army_e + :ae, army_f = army_f + :af", "city", "id_city = :idc" ,  [
                    "aa" => $cityArmy["army_a"], "ab" => $cityArmy["army_b"], "ac" => $cityArmy["army_c"], 
                    "ad" => $cityArmy["army_d"], "ae" => $cityArmy["army_e"], "af" => $cityArmy["army_f"],
                    "idc" => $Hero[0]["id_city"]
                ]);
        
        updateTable(
                "f_1_type = 0, f_1_num = 0, f_2_type = 0, f_2_num = 0, "
                . "f_3_type = 0, f_3_num = 0, b_1_type = 0, b_1_num = 0, "
                . "b_2_type = 0, b_2_num = 0, b_3_type = 0, b_3_num = 0",
                "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        LSaveState::saveCityState($Hero[0]["id_city"]);
        return [
            "state" => "ok",
            "HeroArmy" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero])[0],
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    }
    
    function refreshHeroArmy()
    {
        
        global $idPlayer;
        $idHero = validateID($_GET["idHero"]);
        return [
            "state" => "ok",
            "HeroArmy" => selectFromTable("*", "hero_army", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer])[0]
        ];
    }
    
}

