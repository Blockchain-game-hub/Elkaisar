<?php

class AGuildMember
{
    
    function getGuildMember()
    {
        
        global $idPlayer;
        $offset = validateID($_GET["offset"]);
        $idGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($idGuild))
            return [];
        
        
        return LGuild::getGuildMember($idGuild[0]["id_guild"], $offset);
    
    }
    
    function removeFromPosition()
    {
        
        global $idPlayer;
        $idMember    = validateID($_POST["idMember"]);
        $offset      = validateID($_POST["offset"]);
        $GuildMember = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $MemberRank  = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idMember]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($MemberRank))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_guild"] != $MemberRank[0]["id_guild"])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] <= $MemberRank[0]["rank"])
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("rank = 0", "guild_member", "id_player = :idp AND id_guild = :idg", ["idp" => $idMember , "idg" => $GuildMember[0]["id_guild"]]);
        
        return [
            "state" => "ok",
            "memberList" => LGuild::getGuildMember($GuildMember[0]["id_guild"], $offset)
        ];
        
    }
    
    function promotMember()
    {
        
        global $idPlayer;
        $idMember    = validateID($_POST["idMember"]);
        $offset      = validateID($_POST["offset"]);
        $promotTo    = validateID($_POST["newRank"]);
        $GuildMember = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $MemberRank  = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idMember]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($MemberRank))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_guild"] != $MemberRank[0]["id_guild"])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] <= $MemberRank[0]["rank"] || $MemberRank[0]["rank"] > GUILD_R_DEPUTY)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        if($promotTo > GUILD_R_DEPUTY)
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        if(!in_array($promotTo, array_keys(LGuild::$G_POSITION_MAX_NUM)))
            return ["state" => "error_5", "TryToHack" => TryToHack()];
        
        $posCount = selectFromTable("COUNT(*) AS poSC", "guild_member", "id_guild = :idg AND rank = :r ", ["idg"=>$GuildMember[0]["id_guild"], "r"=>$promotTo])[0]["poSC"];
        if($posCount >= LGuild::$G_POSITION_MAX_NUM[$promotTo])
            return ["state" => "error_6", "TryToHack" => TryToHack()];
         
        updateTable("rank = :r", "guild_member", "id_player = :idp AND id_guild = :idg", ["r"=>$promotTo, "idp"=>$idMember, "idg"=>$GuildMember[0]["id_guild"]]);
        
        return [
            "state" => "ok",
            "memberList" => LGuild::getGuildMember($GuildMember[0]["id_guild"], $offset)
        ];
    }
    
    function tradePosition()
    {
        
        global $idPlayer;
        $idMember    = validateID($_POST["idMember"]);
        $offset      = validateID($_POST["offset"]);
        $GuildMember = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $MemberRank  = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idMember]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($MemberRank))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_guild"] != $MemberRank[0]["id_guild"])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] <= $MemberRank[0]["rank"])
            return ["state" => "error_3"];
        
        updateTable("rank = :r", "guild_member", "id_player = :idp AND id_guild = :idg", ["idp" => $idMember , "idg" => $GuildMember[0]["id_guild"], "r" => $GuildMember[0]["rank"]]);
        updateTable("rank = :r", "guild_member", "id_player = :idp AND id_guild = :idg", ["idp" => $idPlayer , "idg" => $GuildMember[0]["id_guild"], "r" => $MemberRank[0]["rank"]]);
        
        return [
            "state" => "ok",
            "memberList" => LGuild::getGuildMember($GuildMember[0]["id_guild"], $offset)
        ];
        
    }
    
    function modifyPrizeShare()
    {
        
        global $idPlayer;
        $idMember      = validateID($_POST["idMember"]);
        $offset        = validateID($_POST["offset"]);
        $newPrizeShare = validateID($_POST["newPrizeShare"]);
        $GuildMember   = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $MemberRank    = selectFromTable("id_guild, rank, prize_share", "guild_member", "id_player = :idp", ["idp" => $idMember]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($MemberRank))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_guild"] != $MemberRank[0]["id_guild"])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] < GUILD_R_LEADER)
            return ["state" => "error_3"];
        if($newPrizeShare < 0)
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        
        
        $totalPrizeShare = selectFromTable("SUM(prize_share) AS total_prize_share ", "guild_member", "id_guild = :idg", ["idg"=>$GuildMember[0]["id_guild"]])[0]["total_prize_share"];
        if(100 < $totalPrizeShare + $newPrizeShare - $MemberRank[0]["prize_share"])
            return ["state" => "error_5"];
        
        updateTable("prize_share = :sp", "guild_member", "id_player = :idp AND id_guild = :idg", ["idp" => $idMember , "idg" => $GuildMember[0]["id_guild"], "sp" => $newPrizeShare]);
        
        return [
            "state" => "ok",
            "memberList" => LGuild::getGuildMember($GuildMember[0]["id_guild"], $offset)
        ];
        
    }
    
    
    function fireMember()
    {
        global $idPlayer;
        $idMember      = validateID($_POST["idMember"]);
        $offset        = validateID($_POST["offset"]);
        $GuildMember   = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $MemberRank    = selectFromTable("id_guild, rank", "guild_member", "id_player = :idp", ["idp" => $idMember]);
        
        if(!count($GuildMember))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($MemberRank))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($GuildMember[0]["id_guild"] != $MemberRank[0]["id_guild"])
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($GuildMember[0]["rank"] < GUILD_R_DEPUTY_2)
            return ["state" => "error_3"];
        if($MemberRank[0]["rank"] > GUILD_R_MEMBER)
            return ["state" => "error_4"];
        
        
        
        deleteTable("guild_member", "id_player = :idm AND id_guild = :idg  AND rank = 0", ["idm"=>$idMember, "idg"=>$GuildMember[0]["id_guild"]]);
        updateTable("guild = NULL , id_guild = NULL", "player", "id_player = :idp", ["idp" => $idMember]);
        
        return [
            "state" => "ok",
            "memberList" => LGuild::getGuildMember($GuildMember[0]["id_guild"], $offset)
        ];
        
    }
    
    function getOnlineMember()
    {
        global $idPlayer;
        $idGuild = selectFromTable("id_guild", "player", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($idGuild))
            return [];
        if(!$idGuild[0]["id_guild"])
            return [];
        
        return selectFromTable("id_player", "player", "id_guild = :idg", ["idg" => $idGuild[0]["id_guild"]]);
    }
    
    function searchGuildMamber()
    {
        
        $GuildMembareName = validatePlayerWord($_GET["GuildMemberName"]);
        $idGuild          = validateID($_GET["idGuild"]);
        
        return 
            selectFromTable(
                "player.name AS PlayerName, guild.name AS GuildName, player.id_player AS idPlayer, "
                . "guild.id_guild AS idGuild, guild.slog_top, guild.slog_cnt, guild.slog_btm", 
                "player LEFT JOIN guild ON player.id_guild = guild.id_guild",
                "player.id_guild = :idg AND player.name LIKE :n",
                ["idg" => $idGuild, "n" => "%$GuildMembareName%"]);
        
    }
}

