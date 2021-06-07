<?php

class APlayerState{
    
    function getPlayerFullData(){
        
        
        $idPlayer = validateID($_POST["idPlayer"]);
         
        return [
            "Heros"  => selectFromTable("*", "hero",  "id_player = :idp ORDER BY id_city ASC, ord DESC", ["idp" => $idPlayer]),
            "City"   => selectFromTable("*", "city",  "id_player = :idp", ["idp" => $idPlayer]),
            "Equip"  => selectFromTable("*", "equip", "id_player = :idp", ["idp" =>$idPlayer]),
            "Item"   => selectFromTable("*", "player_item", "id_player = :idp AND amount > 0", ["idp" => $idPlayer]),
            "Player" => selectFromTable("*", "player", "id_player = :idp", ["idp" => $idPlayer])[0]
        ];
        
        
    }
    
    
    function updataPlayerHero(){
      
        $idHero     = validateID($_POST["idHero"]);
        $point_a    = validateID($_POST["point_a"]);
        $point_b    = validateID($_POST["point_b"]);
        $point_c    = validateID($_POST["point_c"]);
        $p_b_a      = validateID($_POST["p_b_a"]);
        $p_b_b      = validateID($_POST["p_b_b"]);
        $p_b_c      = validateID($_POST["p_b_c"]);
        $lvl        = validateID($_POST["lvl"]);
        $b_lvl      = validateID($_POST["b_lvl"]);
        $power      = validateID($_POST["power"]);
        $power_max  = validateID($_POST["power_max"]);
        $loyal      = validateID($_POST["loyal"]);
        $ultra_p    = validateID($_POST["ultra_p"]);
        $points     = validateID($_POST["points"]);
        
        updateTable("point_a = $point_a, point_b = $point_b, point_c = $point_c,
                    p_b_a = $p_b_a, p_b_b = $p_b_b,  p_b_c = $p_b_c, 
                    lvl   = $lvl  ,  b_lvl =$b_lvl,   power =$power,   power_max =$power_max, loyal  = $loyal, ultra_p = $ultra_p, points = $points",
                    "hero", "id_hero = :idh", ["idh" => $idHero]);
    }
    
    function updataCityRes(){
        
        $idCity  = validateID($_POST["idCity"]);
        $Res     = validateGameNames($_POST["Res"]);
        $amount  = validateID($_POST["amount"]);
        
        updateTable("`$Res` = :a", "city", "id_city = :idc", ["idc" => $idCity, "a" => $amount]);
        
    }
    
    
    function updateCityArmy(){
        
        
        $idCity  = validateID($_POST["idCity"]);
        $Army     = validateGameNames($_POST["Army"]);
        $amount  = validateID($_POST["amount"]);
        updateTable("`$Army` = :a", "city", "id_city = :idc", ["idc" => $idCity, "a" => $amount]);
    }
    
    
    function updatePlayerItem(){
        $idPlayer  = validateID($_POST["idPlayer"]);
        $Item      = validateGameNames($_POST["Item"]);
        $amount  = validateID($_POST["amount"]);
        updateTable("amount = :a", "player_item", "id_player = :idp AND id_item = :idi", ["idp" => $idPlayer, "idi" => $Item, "a" => $amount]);
    }
    
    function deletePlayerEquip(){
        $idEquip = validateID($_POST["idEquip"]);
        deleteTable("equip", "id_equip = :ide", ["ide" => $idEquip]);
    }
    
    function getHeroDetails(){
        $idHero = validateID($_GET["idHero"]);
        return [
            "Hero" => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idHero])[0],
            "HeroArmy" => selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero])[0],
            "HeroEquip" => selectFromTable("*", "equip", "id_hero = :idh", ["idh" => $idHero])
        ];
    }
    
    function getEquipOffHero(){
        $idEquip = validateID($_POST["idEquip"]);
        updateTable("on_hero = 0, id_hero = NULL", "equip", "id_equip = :ide", ["ide" => $idEquip]);
    }
    
    function changeHeroArmy(){
        
        
        $idHero    = validateID($_POST["idHero"]);
        $ArmyPlace = validateGameNames($_POST["ArmyPlace"]);
        $ToDo      = validateGameNames($_POST["ToDo"]);
        
        $HeroArmy = selectFromTable("hero_army.*, hero.id_city", "hero_army JOIN hero ON hero.id_hero = hero_army.id_hero", "hero_army.id_hero = :idh", ["idh" => $idHero]);
        
       if(!count($HeroArmy))
           return;
       
       updateTable($ArmyPlace."_num = 0, ".$ArmyPlace."_type = 0", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
       $CityArmy = [
           "1" => "army_a",
           "2" => "army_b",
           "3" => "army_c",
           "4" => "army_d",
           "5" => "army_e",
           "6" => "army_f"
       ];
       
       if($ToDo == 1){
           updateTable($CityArmy[$HeroArmy[0][$ArmyPlace."_type"]]." = ".$CityArmy[$HeroArmy[0][$ArmyPlace."_type"]]." + ".$HeroArmy[0][$ArmyPlace."_num"], "city", "id_city = :idc", ["idc" => $HeroArmy[0]["id_city"]]); 
       }
        
    }
    
    
    function changeHeroAtt(){
        
        $idHero    = validateID($_POST["idHero"]);
        $Att       = validateGameNames($_POST["Att"]);
        $Val      = validateGameNames($_POST["val"]);
        
        
        updateTable("$Att = $Val", "hero", "id_hero = :idh", ["idh" => $idHero]);      
    }
}

