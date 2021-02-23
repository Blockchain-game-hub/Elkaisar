<?php

class AHero
{
    
    function changeHeroName()
    {
        global $idPlayer;
        $idHero  = validateID($_POST["idHero"]);
        $newName = validatePlayerWord($_POST["newName"]);
        $Hero = selectFromTable("id_hero", "hero", "id_hero = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]); 
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(mb_strlen($newName) >= 15)
            return ["state" => "error_1"];
        
        updateTable("name = :n", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer, "n" => $newName]);
        
        return [
            "state" => "ok",
            "Hero"  => selectFromTable("*", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer])[0]
        ];
    }
    
    function upgradeHeroLvl()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable("id_hero, lvl, exp, b_lvl, ultra_p, in_city, id_city", "hero", "id_hero = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]); 
        $reqExp = LHero::reqExp($Hero[0]["lvl"]);
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Hero[0]["lvl"] >= 255)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["exp"] < $reqExp)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        $Points = 3;
        if(($Hero[0]["lvl"] + 1)%10 == 0)
            $Points += $Hero[0]["ultra_p"];
        
        updateTable(
                 "lvl = lvl + 1 , points = points + :p ,  exp = exp - :re, power_max = :mp",
                 "hero",
                 "id_hero = :idh AND id_player = :idp  AND in_city = 1",
                 ["p"=>$Points, "re"=>$reqExp, "idh"=>$idHero, "idp"=>$idPlayer, "mp" => min(150, $Hero[0]["lvl"] + 51)]);
         LSaveState::saveCityState($Hero[0]["id_city"]);
         LSaveState::coinOutState($Hero[0]["id_city"]);
            
        return [
            "state" => "ok",
            "Hero"  => selectFromTable("*", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer])[0],
            "City"  => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
        
    }
    
    function fireHero()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero   = selectFromTable("*", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $heroEquip = selectFromTable("COUNT(*) AS c", "equip", "id_hero = :idh", ["idh" => $idHero])[0]["c"];
        
        if(!count($Hero))
            return ["state" == "error_0", "TryToHack" => TryToHack()];
        if($heroEquip > 0)
            return ["state" == "error_1", "TryToHack" => TryToHack()];
        
        
        deleteTable("hero_army", "id_hero  = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        deleteTable("hero_medal", "id_hero = :idh", ["idh" => $idHero]);
        deleteTable("hero_equip", "id_hero = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        deleteTable("world_unit_garrison", "id_hero = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        deleteTable("hero", "id_hero = :idh AND id_player = :idp",  ["idp" => $idPlayer, "idh" => $idHero]);
        
        LHero::reOrderHero($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "CityHero" => selectFromTable("*", "hero", "id_city = :idc ORDER BY ord ASC", ["idc" => $Hero[0]["id_city"]])
        ];
    }
    
    function addExp(){
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $itemToUse = validateGameNames($_POST["itemToUse"]);
        $Hero = selectFromTable("id_hero, exp, lvl", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Hero[0]["lvl"] >= 255)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!LItem::useItem($itemToUse))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
            
        
        $exp = LHero::reqExp($Hero[0]["lvl"]);

        if($itemToUse == "exp_hero_8")
            $amount = $exp*8/100 > 1000 ? $exp*8/100  : 1000;
        elseif($itemToUse == "exp_hero_30")
            $amount = $exp*0.3 > 100000 ? $exp*0.3    : 100000;
        elseif($itemToUse == "exp_hero_100")
            $amount = $exp > 1000000 ? $exp    : 1000000;
        else
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("exp = exp + :a", "hero", " id_player = :idp  AND id_hero = :idh ",["a"=>$amount, "idp"=>$idPlayer, "idh"=>$idHero]);
        
        return [
            "state" => "ok",
            "Exp" => $amount,
            "Hero" => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $Hero[0]["id_hero"]])[0]
        ];
        
    }
    
    function addPower(){
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        
        $Hero = selectFromTable("id_hero, power, lvl", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $itemToUse = validateGameNames($_POST["itemToUse"]);
        $maxPower = $Hero[0]["lvl"] >= 100 ? 150 : $Hero[0]["lvl"] + 50;
        $currentPower = $Hero[0]["power"];
                
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if( $currentPower >= $maxPower)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!LItem::useItem($itemToUse))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
            
        
       
        $amount = 0;
        if($itemToUse == "bread")
            $amount = ($maxPower*10/100) ;
        elseif($itemToUse == "fruit")
            $amount = $maxPower*30/100;
        elseif($itemToUse == "milk")
            $amount = $maxPower*60/100;
        elseif($itemToUse == "meat")
            $amount = $maxPower;
        else
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("power = :p", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer, "p" => min(($currentPower  + $amount), $maxPower)]);
        
        
        return [
            "state" => "ok",
            "power"=> $amount,
            "Hero" => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $Hero[0]["id_hero"]])[0]
        ];
        
    }
    
    function addLoy()
    {
        
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable("id_hero, loyal, lvl", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $itemToUse = validateGameNames($_POST["itemToUse"]);
       
                
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if( $Hero[0]["loyal"] >= 100)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!LItem::useItem($itemToUse))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        if($itemToUse == "luxury_1")     $amount = 5;
        elseif($itemToUse == "luxury_2") $amount = 10;
        elseif($itemToUse == "luxury_3") $amount = 15;
        elseif($itemToUse == "luxury_4") $amount = 20;
        elseif($itemToUse == "luxury_5") $amount = 25;
        elseif($itemToUse == "luxury_6") $amount = 30;
        elseif($itemToUse == "luxury_7") $amount = 35;
        elseif($itemToUse == "luxury_8") $amount = 40;
        elseif($itemToUse == "luxury_9") $amount = 45;
        else                             
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("loyal = :l", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer, "l" => min(($Hero[0]["loyal"]  + $amount), 100)]);
        
        return [
            "state" => "ok",
            "loy" => $amount,
            "Hero" => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $Hero[0]["id_hero"]])[0]
        ];
    }
    
    function resetHeroPoints()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable("id_hero, lvl, b_lvl, ultra_p, id_city", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
       
                
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!LItem::useItem("retreat_point", floor(($Hero[0]["lvl"]/10) + 1)))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        $points = $Hero[0]["lvl"]*3 + floor(($Hero[0]["lvl"] - $Hero[0]["b_lvl"])/10)*$Hero[0]["ultra_p"];
        
        updateTable("points = :po , point_a = p_b_a , point_b = p_b_b  , point_c = p_b_c", "hero", "id_player = :idp AND id_hero = :idh  AND in_city = 1", ["po"=>$points,"idh"=>$idHero, "idp"=>$idPlayer]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "Hero"  => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    }
    
    
    function setHeroPoints()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $pointA = validateID($_POST["pointA"]);
        $pointB = validateID($_POST["pointB"]);
        $pointC = validateID($_POST["pointC"]);
        
        $Hero = selectFromTable("*", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $HeroMaxPoint = ($Hero[0]["lvl"]*3 + floor($Hero[0]["lvl"]/10)*$Hero[0]["ultra_p"] + 60);
        
        if($Hero[0]["point_a"] + $Hero[0]["point_b"] + $Hero[0]["point_c"] + $pointA + $pointB + $pointC > $HeroMaxPoint)
            return ["state" => "error_5", "TryToHack" => TryToHack()];
                
        if(!count($Hero)) 
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($pointA < 0 || $pointB < 0 || $pointC < 0)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["points"] < $pointA + $pointB + $pointC) 
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_3"];
        if($Hero[0]["point_a"] + $pointA > $Hero[0]["point_b"] + $pointB + $Hero[0]["point_c"] + $pointC)
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        if($Hero[0]["point_b"] + $pointB > $Hero[0]["point_a"] + $pointA + $Hero[0]["point_c"] + $pointC)
            return ["state" => "error_5", "TryToHack" => TryToHack()];
        if($Hero[0]["point_c"] + $pointC > $Hero[0]["point_b"] + $pointB + $Hero[0]["point_a"] + $pointA)
            return ["state" => "error_6", "TryToHack" => TryToHack()];
        
        updateTable("point_a = point_a + :pa, point_b = point_b + :pb, point_c = point_c + :pc, points = points - ($pointA + $pointB + $pointC)", "hero", "id_hero = :idh", ["pa" => $pointA, "pb" => $pointB, "pc" => $pointC, "idh" => $idHero]); 
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "Hero"  => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idHero])[0],
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
    }
    
    function addConsole()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $Hero = selectFromTable("id_hero, lvl, b_lvl, ultra_p, id_city, in_city", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
       
                
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_1"];
        
        updateTable("console = 0", "hero", "id_city = :idc", ["idc"=>$Hero[0]["id_city"]]);
        updateTable("console = 1", "hero", "id_hero = :idh", ["idh"=>$idHero]);
        updateTable("console = :idh", "city", "id_city = :idc", ["idh"=>$idHero, "idc"=>$Hero[0]["id_city"]]);
        
        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        
        return [
            "state" => "ok",
            "HeroList"  => selectFromTable("*", "hero", "id_city = :idc", ["idc" => $Hero[0]["id_city"]]),
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0]
        ];
        
    }
    
    function removeConsole()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
       
          
        
        updateTable("console = 0", "hero", "id_city = :idc", ["idc"=>$idCity]);
        updateTable("console = NULL", "city", "id_city = :idc", ["idc"=>$idCity]);
        
        LSaveState::saveCityState($idCity) ; 
        LSaveState::resInState($idCity, "food");
        LSaveState::resInState($idCity, "wood");
        LSaveState::resInState($idCity, "stone");
        LSaveState::resInState($idCity, "metal");
        LSaveState::coinInState($idCity);
        
        return [
            "state" => "ok",
            "HeroList"  => selectFromTable("*", "hero", "id_city = :idc", ["idc" => $idCity]),
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
        
    }
    
    function educate()
    {
        global $idPlayer;
        $idHero     = validateID($_POST["idHero"]);
        $pointFor  = validateGameNames($_POST["pointFor"]);
        $medalToUse = validateGameNames($_POST["medalToUse"]);
        $Hero       = selectFromTable("id_city, in_city", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);

        if(!in_array($pointFor, ["point_a_plus", "point_b_plus", "point_c_plus"]))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($Hero))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        
        if($medalToUse == "medal_bronz"){

            $rand = rand(-100, 100);
            $maxPoint = $rand > 0? ($rand > 50 ? 2 : 1) : -1; 


        }elseif($medalToUse == "medal_silver"){

            $rand = rand(-50, 100);
            $maxPoint = $rand > 0? ($rand > 50 ? 2 : 1) : -1; 

        }elseif( $medalToUse == "medal_gold"){

            $rand = rand(-30, 100);
            $maxPoint = $rand > 0? ($rand > 50 ? ($rand > 75 ? 3 : 2) : 1) : 0; 

        } else 
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        if(!LItem::useItem($medalToUse, 10))
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        

      
        updateTable("`$pointFor` = GREATEST(0 , LEAST(`$pointFor` + (:mx) , 10) )", "hero", "id_player = :idp AND  id_hero = :idh AND in_city = 1", ["mx" => $maxPoint, "idp" => $idPlayer, "idh" => $idHero]);

        LSaveState::saveCityState($Hero[0]["id_city"]) ; 
        LSaveState::resInState($Hero[0]["id_city"], "food");
        LSaveState::resInState($Hero[0]["id_city"], "wood");
        LSaveState::resInState($Hero[0]["id_city"], "stone");
        LSaveState::resInState($Hero[0]["id_city"], "metal");
        LSaveState::coinInState($Hero[0]["id_city"]);
        return [
            "state" => "ok",
            "Hero"   => selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $idHero])[0],
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Hero[0]["id_city"]])[0],
            "PointAdded" => $maxPoint
        ];

        
    }
    
    function searchHero()
    {
        
        $HeroName = validatePlayerWord($_GET["HeroName"]);
        
        return 
            selectFromTable("name AS HeroName, id_hero AS idHero, avatar", "hero", "name = :n", ["n" => "%$HeroName%"]);
        
    }
    
    
    function reOrderHero()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $MoveDir = validateID($_POST["MoveDir"]);
        $Hero = selectFromTable("ord, id_city, id_hero", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        
        if(!count($Hero))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $HeroBefore = selectFromTable("ord, id_hero", "hero", "id_city = :idc AND ord < :ord ORDER BY ord DESC LIMIT 1", ["idc" => $Hero[0]["id_city"], "ord" => $Hero[0]["ord"]]);
        $HeroAfter  = selectFromTable("ord, id_hero", "hero", "id_city = :idc AND ord > :ord ORDER BY ord ASC  LIMIT 1", ["idc" => $Hero[0]["id_city"], "ord" => $Hero[0]["ord"]]);
        
        if($MoveDir == "up")
        {
            if(!count($HeroBefore))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
            
            updateTable("ord = :o", "hero", "id_hero = :idh", ["idh" => $Hero[0]["id_hero"],       "o" => $HeroBefore[0]["ord"]]);
            updateTable("ord = :o", "hero", "id_hero = :idh", ["idh" => $HeroBefore[0]["id_hero"], "o" => $Hero[0]["ord"]]);
            
            
        }else {
            if(!count($HeroAfter))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
            
            updateTable("ord = :o", "hero", "id_hero = :idh", ["idh" => $Hero[0]["id_hero"], "o" => $HeroAfter[0]["ord"]]);
            updateTable("ord = :o", "hero", "id_hero = :idh", ["idh" => $HeroAfter[0]["id_hero"], "o" => $Hero[0]["ord"]]);
            
        }
            
        return [
            "state" => "ok",
            "HeroList" => selectFromTable("ord, id_hero", "hero", "id_city = :idc ORDER BY ord", ["idc" => $Hero[0]["id_city"]])
        ];
        
    }
}


/*
require_once '../config.php';
require_once '../base.php';
DbConnect(1);

$city = selectFromTable("id_city", "city", "id_player = 3603");

foreach ($city as $one)
{
    
    LHero::reOrderHero($one["id_city"]);
    
}*/