<?php

class APlayer
{
    
    function getPlayerData()
    {
        global $idPlayer;


        $PlayerData = LPlayer::getData();
        
        $guildData = LGuild::getPlayerGuildData($idPlayer);
        
        if($guildData){
            $PlayerData["guildData"] = $guildData;
        }
        
        $PlayerData ["playerState"] = selectFromTable("*", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0];
    
        return $PlayerData;
        
    }
    
    
    function changePlayerName()
    {
        
        global $idPlayer;
        $NewName = validatePlayerWord($_POST["NewName"]);
        $playerWithSameName = selectFromTable("id_player", "player", "name = :nn", ["nn" => $NewName]);
        if(mb_strlen($NewName) < 3)
            return ["state" => "error_0"];
        if(mb_strlen($NewName) > 10)
            return ["state" => "error_1"];
        if(count($playerWithSameName) > 0)
            return ["state" => "error_2"];
        if(!LItem::useItem("change_name"))
            return ["state" => "error_3"];
        
        updateTable("name = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $NewName]);
        return [
            "state" => "ok",
            "Player" => LPlayer::getData()
        ];
    }


    function getPlayerState()
    {
        
        global $idPlayer;
        return selectFromTable("*", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0];
    
    }
    function getPlayerGuildData()
    {
        global $idPlayer;
        
        return [
            "GuildData" => LGuild::getPlayerGuildData($idPlayer)
        ];
    }
    
    function getPlayerGuildReqInv()
    {
        return LPlayer::PlayerGuildInvReq();
    }
    
    
    function getPlayerHeros()
    {
        
        global $idPlayer;
        
        return selectFromTable("*", "hero", "id_player = :idp ORDER BY id_city, ord", ["idp" => $idPlayer]);
    
    }
    
    function offline()
    {
        global $idPlayer;
        $idLog = validateID($_POST["idLog"]);
        updateTable("`online` = 0  , last_seen = :n ", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => time()]);
        updateTable("time_leave = CURRENT_TIME ", "player_logs", "id_log = :id", ["id" => $idLog]);
        
    }
    
