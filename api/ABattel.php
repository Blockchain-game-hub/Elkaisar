<?php

class ABattel
{
    
    function inBattelHeros()
    {
        global $idPlayer;
    
        return selectFromTable(
                "battel_member.id_hero , battel_member.id_battel , battel.task , "
                . "battel_member.side , battel.time_start , battel.time_end , "
                . "battel.x_coord ,battel.y_coord , battel.x_city , battel.y_city",
                "FROM battel_member JOIN battel ON battel.id_battel = battel_member.id_battel", 
                "battel_member.id_player  =:idp", ["idp" => $idPlayer]);
    }
    
    function getReturningHeros()
    {
        global $idPlayer;
        return selectFromTable(
                "hero_back.*, hero.id_city, hero.name AS h_name",
                "hero JOIN hero_back ON hero.id_hero = hero_back.id_hero", 
                "hero_back.id_player = :idp",["idp" => $idPlayer]);
    
    }
    
    function abort()
    {
        
        global $idPlayer;
        $idHero   = validateID($_POST["idHero"]);
        $Battel   = selectFromTable("*", "battel", "id_hero = :idh", ["idh" => $idHero]);
        $Hero     = selectFromTable(
                "battel.*, battel_member.id_hero AS idHero", 
                "battel_member JOIN battel ON battel.id_battel = battel_member.id_battel",
                "battel_member.id_player = :idp AND battel_member.id_hero = :idh", 
                ["idp" => $idPlayer, "idh" => $idHero]
                );
        
        if(!count($Hero))
            return ["state" => "error_0", "TryTOHack" => TryToHack()];
        if(count($Battel))
            return $this->abortByLeader($Battel[0]);
        
        return $this->retreatHero($Hero[0]);    
        
        
    
    }
    
    private function abortByLeader($Battel)
    {
        deleteTable("battel", "id_battel = :idb", ["idb" => $Battel["id_battel"]]);
        $Heros = selectFromTable("id_hero, id_player", "battel_member", "id_battel = :idb", ["idb" => $Battel["id_battel"]]);
        deleteTable("battel_member", "id_battel = :idb ", ["idb" => $Battel["id_battel"]]);
        
        foreach ($Heros as $oneHero){
            LBattel::abortBattel($oneHero, $Battel);
        }
            
        return [
            "state"     => "ok",
            "idPlayers" => array_unique(array_column($Heros, "id_player")),
            "Battel"    => [],
            "idBattel"  => $Battel["id_battel"]
        ];
        
    }
    
    private function retreatHero($Battel)
    {
        global $idPlayer;
        $cumulative = 
                
        selectFromTable(
                "COUNT(*) AS c", 
                "battel_member", "id_battel = :idb AND ord > (SELECT ord FROM battel_member WHERE id_battel = :idb2 AND id_hero = :idh)",
                ["idb" => $Battel["id_battel"], "idb2" => $Battel["id_battel"], "idh" => $Battel["idHero"]])[0]["c"];
        
        if($cumulative > 0)
            return ["state" => "error_2"];
        
        LBattel::abortBattel(["id_player" => $idPlayer, "id_hero" => $Battel["idHero"]], $Battel);
        deleteTable("battel_member", "id_hero = :idh", ["idh" => $Battel["idHero"]]);
        return [
            "state" => "ok",
            "idPlayers" => [$idPlayer],
            "Battel" => LBattel::getBattelById($Battel["id_battel"]),
            "idBattel" => $Battel["id_battel"]
        ];
    }
    
    
    function start()
    {
        global $idPlayer;
        $idHero        = validateID($_POST["idHero"]);
        $xCoord        = validateID($_POST["xCoord"]);
        $yCoord        = validateID($_POST["yCoord"]);
        $attackTask    = validateID($_POST["task"]);
        $Hero          = selectFromTable("hero.in_city, city.x, city.y, hero.power, hero.id_city, hero.id_hero, hero.id_player", "hero JOIN city ON city.id_city = hero.id_city", "hero.id_hero = :idh AND hero.id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $Unit          = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord]);
        $powerNeeded   = LBattel::takeHeroPower($idHero, $Unit[0]["ut"]);
        
        if(!count($Hero) || !count($Unit))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(LHeroArmy::isCarringArmy($idHero) == false)
            return ["state" => "hero_carry_no_army"];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "not_in_city", "TryToHack" => TryToHack()];
        if(CWorldUnit::isOverLastLvl($Unit[0]))
            return ["state" => "no_more_lvls", "TryToHack" => TryToHack()];
        if($Unit[0]["lo"] == WU_lOCKED_UNIT)
            return ["state" => "locked_unit", "TryToHack" => TryToHack()];
        if(!LWorldUnit::heroCanAttack($idHero, $Unit[0]["ut"]))
            return ["state" => "hero_cant_used", "TryToHack" => TryToHack()];
        if(!LWorldUnit::isAttackable($idHero, $Unit[0]))
            return ["state" => "in_attackable", "TryToHack" => TryToHack()];
        if(!LBattel::takeStartingPrice($Unit[0]["ut"]))
            return ["state" => "no_enough_mat", "TryToHack" => TryToHack()];
        if($powerNeeded <= 0)
            return ["state" => "no_enough_hero_power", "TryToHack" => TryToHack()];
        if(!LWorld::onTheRoleInAttQue($Unit[0]))
            return ["state" => "not_his_role"];
        
       
        
        updateTable("s = :s", "world", "x = :x AND y = :y", ["s" => WU_ON_FIRE, "x" => $xCoord , "y" => $yCoord]);
        updateTable("in_city = 0", "hero", "id_hero = :idh", ["idh" => $idHero]);
        
        $attackTime = LWorldUnit::calAttackTime($idHero, $Hero[0], $Unit[0]);
        $now        = time();
        
        $idBattel = insertIntoTable(
                "id_hero = :hl, time_start = :ts, time_end = :te, x_coord = :xt, y_coord = :yt, id_player = :idp, x_city = :xf , y_city = :yf , task = :tk",
                "battel", [
                    "hl" => $idHero, "ts" => $now, "te" => $now + $attackTime, "xt" => $Unit[0]["x"], "yt" => $Unit[0]["y"],
                    "idp" => $idPlayer, "xf" => $Hero[0]["x"], "yf" => $Hero[0]["y"], "tk" => $attackTask
                ]);
        $Battel =  LBattel::getBattelById($idBattel);
        
        
        LBattel::announceStart($Unit[0]);
        (new LWebSocket())->send(json_encode([
            "url" => "Battel/newBattelStarted",
            "data" => [
                "Battel" => $Battel
        ]]));
        LBattel::join($Battel, $Hero[0], BATTEL_SIDE_ATT);
        return [
                    "state"          => "ok",
                    "Battel"         => $Battel,
                    "StartingPrice"  => LBattel::startingPrice($Unit[0]["ut"]),
                    "InvolvedPlayer" => array_unique(array_merge(LBattel::involvedPlayers($Unit[0]), [$idPlayer])),
                    "newFire"        => $Unit[0]["s"] == WU_ON_FIRE ? false : true
                ];
     
    
    }
    
