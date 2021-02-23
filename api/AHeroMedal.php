<?php

class AHeroMedal
{
    
    function activateCiceroMedal()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable(
                "hero_medal.*, hero.id_hero, hero.id_city, hero.in_city",
                "hero JOIN hero_medal ON hero_medal.id_hero = hero.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp",
                ["idh" => $idHero, "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("medal_ceasro"))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2"];
        $timeEnd = max(time() + 24*7*60*60, $Hero[0]["medal_ceasro"] + 60*60*24*7);
        updateTable("medal_ceasro = :nt", "hero_medal", "id_hero = :idh", ["idh" => $idHero, "nt" => $timeEnd]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroMedal" => selectFromTable("*", "hero_medal", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    
    }
    
    function activateDentatusMedal()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable(
                "hero_medal.*, hero.id_hero, hero.id_city, hero.in_city",
                "hero JOIN hero_medal ON hero_medal.id_hero = hero.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp",
                ["idh" => $idHero, "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("medal_den"))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2"];
        
        $timeEnd = max(time() + 24*7*60*60, $Hero[0]["medal_den"] + 60*60*24*7);
        updateTable("medal_den = :nt", "hero_medal", "id_hero = :idh", ["idh" => $idHero, "nt" => $timeEnd]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroMedal" => selectFromTable("*", "hero_medal", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    
    
    }
    
    
    function activateLeonidasMedal()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable(
                "hero_medal.*, hero.id_hero, hero.id_city, hero.in_city",
                "hero JOIN hero_medal ON hero_medal.id_hero = hero.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp",
                ["idh" => $idHero, "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("medal_leo"))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2"];
        
        $timeEnd = max(time() + 24*7*60*60, $Hero[0]["medal_leo"] + 60*60*24*7);
        updateTable("medal_leo = :nt", "hero_medal", "id_hero = :idh", ["idh" => $idHero, "nt" => $timeEnd]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroMedal" => selectFromTable("*", "hero_medal", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    
    
    }
    
    
    function activateCaeserMedal()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable(
                "hero_medal.*, hero.id_hero, hero.id_city, hero.in_city",
                "hero JOIN hero_medal ON hero_medal.id_hero = hero.id_hero", "hero.id_hero = :idh AND hero.id_player = :idp",
                ["idh" => $idHero, "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("ceaser_eagle"))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2"];
        
        $timeEnd = max(time() + 24*7*60*60, $Hero[0]["ceaser_eagle"] + 60*60*24*7);
        updateTable("ceaser_eagle = :nt", "hero_medal", "id_hero = :idh", ["idh" => $idHero, "nt" => $timeEnd]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroMedal" => selectFromTable("*", "hero_medal", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    
    
    }
    
}
