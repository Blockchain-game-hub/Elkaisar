<?php

class LHero {

    static function addNew($idCity, $lvl, $avatar, $name, $ultraPoint = 0) {
        
        global $idPlayer;
        $points = static::pointsForLvl($lvl);
        $lastOrder = selectFromTable("ord", "hero", "id_city = :idc ORDER BY ord DESC LIMIT 1", ["idc" => $idCity]);
        if ($lvl >= 100) {
            $power = 150;
        } else {
            $power = 50 + $lvl;
        }
        $ord = count($lastOrder) > 0 ? $lastOrder[0]["ord"] + 1 : 0;
        
        $idHero = static::getNewHeroId();
        if (!$idHero) return false;
        
        insertIntoTable("id_hero =:idh, name = :n , lvl = :l ,  avatar = :a , power = :p , id_city = :idc , id_player = :idp ,"
                . "point_a = :pa , point_b = :pb , point_c = :pc, cap = :c , ultra_p = :up , ord = :o , p_b_a = :pba, p_b_b = :pbb,"
                . " p_b_c = :pbc, b_lvl = :bl, power_max = :pm", "hero",
                [ "idh" => $idHero,
                    "n" => $name, "l" => $lvl, "a" => $avatar, "p" => $power, "idc" => $idCity, "idp" => $idPlayer, 
                    "pa" => $points["pointA"],  "pb" => $points["pointB"], "pc" => $points["pointC"], "c" => $points["cap"], "up" => $ultraPoint, "o" => $ord,
                    "pba" => $points["pba"], "pbb" => $points["pbb"], "pbc" => $points["pbc"], "bl" => $lvl, "pm" => 50 + $lvl
        ]);

        

        
        insertIntoTable("id_hero = :idh, id_player = :idp", "hero_army", ["idh" => $idHero, "idp" => $idPlayer]);
        insertIntoTable("id_hero = :idh", "hero_medal", ["idh" => $idHero]);
        updateTable("hero_num  = hero_num  + 1", "server_data", "id = 1");

        return true;
    }
    
