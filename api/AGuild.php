<?php

class AGuild {

    function create() {
        
        global $idPlayer;
        $guildName = validatePlayerWord($_POST["guildName"]);
        $slogTop = validateID($_POST["slogTop"]);
        $slogMiddle = validateID($_POST["slogMiddle"]);
        $slogBottom = validateID($_POST["slogBottom"]);
        $idCity = validateID($_POST["idCity"]);
        $guildWithSameName = selectFromTable("COUNT(*) AS c", "guild", "name = :n", ["n" => $guildName]);
        $PlayerGuildMem = selectFromTable("COUNT(*) AS C", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if (mb_strlen($guildName) > 15) return ["state" => "error_0"];
        if ($guildWithSameName[0]["c"] > 0) return ["state" => "error_1"];
        if($PlayerGuildMem[0]["C"] > 0)          return ["state" => "error_2"];
        if (!LCity::isResourceTaken(["coin" => 1e5], $idCity)) return ["state" => "error_3"];
        
        $idGuild = insertIntoTable(
                "id_leader = :idp, name = :na, slog_top = :st, slog_cnt = :sc, slog_btm = :sb",
                "guild", ["idp" => $idPlayer, "na" => $guildName, "st" => $slogTop, "sb" => $slogBottom, "sc" => $slogMiddle]
        );
        
        
        $GuildData = LGuild::getGuildData($idGuild);
        
        if(!$GuildData)
            return ["state" => "error_4"];
        
        LGuild::addPlayer($idGuild, $idPlayer, GUILD_R_LEADER);
        $PlayerGuild = LGuild::getPlayerGuildData($idPlayer);
        
        $Guild = ["state" => "ok"];
        if ($PlayerGuild) $Guild["PlayerGuild"] = $PlayerGuild;
        if ($GuildData) $Guild["GuildData"] = $GuildData;
        $Guild["Player"] = LPlayer::getData();
        return $Guild;
    }

    function getGuildData() {
        global $idPlayer;
        $idGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        if (!count($idGuild)) return ["state" => "noGuild"];

        return [
            "state" => "ok",
            "Guild" => LGuild::getGuildData($idGuild[0]["id_guild"])
        ];
    }

    function getGuildInvReq() {

        global $idPlayer;

        $idGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);

        if (!count($idGuild))
                return [
                "GuildReq" => [],
                "GuildInv" => []
            ];
        return LGuild::getGuildReqInv($idGuild[0]["id_guild"]);
    }

    function modifyGuildWord()
    {
        global $idPlayer;
        $newWord = validatePlayerWord($_POST["newWord"]);
        $GuildMember = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(mb_strlen($newWord) > 512)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        updateTable("word = :w", "guild", "id_guild = :idg", ["w" => $newWord, "idg" => $GuildMember[0]["id_guild"]]);
        
        return [
            "state" => "ok",
            "GuildData" => selectFromTable("*", "guild", "id_guild = :idg", ["idg" => $GuildMember[0]["id_guild"]])[0]
        ];
    }
    
