<?php

class ASpyFinish {

    function Finish() {
        $Spies = selectFromTable("*", "spy", "time_arrive <= :n", ["n" => time() + 1]);
        deleteTable("spy", "time_arrive <= :n", ["n" => time() + 1]);
        $Players = [];
        
        
        foreach ($Spies as $oneSpy) {
            
            updateTable("spies = spies  +  {$oneSpy['spy_num']}", "city", "id_city = {$oneSpy['id_city']}");

            if ($oneSpy["spy_on"] == "city") 
                $Players = array_merge ($Players, $this->spyOnCity($oneSpy));
            else 
                $Players = array_merge ($Players, $this->spyOnBarray($oneSpy));

        }
        
        return [
            "state" => "ok",
            "Players" => $Players
        ];
    }
    
    
    private function spyOnCity($Spy)
    {
        
        $vectimSpy    = selectFromTable("spies , id_player , id_city, name", 'city', "x = :xc   AND y = :yc", ["xc" => $Spy["x_to"], "yc" => $Spy["y_to"]]);
        $study_victim = selectFromTable("riding, spying", "edu_acad", "id_player = {$vectimSpy[0]["id_player"]}")[0];
        $studyPlayer  = selectFromTable("riding, spying", "edu_acad", "id_player = {$Spy["id_player"]}")[0];

        $victim_point = $vectimSpy[0]["spies"] + $vectimSpy[0]["spies"]*$study_victim["riding"];
        $player_point = $Spy["spy_num"] + $Spy["spy_num"]*$studyPlayer["riding"];

        if($victim_point < $player_point){
            updateTable("spies = GREATEST(spies - :s, 0)", "city", "id_city = :idc", ["idc" => $Spy["id_city"], "s" => max($vectimSpy[0]["spies"], 0)]);
            updateTable("spies = 0 ", "city", "x = :x   AND y = :y", ["x" => $Spy["x_to"], "y" => $Spy["y_to"]]);
            $this->cityVictimLose($Spy, $vectimSpy[0], $studyPlayer);
            

        }else{
            updateTable("spies = 0", "city", "id_city = :idc", ["idc" => $Spy["id_city"]]);
            updateTable("spies = GREATEST({$vectimSpy[0]["spies"]} - {$Spy["spy_num"]}, 0) ", "city", "x = :x   AND y = :y", ["x" => $Spy["x_to"], "y" => $Spy["y_to"]]);
            $this->cityVictimWin($Spy, $vectimSpy[0]);
        }
        
        return [
            $vectimSpy[0]["id_player"],
            $Spy["id_player"]
        ];
    }
    
    
    private function cityVictimLose($Spy, $Victim, $Player)
    {
        
        $xCoord = $Spy["x_to"];
        $yCoord = $Spy["y_to"];
        $now = time();
        $idReport = insertIntoTable("id_player = :idp , x_coord = :x, y_coord = :y, spy_city = :c , time_stamp = :t , state = 1", "spy_report", ["idp" => $Spy["id_player"], "x" => $xCoord, "y" => $yCoord, "c" => $Spy["id_city"], "t" => $now]);

        $idRV = insertIntoTable("id_player = :idp , x_coord = $yCoord,  y_coord = $yCoord, spy_city = {$Spy["id_city"]} , time_stamp = {$now} , state = 1", "spy_report", ["idp" => $Victim["id_player"]]);

        $content = "قام العديد من اللاعبين بحملات تجسس على  المدينة {$Victim["name"]} [$xCoord , $yCoord]  و خسرت {$Victim["spies"]} جاسوس خلال هذة العملية";

        
        insertIntoTable("id_report = $idRV , id_player = {$Victim["id_player"]} ,  x_coord = $xCoord, y_coord = $yCoord, city_name = '{$Victim["name"]}' , content = '{$content}' , time_stamp = {$now}", "spy_victim");   

        LSaveState::saveCityState($Victim["id_city"]);

        $victimCity = selectFromTable("food , wood , stone , metal, coin , army_a, army_b, army_c, army_d, army_e, army_f, lvl", "city", "id_city = {$Victim["id_city"]}")[0];
        $victimBuilding = selectFromTable("*", "city_building", "id_city = {$Victim["id_city"]}")[0];
        $victimBuildingLvl = selectFromTable("*", "city_building_lvl", "id_city = {$Victim["id_city"]}")[0];
        $studyEffect = max( 25 - $Player["riding"]  , 0);
        
        unset($victimBuilding["id_city"]); unset($victimBuilding["id_player"]); 
        unset($victimBuildingLvl["id_city"]); unset($victimBuildingLvl["id_player"]); 
        
        $Places = [];
        foreach ($victimBuilding as $key => $val){
            $victimBuilding[$key]  = floor($victimBuilding[$key] == 0 ?  0 : $victimBuilding[$key] - $victimBuilding[$key]* rand(-1*$studyEffect*3, $studyEffect*3)/100);
        }
        foreach ($victimBuildingLvl as $key => $val){
            $victimBuildingLvl[$key]  = floor($victimBuilding[$key] == 0 ?  0 : max($victimBuildingLvl[$key] + rand(-1*$studyEffect*2, $studyEffect*2) , 1));
            $Places[] = "$key = '$victimBuilding[$key]-$val'";
        }

        $quary = "id_report = $idReport, id_city = '{$Victim["id_city"]}', id_player = '{$Victim["id_player"]}',". implode(", ", $Places).", food_res = {$victimCity["food"]}, city_lvl = {$victimCity["lvl"]},"
        . " wood_res = {$victimCity["wood"]}, stone_res = {$victimCity["stone"]}, metal_res = {$victimCity["metal"]}, coin_res = {$victimCity["coin"]}, army_a = {$victimCity["army_a"]}, "
        . "army_b = {$victimCity["army_b"]}, army_c = {$victimCity["army_c"]}, army_d = {$victimCity["army_d"]}, army_e = {$victimCity["army_e"]}, army_f = {$victimCity["army_f"]}";

        insertIntoTable($quary, "spy_city");

      
    }