    static function getNewHeroId()
    {
        
        global $idPlayer;
        $heroCount = selectFromTable("COUNT(*) AS c", "hero", "id_player = :idp", ["idp" => $idPlayer])[0]["c"];
        
        $idHero = ($idPlayer - 1 ) *1000 + $heroCount + 1;
        
        if($heroCount == 0)
            return $idHero;
        
        $LastHeroId = selectFromTable("id_hero", "hero", "id_player = :idp ORDER BY id_hero DESC LIMIT 1", ["idp" => $idPlayer])[0]["id_hero"];

        if($LastHeroId >= $idHero)
        {
            $idHeroGap = queryExe("SELECT 
                                    id_hero + 1 AS idHero
                                    FROM hero mo WHERE
                                            NOT EXISTS ( SELECT NULL FROM hero mi WHERE mi.id_hero = mo.id_hero + 1 ) 
                                        AND id_player = :idp
                                        ORDER BY id_hero  LIMIT 1", ["idp" => $idPlayer])["Rows"][0]["idHero"];
           
            if($idHeroGap > ($idPlayer - 1 ) *1000  && $idHeroGap < ($idPlayer) *1000 )
                return $idHeroGap;
        }
        
        
        return max($idHero, $LastHeroId + 1);
    
    }

    static function pointsForLvl($lvl) {
        $points = [];
        $points["pba"] = rand(0, 25);
        $points["pbb"] = rand(0, 25);
        $pointsSum = rand(50, 60);
        $points["pbc"]    = $pointsSum - ($points["pba"] + $points["pbb"]);
        $points["pointA"] = $points["pba"] + ($lvl * 1);
        $points["pointB"] = $points["pbb"] + ($lvl * 1);
        $points["pointC"] = $points["pbc"] + ($lvl * 1);
        $points["cap"] = 30000 + $points["pointA"] * 500;
        return $points;
    }

    static function reqExp($lvl) {
        
        global $idPlayer;
        $studyLvl = selectFromTable("scholership", "player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        if (!count($studyLvl)) return INF;

        $base = pow($lvl, 2);
        $studyEffect = 0;
        if ($studyLvl[0]["scholership"] >= 20)
                $studyEffect = 0.3 + 0.2 + ($studyLvl[0]["scholership"] - 20) * 0.015;
        else if ($studyLvl[0]["scholership"] >= 10)
                $studyEffect = 0.3 + ($studyLvl[0]["scholership"] - 10) * 0.02;
        else $studyEffect = $studyLvl[0]["scholership"] * 0.03;


        return $base * ( 1 - $studyEffect)*125;
    }

    static function reOrderHero($idCity) {
        $idHeros = selectFromTable("id_hero", "hero", "id_city = :idc ORDER BY ord ASC", ["idc" => $idCity]);
        $ord = 0;
        foreach ($idHeros as $oneId) {
            $ord ++;
            updateTable("ord = :ord", "hero", "id_hero = :idh", ["ord" => $ord, "idh" => $oneId["id_hero"]]);
        }
    }
    
    static function filledPlacesSize($idHero)
    {
        $HeroArmy = selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        if (!count($HeroArmy)) return 0;
        $filledPlaces = 0;
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["f_1_type"]] * $HeroArmy[0]["f_1_num"];
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["f_2_type"]] * $HeroArmy[0]["f_2_num"];
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["f_3_type"]] * $HeroArmy[0]["f_3_num"];
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["b_1_type"]] * $HeroArmy[0]["b_1_num"];
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["b_2_type"]] * $HeroArmy[0]["b_2_num"];
        $filledPlaces += CArmy::$ArmyCap[$HeroArmy[0]["b_3_type"]] * $HeroArmy[0]["b_3_num"];
        
        return $filledPlaces;
    }

    static function heroFullCap($idHero)
    {
        global $idPlayer;

        $Hero = selectFromTable("hero.point_a, hero.point_a_plus, hero_medal.*, hero.id_city", "hero JOIN hero_medal ON hero.id_hero = hero_medal.id_hero", "hero.id_hero = :idh", ["idh" => $idHero]);
        $PlayerEdu = selectFromTable("leader", "player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        $AcadmyLvl = LCityBuilding::buildingWithHeighestLvl($Hero[0]["id_city"], CITY_BUILDING_ACADEMY)["Lvl"];

        if (!count($Hero)) return 0;
        if (!count($PlayerEdu)) $PlayerEdu = [["leader" => 0]];

        $now = time();
        $baseCap = HERO_SWAY_POINT_EFF * ($Hero[0]["point_a"] + $Hero[0]["point_a_plus"]);
        $afterEduEff = min($AcadmyLvl, $PlayerEdu[0]["leader"]) * $baseCap * HERO_EDU_LVL_EFF_CAP;
        $afterCiceroEff = $Hero[0]["medal_ceasro"] > $now ? $baseCap * HERO_MEDAL_EFF_CAP : 0;
        $afterCeaserMarqueEff = $Hero[0]["ceaser_eagle"] > $now ? $baseCap * HERO_EAGLE_EFF_CAP : 0;
        return
                $baseCap + $afterEduEff + $afterCiceroEff + $afterCeaserMarqueEff + HERO_BASE_CAP;
    }

    static function emptyPlacesSize($idHero) {
        
        return static::heroFullCap($idHero) - static::filledPlacesSize($idHero);
    }

    static function HeroArmyBattel($idHero, $Battel) {
        
        if ($idHero <= 0) return CHero::$EmptyBattelHero;
        
        if($Battel["task"] != BATTEL_TASK_CHALLANGE)
            $army = selectFromTable("*", "hero_army", "id_hero = :idh", ["idh" => $idHero]);
        else {
           $army = selectFromTable("*", "arena_player_challange_hero", "id_hero = :idh", ["idh" => $idHero]); 
        }
       

        if (!count($army)) return CHero::$EmptyBattelHero;


        return [
            "pre" => [
                "f_1" => $army[0]["f_1_num"],
                "f_2" => $army[0]["f_2_num"],
                "f_3" => $army[0]["f_3_num"],
                "b_1" => $army[0]["b_1_num"],
                "b_2" => $army[0]["b_2_num"],
                "b_3" => $army[0]["b_3_num"]
            ],
            "type" => [
                "f_1" => $army[0]["f_1_type"],
                "f_2" => $army[0]["f_2_type"],
                "f_3" => $army[0]["f_3_type"],
                "b_1" => $army[0]["b_1_type"],
                "b_2" => $army[0]["b_2_type"],
                "b_3" => $army[0]["b_3_type"]
            ]
        ];
    }

    static function prepareForBattel($Hero, $Battel) {

        $now = time();
        $leoMedalEff = 0;
        $denMedalEff = 0;
        $pointAttack = 0;
        $pointDeffence = 0;
        $HeroArmy = static::HeroArmyBattel($Hero["id_hero"], $Battel);

        if (isset($Hero["medal_leo"]))
                $leoMedalEff = $Hero["medal_leo"] > $now ? 0.25 : 0;
        if (isset($Hero["medal_den"]))
                $denMedalEff = $Hero["medal_den"] > $now ? 0.25 : 0;
        if (isset($Hero["point_b"]))
                $pointAttack = $Hero["point_b"] + $Hero["point_b"] * $denMedalEff + $Hero["point_b_plus"];
        if (isset($Hero["point_c"]))
                $pointDeffence = $Hero["point_c"] + $Hero["point_c"] * $denMedalEff + $Hero["point_c_plus"];
        
        if (isset($Hero["type"])) $HeroArmy["type"] = $Hero["type"];
        if (isset($Hero["pre"])) $HeroArmy["pre"] = $Hero["pre"];


        return [
            "id_hero"   => $Hero["id_hero"],
            "id_player" => $Hero["id_player"],
            "id_city"   => $Hero["id_city"],
            "x_coord"   => $Hero["x"],
            "y_coord"   => $Hero["y"],
            "side"      => $Hero["side"],
            "ord"       => $Hero["ord"],
            "type"      => $HeroArmy["type"],
            "pre"       => $HeroArmy["pre"],
            "post" => [
                "f_1" => 0,
                "f_2" => 0,
                "f_3" => 0,
                "b_1" => 0,
                "b_2" => 0,
                "b_3" => 0
            ],
            "real_eff" => [
                1 => CHero::$EmptyBattelHeroEff, 2 => CHero::$EmptyBattelHeroEff,
                3 => CHero::$EmptyBattelHeroEff, 4 => CHero::$EmptyBattelHeroEff,
                5 => CHero::$EmptyBattelHeroEff, 6 => CHero::$EmptyBattelHeroEff
            ],
            "is_garrsion"       => $Hero["is_garrsion"],
            "point_atk"         => $pointAttack,
            "point_def"         => $pointDeffence,
            "honor"             => 0,
            "points"            => 0,
            "resource_capacity" => 0,
            "gainXp"            => 0,
            "standTillRound"    => 0,
            "troopsKilled"      => 0,
            "troopsKills"       => 0,
            
        ];
    }

    public static function studyEffectOnForces($type, $Study) {
        if ($type == 1 || $type == 3 || $type == 4) {
            return 0.03 * $Study["infantry"];
        }
        if ($type == 2) {
            return 0.03 * $Study["riding"];
        }
        if ($type == 5 || $type == 6) {
            return 0.03 * $Study["army"];
        }

        return 0;
    }

    public static function prepareHeroPowerBattel(&$Heros, $Unit) {

        $players_array = [];
        foreach ($Heros as &$oneHero) {

            $id_player = $oneHero["id_player"];

            $atk_percent = 0;
            $def_percent = 0;
            $acad_study = ["infantry" => 0, "riding" => 0, "army" => 0, "infantry" => 0, "medicine" => 0, "safe" => 0];

            if (array_key_exists($id_player, $players_array)) {

                $atk_percent   = $players_array[$id_player]["atk_percent"];
                $def_percent   = $players_array[$id_player]["def_percent"];
                $acad_study    = $players_array[$id_player]["study"];
                $godGateEffect = $players_array[$id_player]["godGate"];
            } else {

                if ($id_player > 0) {

                    $now = time();
                    $player_state = selectFromTable("attack_10 , defence_10", "player_stat", "id_player = {$id_player}")[0];
                    $acad_study = selectFromTable("infantry , riding, army, safe, medicine", "player_edu", "id_player = {$id_player}")[0];
                    $atk_percent = $player_state['attack_10'] > $now ? 0.1 : 0;
                    $def_percent = $player_state['defence_10'] > $now ? 0.1 : 0;
                    $godGateEffect = LGodGate::getPlayerGateEffect($id_player, $Unit);


                    $players_array[$id_player]["atk_percent"] = $atk_percent;
                    $players_array[$id_player]["def_percent"] = $def_percent;
                    $players_array[$id_player]["study"]       = $acad_study;
                    $players_array[$id_player]["godGate"]     = $godGateEffect;
                } else {

                    $godGateEffect = LGodGate::getPlayerGateEffect($id_player, $Unit);

                    $players_array[$id_player]["atk_percent"] = 0;
                    $players_array[$id_player]["def_percent"] = 0;
                    $players_array[$id_player]["study"] = ["infantry" => 0, "riding" => 0, "army" => 0, "safe" => 0, "medicine" => 0];
                    $players_array[$id_player]["godGate"] = $godGateEffect;
                }
            }


            foreach ($oneHero["real_eff"] as &$one):  // increment attack and defance points  for hero

                $one["attack"] += $oneHero["point_atk"];
                $one["attack"] += $one["attack"] * LHero::studyEffectOnForces($one["armyType"], $acad_study);
                $one["attack"] += $atk_percent * $one["attack"];
                $one["attack"] += $godGateEffect["attack"];
                $one["def"] += $oneHero["point_def"];
                $one["def"] += $one["def"] * $acad_study["safe"] * 0.03;
                $one["def"] += $def_percent * $one["def"];
                $one["def"] += $godGateEffect["defence"];
                $one["vit"] += $one["vit"] * $acad_study["medicine"] * 0.05;
                $one["vit"] += $godGateEffect["vit"];
                $one["dam"] += $godGateEffect["damage"];

            endforeach;
        }
    }

}
