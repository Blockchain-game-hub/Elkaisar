<?php

class AGuildInvReq
{
    
    function sendGuildJoinInv()
    {
        global $idPlayer;
        $GuildMember      = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $idPlayerToInvite = validateID($_POST["idPlayerToInvite"]);
        $playerToInvGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayerToInvite]);
        
        if(count($playerToInvGuild))
            return ["state" => "error_0"];
        if(!count($GuildMember))
            return ["state" => "error_1"];
        if($GuildMember[0]["rank"] < GUILD_R_SUPERVISOR)
            return ["state" => "error_2"];
        
        insertIntoTable(
                "id_guild = :idg, id_player = :idp, inv_by = :ib, time_stamp = :ts",
                "guild_inv", 
                ["idg" =>$GuildMember[0]["id_guild"], "idp" => $idPlayerToInvite, "ib"=> $idPlayer, "ts" => time()]);
        
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinInvSent",
            "data" => [
                "idGuild" => $GuildMember[0]["id_guild"],
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $GuildMember[0]["id_guild"]]), "id_player")
                ]]));
        
        return [
            "state" => "ok",
            "GuildReqInvList" => LGuild::getGuildReqInv($GuildMember[0]["id_guild"])
        ];
    
    }
    
    function rejectGuildInv()
    {
        global $idPlayer;
        $idGuild = validateID($_POST["idGuild"]);
        if(!deleteTable("guild_inv", "id_player = :idp AND id_guild = :idg", ["idp" => $idPlayer, "idg" => $idGuild]))
            return ["state" => "error_0", "TryToHack" => TryToHack ()];
        
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinInvRejected",
            "data" => [
                "idGuild" => $idGuild,
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $idGuild]), "id_player")
                ]]));
        $GuildInvReq = LPlayer::PlayerGuildInvReq();
        $GuildInvReq["state"] = "ok";
        return $GuildInvReq;
    }
    
    function acceptGuildInv()
    {
        
        global $idPlayer;
        $idGuild = validateID($_POST["idGuild"]);
        $inv = selectFromTable("id_guild", "guild_inv", "id_player = :idp AND id_guild = :idg", ["idp" => $idPlayer, "idg" => $idGuild]);
        $playerGuild = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if(!count($inv))
            return ["state" => "error_0" , "TryToHack" => TryToHack()];
        if(count($playerGuild))
            return ["state" => "error_1" , "TryToHack" => TryToHack()];
        
        LGuild::addPlayer($inv[0]["id_guild"], $idPlayer);
        
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinInvAccepted",
            "data" => [
                "idGuild" => $idGuild,
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $idGuild]), "id_player")
                ]]));
        
        return [
            "state"       => "ok",
            "PlayerGuild" => LGuild::getPlayerGuildData($idPlayer),
            "GuildData"   => LGuild::getGuildData($idGuild)
        ];
    
    }

    function cancelGuildInv()
    {
        global $idPlayer;
        $GuildMember      = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $idPlayerToInvite = validateID($_POST["idPlayerToInvite"]);
        
        
        if(!count($GuildMember))
            return ["state" => "error_0"];
        if($GuildMember[0]["rank"] < GUILD_R_SUPERVISOR)
            return ["state" => "error_1"];
        
        deleteTable("guild_inv", "id_player = :idp AND id_guild = :idg", ["idp" => $idPlayerToInvite, "idg" => $GuildMember[0]["id_guild"]]);
        
        (new LWebSocket())->send([
            "url" => "Guild/JoinReqCanceled",
            "data" => [
                "idGuild" => $GuildMember[0]["id_guild"],
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $GuildMember[0]["id_guild"]]), "id_player")
                ]]);
        return [
            "state" => "ok",
            "GuildReqInvList" => LGuild::getGuildReqInv($GuildMember[0]["id_guild"])
        ];
    
    }

    function sendGuildRequest()
    {
        global $idPlayer;
        $idGuild  = validateID($_POST["idGuild"]);
        $GuildMember = selectFromTable("COUNT(*) AS c", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        
        if($GuildMember[0]["c"] > 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        deleteTable("guild_req", "id_player = :idp", ["idp" => $idPlayer]);
        insertIntoTable("id_player = :idp, id_guild = :idg, time_stamp = :ts", "guild_req",["idp" => $idPlayer, "idg" => $idGuild, "ts" => time()]);
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinReqSent",
            "data" => [
                "idGuild" => $idGuild,
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $idGuild]), "id_player")
                ]]));
        $GuildInvReq = LPlayer::PlayerGuildInvReq();
        $GuildInvReq["state"] = "ok";
        return $GuildInvReq;
    }
    
    function cancelGuildRequest()
    {
        
        global $idPlayer;
        $idGuild = validateID($_POST["idGuild"]);
        deleteTable("guild_req", "id_player = :idp", ["idp" => $idPlayer]);
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinReqCanceled",
            "data" => [
                "idGuild" => $idGuild,
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $idGuild]), "id_player")
                ]]));
        
        $GuildInvReq = LPlayer::PlayerGuildInvReq();
        $GuildInvReq["state"] = "ok";
        return $GuildInvReq;
    }
    
    function acceptGuildReq()
    {
        global $idPlayer;
        $GuildMember      = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $idPlayerToAccept = validateID($_POST["idPlayerToAccept"]);
        $playerToJoinGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayerToAccept]);
        
        if(count($playerToJoinGuild))
            return ["state" => "error_0"];
        if(!count($GuildMember))
            return ["state" => "error_1"];
        if($GuildMember[0]["rank"] < GUILD_R_SUPERVISOR)
            return ["state" => "error_2"];
        
        LGuild::addPlayer($GuildMember[0]["id_guild"], $idPlayerToAccept);
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinReqAccepted",
            "data" => [
                "idGuild" => $GuildMember[0]["id_guild"],
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $GuildMember[0]["id_guild"]]), "id_player")
                ]]));
        
        return [
            "state" => "ok",
            "GuildReqInvList" => LGuild::getGuildReqInv($GuildMember[0]["id_guild"])
        ];
        
    }
    
    function rejectGuildJoinReq()
    {
        
        global $idPlayer;
        $GuildMember      = selectFromTable("*", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $idPlayerToAccept = validateID($_POST["idPlayerToAccept"]);
        $playerToJoinGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayerToAccept]);
        
        if(count($playerToJoinGuild))
            return ["state" => "error_0"];
        if(!count($GuildMember))
            return ["state" => "error_1"];
        if($GuildMember[0]["rank"] < GUILD_R_SUPERVISOR)
            return ["state" => "error_2"];
        
        deleteTable("guild_req", "id_player = :idp AND id_guild = :idg", ["idp" => $idPlayerToAccept, "idg" => $GuildMember[0]["id_guild"]]);    
        
        (new LWebSocket())->send(json_encode([
            "url" => "Guild/JoinReqRejected",
            "data" => [
                "idGuild" => $GuildMember[0]["id_guild"],
                "Players" => array_column(selectFromTable("id_player", "player", "id_guild = :idg AND online = 1", ["idg" => $GuildMember[0]["id_guild"]]), "id_player")
                ]]));
        return [
            "state" => "ok",
            "GuildReqInvList" => LGuild::getGuildReqInv($GuildMember[0]["id_guild"])
        ];
        
        
    }
    
    
    
    
}