    private function cityVictimWin($Spy, $Victim)
    {
        
        $player_spy  = selectFromTable("spies, x, y, name, id_city, id_player", "city", "id_city = :idc", ["idc" => $Spy["id_city"]]);
        $now = time();
        
        
        $id_report = insertIntoTable("id_player = {$Victim["id_player"]} , x_coord = {$Spy["x_to"]}, y_coord = {$Spy["y_to"]}, spy_city = {$Spy["id_city"]} , time_stamp = {$now} , state = 0", "spy_report");

        $content = "قامت المدينة {$player_spy[0]["name"]} [{$player_spy[0]["x"]} , {$player_spy[0]["y"]}] بحملة تجسس على المدينة {$Victim["name"]} [{$Spy["x_to"]} , {$Spy["y_to"]}]  باستخدام {$Spy["spy_num"]}  جاسوس وخسرت مدينتك {$Spy["spy_num"]} جاسوس خلال هذة الحملة";
        $victim_report = "id_report = $id_report , id_player = {$Victim["id_player"]} , x_coord = {$Spy["x_to"]}, y_coord = {$Spy["y_to"]}, city_name = '{$Victim["name"]}' , content = '{$content}' , time_stamp = {$now}";

        $id_report_player = insertIntoTable("id_player = {$Spy["id_player"]} , x_coord = {$Spy["x_to"]}, y_coord = {$Spy["y_to"]}, spy_city = {$Spy["id_city"]} , time_stamp = {$now} , state = 0", "spy_report");       
        $content_player = "فشل محاولة التجسس على المدينة {$Victim["name"]} [{$Spy["x_to"]} , {$Spy["y_to"]}] وخسرت {$Spy["spy_num"]}جاسوس فى هذة المحاولة ";
        $player_report = "id_report = $id_report_player , id_player = {$Spy["id_player"]} ,"
                . " x_coord = {$Spy["x_to"]}, y_coord = {$Spy["y_to"]}, city_name = '{$Victim["name"]}' ,"
                . " content = '{$content_player}' , time_stamp = {$now}";
        insertIntoTable($victim_report, "spy_victim");
        insertIntoTable($player_report, "spy_victim");
       
    }
    
    
    private function spyOnBarray($Spy)
    {
      
        $now = time();
        $Army = [0 => 0, "army_a"=>0, "army_b"=>0, "army_c"=>0, "army_d"=>0, "army_e"=>0, "army_f"=>0];
        $barryHeros  = selectFromTable("*", "world_unit_hero", "x = {$Spy["x_to"]} AND y = {$Spy["y_to"]}");
        
        foreach ($barryHeros as $oneHero)
        {
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_1_type"]]] += $oneHero["f_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_2_type"]]] += $oneHero["f_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_3_type"]]] += $oneHero["f_3_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_1_type"]]] += $oneHero["b_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_2_type"]]] += $oneHero["b_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_3_type"]]] += $oneHero["b_3_num"];
        }
        $id_report = insertIntoTable("id_player = {$Spy["id_player"]} , x_coord = {$Spy["x_to"]},"
                    . " y_coord = {$Spy["y_to"]}, spy_city = {$Spy["id_city"]} ,"
                . " time_stamp = {$now} , spy_for = 'barrary'  , state = 1", "spy_report");

        insertIntoTable("id_report = $id_report , army_a = {$Army["army_a"]}, army_b = {$Army["army_b"]},"
                        . " army_c = {$Army["army_c"]}, army_d = {$Army["army_d"]},"
                        . " army_e = {$Army["army_e"]},  army_f = {$Army["army_f"]}"
                    , "spy_barray");
                        
        return [$Spy["id_player"]];

    }
}
