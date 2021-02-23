<?php

class LWorld {

    static function distanceBetween($xFrom, $yFrom, $xTo, $yTo) {
        return floor(sqrt(pow(($xFrom - $xTo), 2) + pow(($yFrom - $yTo), 2)) * 6000);
    }

    static function distanceBetweenCities($idCityFrom, $idCityTo) {
        $coordFrom = selectFromTable("x, y", "city", "id_city = :idc" , ["idc" => $idCityFrom]);
        $coordTo = selectFromTable("x, y", "city", "id_city = :idc" , ["idc" => $idCityTo]);

        return static::distanceBetween($coordFrom[0]["x"], $coordFrom[0]["y"], $coordTo[0]["x"], $coordTo[0]["y"]);
    }

    static function UnitData($xCoord, $yCoord) {
        return selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord])[0];
    }
    
    
    static function cityWall($Unit)
    {  
        $cityWall = selectFromTable("wall_a, wall_b, wall_c, id_city, id_player", "city", "x = {$Unit["x"]} AND y = {$Unit["y"]}");
        if(count($cityWall))
            return [
                "id_hero" => 0, "id_player" => $cityWall[0]["id_player"],
                "id_city" => $cityWall[0]["id_player"], "x" => $Unit["x"], "y" => $Unit["y"],
                "side" => BATTEL_SIDE_DEF,
                "is_garrsion"  =>FALSE,
                "name" => "السور",
                "ord" => -1,
                "pre" => [
                    "f_1" => $cityWall[0]["wall_a"]/2,
                    "f_2" => $cityWall[0]["wall_b"]/2,
                    "f_3" => $cityWall[0]["wall_c"]/2,
                    "b_1" => $cityWall[0]["wall_a"]/2,
                    "b_2" => $cityWall[0]["wall_b"]/2,
                    "b_3" => $cityWall[0]["wall_c"]/2
                ],
                "type" => [
                    "f_1" => ARMY_WALL_A,
                    "f_2" => ARMY_WALL_B,
                    "f_3" => ARMY_WALL_C,
                    "b_1" => ARMY_WALL_A,
                    "b_2" => ARMY_WALL_B,
                    "b_3" => ARMY_WALL_C
                ]
            ];
        
        return null;
        
    }

    static function unitHeros($Unit) {

        $heros = selectFromTable("*", "world_unit_hero", "x = :x AND y= :y AND lvl = :l ORDER BY ord ASC", ["x" => $Unit["x"], "y" => $Unit["y"], "l" => $Unit["l"]]);
        $herosToSend = [];
        foreach ($heros as $one) {
            $herosToSend[] = [
                "name" => $one["name"],
                "ord" => $one["ord"],
                "pre" => [
                    "f_1" => $one["f_1_num"],
                    "f_2" => $one["f_2_num"],
                    "f_3" => $one["f_3_num"],
                    "b_1" => $one["b_1_num"],
                    "b_2" => $one["b_2_num"],
                    "b_3" => $one["b_3_num"]
                ],
                "type" => [
                    "f_1" => $one["f_1_type"],
                    "f_2" => $one["f_2_type"],
                    "f_3" => $one["f_3_type"],
                    "b_1" => $one["b_1_type"],
                    "b_2" => $one["b_2_type"],
                    "b_3" => $one["b_3_type"]
                ]
            ];
        }

        return $herosToSend;
    }
    
    static function unitGarrisonHero($Unit) {
        return
                selectFromTable("world_unit_garrison.* , hero.id_city, "
                . " hero.point_b, hero.point_b_plus, hero.point_c, hero.point_c_plus  , "
                . " hero_medal.medal_den , hero_medal.medal_leo, city.x ,city.y ",
                "world_unit_garrison JOIN hero ON hero.id_hero = world_unit_garrison.id_hero"
                . " JOIN hero_medal ON hero_medal.id_hero = world_unit_garrison.id_hero"
                . " JOIN city ON city.id_city = hero.id_city",
                "world_unit_garrison.x_coord = :x AND world_unit_garrison.y_coord = :y ORDER BY ord ASC", ["x" => $Unit["x"], "y" => $Unit["y"]]);
    }

    static function afterWinAnnounceable($Unit) {
        if (
                LWorldUnit::isArena      ($Unit["ut"]) ||
                LWorldUnit::isArmyCapital($Unit["ut"]) ||
                LWorldUnit::isStatueWalf ($Unit["ut"]) ||
                LWorldUnit::isStatueWar  ($Unit["ut"]) ||
                LWorldUnit::isRepelCastle($Unit["ut"]) ||
                LWorldUnit::isQueenCity  ($Unit["ut"]))
            
            return true;
        
        if(LWorldUnit::isMonawrat($Unit["ut"]) ||LWorldUnit::isCamp($Unit["ut"]))
            if($Unit["l"]%10 == 0)
                return true;
            
        return false;
            
    }
    
    static function fireOffUnit($Unit)
    {
        $FireCount = selectFromTable("COUNT(*) AS c", "battel", "x_coord = :x AND y_coord = :y", ["x" => $Unit["x"], "y" => $Unit["y"]])[0]["c"];
        if($FireCount > 0)
            return false;
        updateTable("s = :s", "world", "x = :x AND y = :y", ["x" => $Unit["x"], "y" => $Unit["y"], "s" => WU_OFF_FIRE]);
        return true;
    }
    
    static function lvlChanged($Unit)
    {
        
    }

    static function getEmptyPlace($province)
    {
        return 
            selectFromTable("*", "world", "ut = 0 AND p = :p ORDER BY RAND() LIMIT 1", ["p" => $province]);
    }
    
   

    static function onTheRoleInAttQue($Unit){
        
        global $idPlayer;
        
        if(!LWorldUnit::isRepelCastle($Unit["ut"]))
            return true;
        
        $GuildPlayer = selectFromTable("id_player, id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildPlayer))
            return false;
        if($GuildPlayer[0]["rank"] < GUILD_R_DEPUTY_2)
            return false;
        $QueueRole = selectFromTable("*", "world_attack_queue", "x_coord = :x AND y_coord = :y ORDER BY id ASC LIMIT 1", ["x" => $Unit["x"], "y" => $Unit["y"]]);
        
        if(!count($QueueRole))
            return false;
        if($QueueRole[0]["id_guild"] != $GuildPlayer[0]["id_guild"])
            return false;
        if(time() < $QueueRole[0]["time_start"])
            return false;
        if(time() > $QueueRole[0]["time_end"])
            return false;
        
        return true;
    }
    
    
    static function getWorldAttackQueueForGuild($Unit)
    {
       
        return selectFromTable(
                "guild.name AS GuildName, guild.id_guild, guild.slog_top,"
                . "guild.slog_cnt, guild.slog_btm, world_attack_queue.time_start,"
                . " world_attack_queue.time_end", 
                "world_attack_queue LEFT JOIN guild ON guild.id_guild = world_attack_queue.id_guild", 
                "world_attack_queue.x_coord = :x AND world_attack_queue.y_coord = :y",
                ["x" => $Unit["x"], "y" => $Unit["y"]]);
        
    }
    
    static function removeCityColonizer ($idCity)
    {
        
        deleteTable("city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]);
        
    }
}
