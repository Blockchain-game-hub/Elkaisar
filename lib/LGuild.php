<?php

class LGuild {
    
    static public $G_POSITION_MAX_NUM =  array(

            0=>300,// عضو عادى
            1=>10,  //  عضو رسمى
            2=>6,   //  عضو كبير
            3=>4,   //  مستشار
            4=>3,  //  وزير
            5=>2,  //  نائب المدير
            6=>1   //  المدير

        );  

    static function inSameGuild($idPlayer2) {
        global $idPlayer;
        $idGuild1 = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        if (!count($idGuild1)) return false;
        $idGuild2 = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer2]);
        if (!count($idGuild2)) return false;
        if($idGuild1[0]["id_guild"] !=  $idGuild2[0]["id_guild"]) return false;
        return true;
    }
    
    static function canDefenceGuildWar($Battel)
    {
        global $idPlayer;
        
        if(LWorldUnit::isRepelCastle($Battel["ut"]) || LWorldUnit::isQueenCity($Battel["ut"]))
        {
            $PlayerGuild = selectFromTable("id_guild", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
            if(!count($PlayerGuild))
                return false;
            
            $GuildDominant = selectFromTable("*", "world_unit_rank", "x = :x AND y = :y ORDER BY id_round DESC LIMIT 1", ["x" => $Battel["x"], "y" => $Battel["y"]]);
            
            if(!count($GuildDominant))
                return false;
            return $GuildDominant[0]["id_guild"] == $PlayerGuild[0]["id_guild"];
        }
        
        return true;
    }
    
    static function addPlayer($idGuild, $idPlayer, $Post = GUILD_R_MEMBER)
    {
        deleteTable("guild_inv", "id_player = :idp", ["idp" => $idPlayer]);
        deleteTable("guild_req", "id_player = :idp", ["idp" => $idPlayer]);
        insertIntoTable("id_player = :idp , id_guild = :idg  , rank = :p , time_join = :t", "guild_member", ["idp" => $idPlayer, "idg" => $idGuild, "p" => $Post, "t" => time()]);
        updateTable("id_guild = :idg , guild = (SELECT name FROM guild WHERE id_guild = :idgp)", "player", "id_player = :idp", ["idg" => $idGuild, "idgp" => $idGuild, "idp" => $idPlayer]);
    }
    
    static function getGuildData($idGuild)
    {
   
        $Guild = selectFromTable("*", "guild", "id_guild = :idg", ["idg"=>$idGuild]);


        if(!count($Guild))
            return false;
        
        $GuildData = [];
        
        $GuildData["GuildData"]  = $Guild[0];
        $GuildData["leaderName"] = LPlayer::getName($Guild[0]["id_leader"]);
        $GuildData["Allay"]      = selectFromTable("guild.id_guild AS idGuild, guild.name , guild_relation.state", "guild JOIN guild_relation ON guild.id_guild = guild_relation.id_guild_2", "guild_relation.id_guild_1 = :idg", ["idg"=>$idGuild]);
        $GuildData["prizeShare"] = selectFromTable("SUM(prize_share) AS total_prize_share ", "guild_member", "id_guild = :idg", ["idg"=>$idGuild])[0]["total_prize_share"];

        return $GuildData;
    }
    
    static function getPlayerGuildData($idPlayer)
    {
        
        $playerGuild =  
            selectFromTable(
                    "guild_member.* , guild.name",
                    "guild_member JOIN guild  ON guild.id_guild = guild_member.id_guild",
                    "guild_member.id_player = :idp", ["idp" => $idPlayer]);
        if(count($playerGuild))
            return $playerGuild[0];
        
        return null;
        
    }
    
    static function getGuildReqInv($idGuild)
    {
        
        return [
            "GuildReq" => selectFromTable("guild_req.*, player.name, player.porm, player.avatar", "guild_req JOIN player ON player.id_player = guild_req.id_player", "guild_req.id_guild = :idg", ["idg" => $idGuild]),
            "GuildInv" => selectFromTable("guild_inv.*, player.name, player.porm, player.avatar", "guild_inv JOIN player ON player.id_player = guild_inv.id_player", "guild_inv.id_guild = :idg", ["idg" => $idGuild])
        ];
        
    }
    
    static function updateGuildData($idGuild){
        
        updateTable(
                " mem_num = (SELECT COUNT(*) FROM guild_member WHERE id_guild = :idg) ,"
                . " prestige = (SELECT SUM(player.prestige) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = :idg1) ,"
                . " honor = (SELECT SUM(player.honor) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = :idg2)"
                , "guild", "id_guild = :idg3", ["idg" => $idGuild, "idg1" => $idGuild, "idg2" => $idGuild, "idg3" => $idGuild]);
    }
    
    static function getGuildMember($idGuild, $offset)
    {
        return selectFromTable(
            "player.name , guild_member.rank , guild_member.prize_share ,player.prestige , player.id_player , player.`online`, player.last_seen, player.porm",
            "player JOIN  guild_member ON player.id_player = guild_member.id_player",
            "guild_member.id_guild = :idg ORDER BY guild_member.rank DESC, player.prestige DESC LIMIT 15 OFFSET $offset", ["idg" => $idGuild]);
        
    }
}