    function applyForRoleInAttQue()
    {
        
        $xCoord = validateID($_POST["xCoord"]);
        $yCoord = validateID($_POST["yCoord"]);
        $Unit   = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord]);
        
        if(LWorldUnit::isRepelCastle($Unit[0]["ut"]))
            return $this->applyForRoleInAttRCQue ($Unit[0]);
        
        return [
            "state" => "error"
        ];
    }
    
    private function applyForRoleInAttRCQue($Unit)
    {
        global $idPlayer;
        $PlayerGuild = selectFromTable("id_player, id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $GuildRole   = selectFromTable("id", 
                "world_attack_queue JOIN world ON world.x = world_attack_queue.x_coord AND world.y = world_attack_queue.y_coord",
                "world_attack_queue.id_guild = :idg AND world.ut IN (".WUT_REPLE_CASTLE_A.", ".WUT_REPLE_CASTLE_B.", ".WUT_REPLE_CASTLE_C.")",
                ["idg" => $PlayerGuild[0]["id_guild"]]);
        
        $RoleCount   = selectFromTable("COUNT(*) AS c", "world_attack_queue", "x_coord = :x AND y_coord = :y", ["x" => $Unit["x"], "y" => $Unit["y"]])[0]["c"];
        
        if(count($GuildRole) > 0)
            return [ "state" => "error_0" ];
        if($RoleCount >= 10)
            return [ "state" => "error_1"];
        if(!count($PlayerGuild))
            return [ "state" => "error_2" ];
        if($PlayerGuild[0]["rank"] < GUILD_R_DEPUTY_2)
            return [ "state" => "error_3" ];
        if(!LItem::useItem("bronze_horn"))
            return [ "state" => "error_4" ];
        
        $Pram = [];
        if(LWorldUnit::isRepelCastleS($Unit["ut"]))
            $Pram = ["idg" => $PlayerGuild[0]["id_guild"], "f" => 10e5, "w" => 10e5, "s" => 10e5, "m" => 10e5, "c" => 10e4];
        if(LWorldUnit::isRepelCastleM($Unit["ut"]))
            $Pram = ["idg" => $PlayerGuild[0]["id_guild"], "f" => 10e6, "w" => 10e6, "s" => 10e6, "m" => 10e6, "c" => 10e5];
        if(LWorldUnit::isRepelCastleH($Unit["ut"]))
            $Pram = ["idg" => $PlayerGuild[0]["id_guild"], "f" => 10e7, "w" => 10e7, "s" => 10e7, "m" => 10e7, "c" => 10e6];
        
        
        updateTable (
                "food = CAST( food AS SIGNED ) - :f, wood = CAST( wood AS SIGNED ) - :w, stone = CAST( stone AS SIGNED ) - :s, metal = CAST( metal AS SIGNED ) - :m, coin = CAST( food AS SIGNED ) - :c",
                "guild", "id_guild = :idg", $Pram);
        
        $timeStart = 0;
        
        if($RoleCount == 0)
            $timeStart = gmmktime(13,0,0, gmdate('n'), gmdate('j') + 1, gmdate('Y'));
        else{
            $LastRole = selectFromTable("id, time_start", "world_attack_queue", "x_coord = :x AND y_coord = :y ORDER BY id DESC LIMIT 1", ["x" => $Unit["x"], "y" => $Unit["y"]])[0];
            $timeStart = $LastRole["time_start"] + 24*60*60;
        }
        
        $timeEnd   = $timeStart + 60*60;
        
        insertIntoTable( "x_coord = :x, y_coord = :y, id_guild = :idg, id_player = :idp, time_start = :ts, time_end = :te", "world_attack_queue", 
                ["x" => $Unit["x"], "y" => $Unit["y"], "idg" => $PlayerGuild[0]["id_guild"], "idp" => $idPlayer, "ts" => $timeStart, "te" => $timeEnd]);
        return [
            "state" => "ok",
            "QueueList" => LWorld::getWorldAttackQueueForGuild($Unit)
        ];
        
    }
}