    function online()
    {
        
        global $idPlayer;
        $ip         = validatePlayerWord($_POST["ip"]);
        updateTable("`online` = 1", "player", "id_player = :idp", ["idp" => $idPlayer]);
        $idLog = insertIntoTable("id_player = :idp, ipv4 = :ip", "player_logs", ["idp" => $idPlayer, "ip" => $ip]);
        $q = "title_1, title_2, title_3, title_4, "
                . "title_5, title_6, title_7, title_8, "
                . "title_9, title_10";
        LPlayer::OnPlayerLogged();
        $Player = selectFromTable("*", "player",  "id_player = :idp", ["idp" => $idPlayer])[0];
        $Player["p_token"] = selectFromTable("auth_token", "player_auth", "id_player = :idp", ["idp" => $idPlayer])[0]["auth_token"];
        
        return ([
            "title"    => array_filter(selectFromTable($q, "player_title", "id_player = :idp", ["idp" => $idPlayer])[0]),
            "player"   => $Player,
            "isBadIp"  => selectFromTable("COUNT(*) as c", "panned_ip", "ipv4 = :ip", ["ip" => $ip])[0]["c"],
            "idLog"    => $idLog,
            "idPlayer" => $idPlayer
        ]);

    }
    
    
    function getNotif()
    {
        
        global $idPlayer;
        
        $Server = selectFromTable("*", "server_data", "1");
        
        return [
            "incomeMailCount"     => selectFromTable("COUNT(*) AS c", "msg_income",    "id_to     = :idp AND seen = 0", ["idp" => $idPlayer])[0]["c"],
            "totalIncomMailCount" => selectFromTable("COUNT(*) AS c", "msg_income",    "id_to     = :idp",              ["idp" => $idPlayer])[0]["c"],
            "miscMailCount"       => selectFromTable("COUNT(*) AS c", "msg_diff",      "id_to     = :idp AND seen = 0", ["idp" => $idPlayer])[0]["c"],
            "totalMiscsMailCount" => selectFromTable("COUNT(*) AS c", "msg_diff",      "id_to     = :idp AND seen = 0", ["idp" => $idPlayer])[0]["c"],
            "reportMailCount"     => selectFromTable("COUNT(*) AS c", "report_player", "id_player = :idp AND seen = 0", ["idp" => $idPlayer])[0]["c"],
            "totalReportCount"    => selectFromTable("COUNT(*) AS c", "report_player", "id_player = :idp",              ["idp" => $idPlayer])[0]["c"],
            "spyMailCount"        => selectFromTable("COUNT(*) AS c", "spy_report",    "id_player = :idp AND seen = 0", ["idp" => $idPlayer])[0]["c"],
            "totalSpyReportCount" => selectFromTable("COUNT(*) AS c", "spy_report",    "id_player = :idp",              ["idp" => $idPlayer])[0]["c"],
            
            "totalPlayerCount"    => $Server[0]["player_num"],
            "totalGuildCount"     => $Server[0]["guild_num"],
            "totalCityCount"      => $Server[0]["city_num"],
            "totalHeroCount"      => $Server[0]["hero_num"]
            
        ];
    
    }
    
    
    function refreshPlayerData()
    {
        global $idPlayer;
        
        $Player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $idPlayer]);
        unset($Player[0]["p_token"]);
        
        return $Player[0];
    
    }
    
    
    function searchPlayer()
    {
        
        $GuildMembareName = validatePlayerWord($_GET["PlayerName"]);
        
        return 
            selectFromTable(
                "player.name AS PlayerName, guild.name AS GuildName, player.id_player AS idPlayer, "
                . "guild.id_guild AS idGuild, guild.slog_top, guild.slog_cnt, guild.slog_btm", 
                "player LEFT JOIN guild ON player.id_guild = guild.id_guild","player.name LIKE :n",
                ["n" => "%$GuildMembareName%"]);
        
    }
    
    function viewProfile()
    {
        
        $idPlayer = validateID($_GET["idPlayer"]);
        
        $Player = selectFromTable(
                "name AS PlayerName, porm AS nobleRank, prestige, honor, id_player AS idPlayer, avatar, id_guild", "player", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($Player))
            return [];
       $City = selectFromTable("COUNT(*) AS c, SUM(pop_max) AS s", "city", "id_player = :idp", ["idp" => $idPlayer])[0];
       $Player[0]["popMax"]    = $City["s"];
       $Player[0]["cityCount"] = $City["c"];
       
        if($Player[0]["id_guild"])
            return $Player[0];
        $Guild = selectFromTable("name, id_guild, slog_top, slog_cnt, slog_btm", "guild", "id_guild = :idg", ["idg" => $Player[0]["id_guild"]]);
       
        if(!count($Guild))
            return $Player[0];
        $Player[0]["idGuild"]   = $Guild[0]["id_guild"];
        $Player[0]["GuildName"] = $Guild[0]["name"];
        $Player[0]["slog_top"]  = $Guild[0]["slog_top"];
        $Player[0]["slog_cnt"]  = $Guild[0]["slog_cnt"];
        $Player[0]["slog_btm"]  = $Guild[0]["slog_btm"];
        return $Player[0];
    }
    
    
    function playLuckWheel()
    {
        if(!LItem::useItem("luck_play"))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $Prize = selectFromTable("*", "luck_wheel_prize", "1 ORDER BY RAND() LIMIT 20");
        
        $PlayerLuck = rand(1, 100);
        $index = 0;
        foreach ($Prize as $one)
        {
            
            if($one["luck"] >= $PlayerLuck)
                break;
            $index++;
        }
        
        LItem::addItem($Prize[$index]["Prize"], $Prize[$index]["amount"]);
        
        return [
            "state" => "ok",
            "Prize" => $Prize,
            "winIndex" => $index
        ];
    }
    
    
    function chatPann()
    {
        
        global $idPlayer;
        $idPlayerTOPan = validateID($_POST["playerToPan"]);
        $duration  = validateID($_POST["duration"]);
        
        $SuperVisor   = selectFromTable("id_player, user_group, name", "player", "id_player = :idp", ["idp" => $idPlayer]);
        $PlayerTOPann = selectFromTable("id_player, user_group, name", "player", "id_player = :idp", ["idp" => $idPlayerTOPan]);
        
        if(!count($PlayerTOPann))
            return ["state" => "error_0"];
        if(!count($SuperVisor))
            return ["state" => "error_1"];
        if($PlayerTOPann[0]["user_group"] >= $SuperVisor[0]["user_group"])
            return ["state" => "error_2"];
        if($SuperVisor[0]["user_group"] < 3)
            return ["state" => "error_3", "TryToHack"];
        
        
        updateTable("chat_panne = :cp", "player", "id_player = :idp", ["idp" => $idPlayerTOPan, "cp" => time() + $duration]);
        (new LWebSocket())->send(json_encode([
                 "url" => "Ban/worldChat",
                 "data" => [
                     "PannedName" => $PlayerTOPann[0]["name"],
                     "PannerName" => $SuperVisor[0]["name"],
                     "duration"   => $duration,
                     "idPlayerToPan" => $idPlayerTOPan
                     ]]));
        
        return ["state" => "ok"];
    
    }
}
