<?php

class ATransferPlayer
{
    
    private $Player = [];
            
    private $newCityId =[];
    private $newHeroId =[];
    
    function startTrans()
    {
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer     = validateID($_POST["idPlayer"]);
        $idServerFrom = validateID($_POST["idServerFrom"]);
        $idServerTo   = validateID($_POST["idServerTo"]);
        
        DbConnect($idServerFrom);
        $player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $idPlayer]);
        $guildLeader = selectFromTable("id_guild", "guild", "id_leader = :idl", ["idl" => $idPlayer]);
        
        if(!count($player))
            return ["state" => "error_2"];
        if(count($guildLeader))
            return ["state" => "error_3"];
        
        DbConnect($idServerTo);
        $guildLeaderTo = selectFromTable("id_guild", "guild", "id_leader = :idl", ["idl" => $idPlayer]);
         
        if(count($guildLeaderTo))
            return ["state" => "error_2"];
         
        
        $playerTo = selectFromTable("*", "player", "id_player = :idp", ["idp" => $idPlayer]);
        if(count($playerTo)){
            
        
            insertIntoTable("id_player = :idp, name = :n, prestige = :p, honor =:h, porm = :po", "player_transfer", ["idp" => $idPlayer, "n" => $playerTo[0]["name"], "p" => $playerTo[0]["prestige"], "h" => $playerTo[0]["honor"], "po" => $playerTo[0]["porm"] ]);
        
            $this->saveOldData();
            $this->clearOldData();
        }
        
        $this->getPlayerAcount($idPlayer);
        $this->transCity();
        $this->transHero();
        $this->transTo($idServerTo);
         
        DbConnect($idServerFrom);
        insertIntoTable("id_player = :idp, name = :n, prestige = :p, honor =:h, porm = :po", "player_transfer", ["idp" => $idPlayer, "n" => $player[0]["name"], "p" => $player[0]["prestige"], "h" => $player[0]["honor"], "po" => $player[0]["porm"] ]);
        
        $this->saveOldData();
        $this->clearOldData();
        
        
        return ["state" => "ok"];
        
        
        
    }
    
    
    function deletePlayer()
    {
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        
        $guildLeader = selectFromTable("*", "guild", "id_leader = :idp", ["idp" => $idPlayer]);
        
        if(count($guildLeader))
            return ["state" => "error_2"];
        
        $Player = selectFromTable("*", "player", "id_player = $idPlayer");
        if(!count($Player))
            return ["state" => "error_3"];
        
        insertIntoTable("id_player = :idp, name = :n, prestige = :p, honor =:h, porm = :po", "player_transfer", ["idp" => $idPlayer, "n" => $Player[0]["name"], "p" => $Player[0]["prestige"], "h" => $Player[0]["honor"], "po" => $Player[0]["porm"] ]);
        $this->saveOldData();
        $this->clearOldData();
        
        return [
            "state" => "ok"
        ];
        
    }

    
    function  restorPlayer()
    {
        $idPlayer     = validateID($_POST["idPlayer"]);
       
        $Player = selectFromTable("*", "player_transfer", "id_player = :idp", ["idp" => $idPlayer]);
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        if(!count($Player))
            return ["state" => "error_2"];
        $this->clearOldData();
        
        unset($Player[0]["id_player"]);
        unset($Player[0]["name"]);
        unset($Player[0]["prestige"]);
        unset($Player[0]["honor"]);
        unset($Player[0]["porm"]);
        
        foreach ($Player[0] as $table => $Record)
        {
            
            $array = json_decode($Record, TRUE);
          
           
            foreach ($array as $oneRecord)
            {
                
                $arr = [];
                foreach ($oneRecord as $key => $val)
                {
                    $arr[] = "$key = '$val'";
                }
                insertIntoTable(implode(",", $arr), $table);
                
            
            }
            
        }
       // deleteTable("player_transfer", "id_player = :idp", ["idp" => $idPlayer]);
        return ["state" => "ok"];
        
    }

    private function transCity()
    {
        $index = 0;
        foreach ($this->Player["city"] as $key => $oneCity)
        {
            $index++;
            
            $EmptyCoord = selectFromTable("x, y", "world", "ut = 0 AND t = 0 ORDER BY RAND() LIMIT 1");
            updateTable("t = :t, ut = :ut", "world", "x = :x AND y = :y", ["t" => 17 + $oneCity["lvl"], "ut" => 60 + $oneCity["lvl"], "x" => $EmptyCoord[0]["x"], "y" => $EmptyCoord[0]["y"]]);
            deleteTable("world_unit_garrison", "x_coord = :x AND y_coord = :y", ["x" => $EmptyCoord[0]["x"], "y" => $EmptyCoord[0]["y"]]);
            
            $idCityNew = ($oneCity["id_player"] -1 )*10 + $index;
            $this->newCityId[$oneCity["id_city"]] = $idCityNew;
            $this->Player["city"][$key]["id_city"] = $idCityNew;
            $this->Player["city"][$key]["x"] = $EmptyCoord[0]["x"];
            $this->Player["city"][$key]["y"] = $EmptyCoord[0]["y"];
            $this->Player["city_building"][$key]["id_city"] = $idCityNew;
            $this->Player["city_building_lvl"][$key]["id_city"] = $idCityNew;
            $this->Player["city_jop"][$key]["id_city"] = $idCityNew;
            $this->Player["city_storage"][$key]["id_city"] = $idCityNew;
            $this->Player["city_theater"][$key]["id_city"] = $idCityNew;
            $this->Player["city_wounded"][$key]["id_city"] = $idCityNew;
        }
        
    }
    
    private function transHero()
    {
        $index = 0;
        foreach ($this->Player["hero"] as $key => $oneHero)
        {
            $index++;
            
            $idHeroNew = ($oneHero["id_player"] -1 )*1000 + $index;
            $this->newHeroId[$oneHero["id_hero"]] = $idHeroNew;
            $this->Player["hero"][$key]["id_city"] = $this->newCityId[$oneHero["id_city"]];
            $this->Player["hero"][$key]["id_hero"] = $idHeroNew;
            $this->Player["hero_army"][$key]["id_hero"] = $idHeroNew;
            $this->Player["hero_medal"][$key]["id_hero"] = $idHeroNew;
        }
        
    }
    
    private function saveOldData()
    {
        $idPlayer     = validateID($_POST["idPlayer"]);
        
        updateTable("arena_player_challange = :a","player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("build_army = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "build_army",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("buy_item = :a",              "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "buy_item",               "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city = :a",                  "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city",                   "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_bar = :a",              "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_bar",               "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_building = :a",         "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_building",          "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_building_lvl = :a",     "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_building_lvl",      "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_jop = :a",              "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_jop",               "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_storage = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_storage",           "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_theater = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_theater",           "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("city_wounded = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "city_wounded",           "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("equip = :a",                 "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "equip",                  "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        
        updateTable("god_gate = :a",              "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "god_gate",               "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("god_gate_1 = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "god_gate_1",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("god_gate_2 = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "god_gate_2",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("god_gate_3 = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "god_gate_3",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("god_gate_4 = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "god_gate_4",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("hero = :a",                  "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "hero",                   "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("hero_army = :a",             "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "hero_army",              "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("hero_medal = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "hero_medal",             "id_hero IN (SELECT id_hero FROM hero WHERE id_player = :idp)", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("hero_theater = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "hero_theater",           "id_city IN (SELECT id_city FROM city WHERE id_player = :idp)", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("market_deal = :a",           "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "market_deal",            "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
       
        updateTable("player = :a",                "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "player",                 "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("player_edu = :a",            "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "player_edu",             "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("player_item = :a",           "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "player_item",            "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("player_stat = :a",           "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "player_stat",            "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("player_title = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "player_title",           "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("quest_player = :a",          "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "quest_player",           "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
        updateTable("world_unit_garrison = :a",   "player_transfer", "id_player = :idp", ["a" => json_encode(selectFromTable("*", "world_unit_garrison",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "idp" => $idPlayer]);
      
        updateTable("matrial_acce     = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_acce",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_box      = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_box",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_box_plus = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_box_plus",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_flags    = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_flags",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_luxury   = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_luxury",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_main     = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_main",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        updateTable("matrial_product  = :a",   "player_transfer",  "id_player = :idp", ["idp" => $idPlayer, "a" => json_encode(selectFromTable("*", "matrial_product",    "id_player = :idp", ["idp" => $idPlayer]), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
        
    }
    
    private function clearOldData()
    {
        $idPlayer     = validateID($_POST["idPlayer"]);
        
        queryExe("UPDATE world JOIN city ON city.x = world.x AND city.y = world.y SET world.t = 0 WHERE city.id_player = :idp ", ["idp" => $idPlayer]);
        deleteTable("arena_player_challange_hero", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("battel_member", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("battel", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("build_army", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_storage", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_jop_hiring", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_jop", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_worker", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_building_lvl", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_building", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_bar", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("hero_theater", "id_city IN(SELECT id_city FROM city WHERE id_player = :idp)", ["idp" => $idPlayer]);
        deleteTable("city_theater", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("city_wounded", "id_player = :idp", ["idp" => $idPlayer]);
        
        
        deleteTable("edu_uni", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("edu_acad", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("guild_inv", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("guild_req", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("hero_medal", "id_hero IN (SELECT id_hero FROM hero WHERE id_player = :idp)", ["idp" => $idPlayer]);
        deleteTable("hero_equip", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("hero_deleted", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("hero_back", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("hero_army", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("hero", "id_player = :idp", ["idp" => $idPlayer]);
        
        
        deleteTable("market_deal", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("study_acad", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("study_tasks", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("study_uni", "id_player = :idp", ["idp" => $idPlayer]);
        
       
        
        deleteTable("matrial_acce", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_box", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_box_plus", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_flags", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_luxury", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_main", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("matrial_product", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("msg_diff", "id_to = :idp", ["idp" => $idPlayer]);
        deleteTable("msg_income", "id_to = :idp", ["idp" => $idPlayer]);
        deleteTable("msg_out", "id_from = :idp", ["idp" => $idPlayer]);
        deleteTable("msg_out", "id_to = :idp", ["idp" => $idPlayer]);
        deleteTable("player_title", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("player_stat", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("player_item", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("quest_player", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("report_player", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("spy", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("spy_city", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("spy_victim", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("spy_report", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("world_unit_garrison", "id_player = :idp", ["idp" => $idPlayer]);
        
        
        deleteTable("city", "id_player = :idp", ["idp" => $idPlayer]);
         
        deleteTable("equip", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("exchange_player", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("god_gate", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("god_gate_1", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("god_gate_2", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("god_gate_3", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("god_gate_4", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("buy_item", "id_player = :idp", ["idp" => $idPlayer]);
        
        deleteTable("player", "id_player = :idp", ["idp" => $idPlayer]);
    }
    
    
    
    private function getPlayerAcount($idPlayer)
    {
        $idServerFrom = validateID($_POST["idServerFrom"]);
        DbConnect($idServerFrom);
        
        $this->Player = [
            "arena_player_challange" => selectFromTable("*",            "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]),
            "city"                   => selectFromTable("*",            "city",               "id_player = :idp ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_building"          => selectFromTable("*",            "city_building",      "id_player = :idp ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_building_lvl"      => selectFromTable("*",            "city_building_lvl",  "id_player = :idp ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_jop"               => selectFromTable("*",            "city_jop",           "id_player = :idp ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_storage"           => selectFromTable("*",            "city_storage",       "id_player = :idp   ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_theater"           => selectFromTable("*",            "city_theater",       "id_player = :idp  ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "city_wounded"           => selectFromTable("*",            "city_wounded",       "id_player = :idp ORDER BY id_city ASC", ["idp" => $idPlayer]),
            "equip"                  => selectFromTable("*",            "equip",              "id_player = :idp", ["idp" => $idPlayer]),
            "god_gate"               => selectFromTable("*",            "god_gate",           "id_player = :idp", ["idp" => $idPlayer]),
            "god_gate_1"             => selectFromTable("*",            "god_gate_1",         "id_player = :idp", ["idp" => $idPlayer]),
            "god_gate_2"             => selectFromTable("*",            "god_gate_2",         "id_player = :idp", ["idp" => $idPlayer]),
            "god_gate_3"             => selectFromTable("*",            "god_gate_3",         "id_player = :idp", ["idp" => $idPlayer]),
            "god_gate_4"             => selectFromTable("*",            "god_gate_4",         "id_player = :idp", ["idp" => $idPlayer]),
            "hero"                   => selectFromTable("*",            "hero",               "id_player = :idp ORDER BY id_hero ASC", ["idp" => $idPlayer]),
            "hero_army"              => selectFromTable("*",            "hero_army",          "id_player = :idp ORDER BY id_hero ASC", ["idp" => $idPlayer]),
            "hero_medal"             => selectFromTable("hero_medal.*", "hero_medal JOIN hero ON hero.id_hero = hero_medal.id_hero",       "hero.id_player = :idp ORDER BY id_hero ASC", ["idp" => $idPlayer]),
            "player"                 => selectFromTable("*",            "player",             "id_player = :idp", ["idp" => $idPlayer]),
            "player_edu"             => selectFromTable("*",            "player_edu",         "id_player = :idp", ["idp" => $idPlayer]),
            "player_item"            => selectFromTable("*",            "player_item",        "id_player = :idp", ["idp" => $idPlayer]),
            "player_stat"            => selectFromTable("*",            "player_stat",        "id_player = :idp", ["idp" => $idPlayer]),
            "quest_player"           => selectFromTable("*",            "quest_player",             "id_player = :idp", ["idp" => $idPlayer]),
            "study_acad"             => selectFromTable("*",            "study_acad",             "id_player = :idp", ["idp" => $idPlayer]),
            "study_uni"              => selectFromTable("*",            "study_uni",             "id_player = :idp", ["idp" => $idPlayer]),
            "matrial_acce    "       => selectFromTable("*",            "matrial_acce     ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_box     "       => selectFromTable("*",            "matrial_box      ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_box_plus"       => selectFromTable("*",            "matrial_box_plus ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_flags   "       => selectFromTable("*",            "matrial_flags    ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_luxury  "       => selectFromTable("*",            "matrial_luxury   ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_main    "       => selectFromTable("*",            "matrial_main     ", "id_player = :idp", ["idp" => $idPlayer] ), 
            "matrial_product "       => selectFromTable("*",            "matrial_product  ", "id_player = :idp", ["idp" => $idPlayer] )
        ];
        
        foreach ($this->Player["equip"] as $key => $val)
        {
            unset($this->Player["equip"][$key]["id_equip"]);
        }
        
    }
    
    
    private function transTo($idServerTo)
    {
        $idPlayer     = validateID($_POST["idPlayer"]);
        
        DbConnect($idServerTo);
        
        foreach ($this->Player as $tableName => $Rows)
        {
            
            foreach ($Rows as $oneRow){
                $arr = [];
                foreach ($oneRow as $key => $val)
                {
                    $arr[] = "$key = '$val'";
                }
                insertIntoTable(implode(",", $arr), $tableName);
                
            }
            
        }
        
        queryExe("INSERT IGNORE INTO hero_equip(id_player, id_hero) SELECT {$idPlayer}, id_hero FROM hero WHERE id_player = :idp", ["idp" => $idPlayer]);
        
    }
}