    function changeGuildRelation()
    {
        global $idPlayer;
        $relation    = validateID($_POST["relation"]);
        $idOtheGuild = validateID($_POST["idGuild"]);
        $GuildMember = selectFromTable("guild_member.*, player.name", "guild_member JOIN player ON player.id_player = guild_member.id_player", "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($relation > 2)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        $GuildRelation = selectFromTable("*", "guild_relation", "id_guild_1 = :idMyG AND id_guild_2 = :idOG", ["idMyG" => $GuildMember[0]["id_guild"], "idOG" => $idOtheGuild]);
        
        if(!count($GuildRelation))
            insertIntoTable ("id_guild_1 = :idg1, id_guild_2 = :idg2, state = :s", "guild_relation",["idg1" => $GuildMember[0]["id_guild"], "idg2" => $idOtheGuild, "s" => $relation]);
        else
            updateTable ("state = :s", "guild_relation", "id_guild_1 = :idg1 AND id_guild_2 = :idg2", ["idg1" => $GuildMember[0]["id_guild"], "idg2" => $idOtheGuild, "s" => $relation]);
        
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/announceRelation",
            "data" => [
                    "PlayerName"   => $GuildMember[0]["name"],
                    "idGuildOne"   => $GuildMember[0]["id_guild"],
                    "idGuildTwo"   => $idOtheGuild,
                    "relation"     => $relation
                ]]));
        
        return  [
            "state" => "ok",
            "Allay" => selectFromTable(
                    "guild.id_guild , guild.name , guild_relation.state",
                    "guild JOIN guild_relation ON guild.id_guild = guild_relation.id_guild_2", 
                    "guild_relation.id_guild_1 = :idg", ["idg"=>$GuildMember[0]["id_guild"]])
        ];
    
    }
    
    
    
    function modifyGuildSlogan()
    {
        
        global $idPlayer;
        $GuildMember  = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $slogTop      = validateID($_POST["slogTop"]);
        $slogMiddle   = validateID($_POST["slogMiddle"]);
        $slogBottom   = validateID($_POST["slogBottom"]);
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY_2)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!LItem::useItem("family_slogan"))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        updateTable("slog_top = :st, slog_cnt = :sm, slog_btm = :sb", "guild", "id_guild = :idg", 
                ["idg" => $GuildMember[0]["id_guild"], "st" => $slogTop, "sm" => $slogMiddle, "sb" => $slogBottom]);
        
        return [
            "state" => "ok",
            "GuildData" => selectFromTable("*", "guild", "id_guild = :idg", ["idg" => $GuildMember[0]["id_guild"]])[0]
        ];
     
    }
    
    function resignFromPosition()
    {
        global $idPlayer;
        $GuildMember  = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] >= GUILD_R_LEADER)
            return ["state" => "error_1"];
        
        updateTable("rank = 0", "guild_member", "id_guild = :idg AND id_player = :idp", ["idg" => $GuildMember[0]["id_guild"], "idp" => $idPlayer]);
        return [
            "state" => "ok",
            "playerGuildData" => LGuild::getPlayerGuildData($idPlayer)
        ];
    }
    
    function quitFromGuild()
    {
        
        global $idPlayer;
        $GuildMember  = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] > GUILD_R_MEMBER)
            return ["state" => "error_1"];
        deleteTable("guild_member", "id_player = :idp", ["idp"=>$idPlayer]);
        updateTable("guild = NULL, id_guild = NULL", "player", "id_player = :idp", ["idp" => $idPlayer]);
        LGuild::updateGuildData($GuildMember[0]["id_guild"]);
        return [
            "state" => "ok",
            "playerGuildData" => LGuild::getPlayerGuildData($idPlayer),
            "playerData" => LPlayer::getData()
        ];
        
    }
    
    function disbandGuild()
    {
        
        global $idPlayer;
        $GuildMember  = selectFromTable("guild_member.*, guild.id_leader", "guild_member JOIN guild ON guild.id_guild = guild_member.id_guild", "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] < GUILD_R_LEADER)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_leader"] != $idPlayer)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        deleteTable("guild_member", "id_guild = :idg", ["idg"=>$GuildMember[0]["id_guild"]]);
        updateTable("guild = NULL, id_guild = NULL"  , "player", "id_guild = :idg", ["idg" => $GuildMember[0]["id_guild"]]);
        deleteTable("guild", "id_leader = :idp", ["idp" => $idPlayer]);
        return [
            "state" => "ok",
            "playerGuildData" => LGuild::getPlayerGuildData($idPlayer),
            "playerData" => LPlayer::getData()
        ];
        
    }
    
    function donateRes()
    {
        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);
        $food   = validateID($_POST["food"]);
        $wood   = validateID($_POST["wood"]);
        $stone  = validateID($_POST["stone"]);
        $metal  = validateID($_POST["metal"]);
        $coin   = validateID($_POST["coin"]);
        $GuildMember  = selectFromTable("*", "guild_member JOIN guild ON guild_member.id_guild = guild.id_guild", "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($food < 0 || $wood < 0 || $stone < 0 || $metal < 0 || $coin < 0)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        if(!LCity::isResourceTaken([
            "food"  => $food,  "wood"  => $wood,
            "stone" => $stone, "metal" => $metal,
            "coin"  => $coin
        ], $idCity))
                return ["state" => "error_2", "TryToHack" => TryToHack()];
            
        
        updateTable(
                "food = food + :f, wood = wood + :w, stone = stone + :s, metal = metal + :m, coin = coin + :c", 
                "guild", "id_guild = :idg", 
                ["idg" => $GuildMember[0]["id_guild"], "f" => $food, "w" => $wood, "s" => $stone, "m" => $metal, "c" => $metal]
                );
        
        return [
            "state" => "ok",
            "GuildData" => LGuild::getGuildData($GuildMember[0]["id_guild"]),
            "CityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    
    }
    function upgradeUsingRes()
    {
        global $idPlayer;
        $GuildMember  = selectFromTable("*", "guild_member JOIN guild ON guild_member.id_guild = guild.id_guild", "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY_2)
            return ["state" => "error_1"];
        if($GuildMember[0]["lvl"] >= 10)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(
                $GuildMember[0]["food"] < $GuildMember[0]["lvl"]*1250000 
                || $GuildMember[0]["wood"] < $GuildMember[0]["lvl"]*1250000 
                || $GuildMember[0]["stone"] < $GuildMember[0]["lvl"]*1250000 
                || $GuildMember[0]["metal"] < $GuildMember[0]["lvl"]*1250000 
                || $GuildMember[0]["coin"] < $GuildMember[0]["lvl"]*1000000 
                )
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("lvl = lvl + 1", "guild", "id_guild = :idg", ["idg" => $GuildMember[0]["id_guild"]]);
        
        return [
            "state" => "ok",
            "GuildData" => LGuild::getGuildData($GuildMember[0]["id_guild"])
        ];
    
    }
    
    function upgradeUsingItem()
    {
        global $idPlayer;
        $GuildMember  = selectFromTable("*", "guild_member JOIN guild ON guild_member.id_guild = guild.id_guild", "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        $itemToUse    = validateGameNames($_POST["itemToUse"]);
        $newLvl = 0;
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY_2)
            return ["state" => "error_1"];
        

        if($itemToUse == "union_slogan"){
            $newLvl =10;
        }else if($itemToUse == "union_declar"){
            $newLvl = 20;
        }else if($itemToUse == "union_era"){
            $newLvl = 30;
        }else{
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        }

        if(!LItem::useItem($itemToUse))
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        
        updateTable("lvl = :lv", "guild", "id_guild = :idg", ["idg"=>$GuildMember[0]["id_guild"], "lv" => $newLvl]);

   
        
        return [
            "state" => "ok",
            "GuildData" => LGuild::getGuildData($GuildMember[0]["id_guild"])
        ];
    
    }
    
    function searchGuild()
    {
        
        $GuildName = validatePlayerWord($_GET["GuildName"]);
        
        return 
            selectFromTable(
                "guild.name AS GuildName, player.name AS LeaderName,"
                . " guild.mem_num AS memberNum, guild.id_guild AS idGuild,"
                . " guild.id_leader AS idPlayer, guild.slog_top, guild.slog_cnt,"
                . " guild.slog_btm", "guild JOIN player ON guild.id_leader = player.id_player",
                "guild.name LIKE :gn", ["gn" => "%$GuildName%"]);
        
    }
}
