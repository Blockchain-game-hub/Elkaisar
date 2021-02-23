<?php

class LHeroArmy {

    static function isTheSame($idHero, $arymType) {
        $heroArmy = selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        if (!count($heroArmy)) return false;
        if ($heroArmy[0]["f_1_type"] != 0 && $heroArmy[0]["f_1_type"] != $arymType)     return FALSE;
        elseif ($heroArmy[0]["f_2_type"] != 0 && $heroArmy[0]["f_2_type"] != $arymType) return FALSE;
        elseif ($heroArmy[0]["f_3_type"] != 0 && $heroArmy[0]["f_3_type"] != $arymType) return FALSE;
        elseif ($heroArmy[0]["b_1_type"] != 0 && $heroArmy[0]["b_1_type"] != $arymType) return FALSE;
        elseif ($heroArmy[0]["b_2_type"] != 0 && $heroArmy[0]["b_2_type"] != $arymType) return FALSE;
        elseif ($heroArmy[0]["b_3_type"] != 0 && $heroArmy[0]["b_3_type"] != $arymType) return FALSE;
        return TRUE;
    }

    static function getSlowestSpeed($idHero)
    {
        $HeroArmy = selectFromTable("f_1_type, f_2_type, f_3_type, b_1_type, b_2_type, b_3_type", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        
        if(!count($HeroArmy))
            return CArmy::$AmySpeed[0];
        
        $Slowest = CArmy::$AmySpeed[2];
        
        foreach ($HeroArmy[0] as $cell){
            if($cell == 0)
                continue;
            
            if($Slowest > CArmy::$AmySpeed[$cell])
                $Slowest = CArmy::$AmySpeed[$cell];
        }
            
        return $Slowest;
        
    }
    
    public static function deathFactor($unitType)
    {
        if( LWorldUnit::isBarrary($unitType) || LWorldUnit::isArenaDeath($unitType))
            return  1;
        if(LWorldUnit::isArmyCapital($unitType))
            return 0;
        if(LWorldUnit::isAsianSquads($unitType) || LWorldUnit::isCarthagianArmies($unitType))
            return 0.2;
        if(LWorldUnit::isCamp($unitType) || LWorldUnit::isStatueWar($unitType))
            return 0.4;
        if(LWorldUnit::isMonawrat($unitType) 
                || LWorldUnit::isGangStar($unitType)
                || LWorldUnit::isArenaChallange($unitType)
                || LWorldUnit::isArenaGuild($unitType)
                )
            return 0;
        else
            return 1;
        
    }

    public static function killHeroArmy($Hero , LFight &$Fight){
        
        if($Fight->Battel["task"] == BATTEL_TASK_CHALLANGE)
            return;
        
        $factor = static::deathFactor($Fight->Unit["ut"]);
        if($factor == 0 || $Hero["id_player"] <= 0)
            return ;
        $medical_status_effect = selectFromTable("medical", "player_stat", "id_player = {$Hero["id_player"]}")[0]["medical"];
        $loseRatio = $medical_status_effect > time() ? 0.6 : 0.1;
        
        if($factor <= 0)
            return ;
        
        $RemainAmount = [];
        $RemainType   = [];
        $cityWound    = [0,0,0,0,0,0,0];
        
        foreach ($Hero["pre"] as $Place => $amount)
        {
            $RemainAmount[$Place] = max(ceil($amount - $Hero["post"][$Place]*$factor) , 0);
            $RemainType[$Place]  = $RemainAmount[$Place] > 0 ? $Hero["type"][$Place] : 0;
            $cityWound[$Hero["type"][$Place]] += $Hero["post"][$Place]*$factor*$loseRatio;
        }
        
        $quary = "f_1_num  = {$RemainAmount["f_1"]}, f_1_type = {$RemainType["f_1"]}, "
                . "f_2_num = {$RemainAmount["f_2"]}, f_2_type = {$RemainType["f_2"]}, "
                . "f_3_num = {$RemainAmount["f_3"]}, f_3_type = {$RemainType["f_3"]}, "
                . "b_1_num = {$RemainAmount["b_1"]}, b_1_type = {$RemainType["b_1"]}, "
                . "b_2_num = {$RemainAmount["b_2"]}, b_2_type = {$RemainType["b_2"]}, "
                . "b_3_num = {$RemainAmount["b_3"]}, b_3_type = {$RemainType["b_3"]} ";
        updateTable($quary, "hero_army", "id_hero = :idh" , ["idh" => $Hero["id_hero"]]);
        
        
        
        $quary = " army_a = army_a + {$cityWound[1]} ,army_b = army_b + {$cityWound[2]} ,"
        . "army_c = army_c + {$cityWound[3]} ,army_d = army_d + {$cityWound[4]} ,"
        . "army_e = army_e + {$cityWound[5]} ,army_f = army_f + {$cityWound[6]} ";
        
        updateTable($quary, "city_wounded", "id_city = :idc", ["idc" => $Hero["id_city"]]);
    }
    
    static function isCarringArmy($idHero)
    {
        global $idPlayer;
        $Army = selectFromTable("*", "hero_army", "id_hero = :idh AND id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        if(!count($Army))
            return false;
        
        if(
               $Army[0]["f_1_num"] > 0 || $Army[0]["f_2_num"] > 0 || $Army[0]["f_3_num"] > 0 
            || $Army[0]["b_1_num"] > 0 || $Army[0]["b_2_num"] > 0 || $Army[0]["b_3_num"] > 0  
            )
        return $Army[0];
        
        return false;
        
    }
}
