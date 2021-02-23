<?php

class ARankingGuild
{
    
    function generalRank()
    {
         $offset = validateID($_GET["offset"]);

        return selectFromTable(
                "guild.lvl, guild.name AS GuildName, guild.prestige, guild.honor, guild.id_guild, "
                . "guild.mem_num , player.name AS lord_name, player.avatar, player.porm,"
                . "guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "guild JOIN player ON player.id_player = guild.id_leader",
                "1 ORDER BY guild.mem_num DESC  LIMIT 10 OFFSET $offset");
        
    }
    
    function prestigeRank()
    {
         $offset = validateID($_GET["offset"]);

        return selectFromTable(
                "guild.lvl, guild.name AS GuildName, guild.prestige, guild.honor, guild.id_guild, "
                . "guild.mem_num , player.name AS lord_name, player.avatar, player.porm,"
                . "guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "guild JOIN player ON player.id_player = guild.id_leader",
                "1 ORDER BY guild.prestige DESC  LIMIT 10 OFFSET $offset");
        
    }
    
    function honorRank()
    {
         $offset = validateID($_GET["offset"]);

        return selectFromTable(
                "guild.lvl, guild.name AS GuildName, guild.prestige, guild.honor, guild.id_guild, "
                . "guild.mem_num , player.name AS lord_name, player.avatar, player.porm,"
                . "guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "guild JOIN player ON player.id_player = guild.id_leader",
                "1 ORDER BY guild.honor DESC  LIMIT 10 OFFSET $offset");
        
    }
    
    function searchByName()
    {
        
        $searchName = validateID($_GET["searchName"]);

        return selectFromTable(
                "guild.lvl, guild.name AS GuildName, guild.prestige, guild.honor, guild.id_guild, "
                . "guild.mem_num , player.name AS lord_name, player.avatar, player.porm,"
                . "guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "guild JOIN player ON player.id_player = guild.id_leader",
                "guild.name LIKE :n ORDER BY guild.mem_num DESC  LIMIT 10", ["n" => "%$searchName%"]);
        
    }
    
    function searchByRank()
    {
        
        $rank = validateID($_GET["rank"]);

        return selectFromTable(
                "guild.lvl, guild.name AS GuildName, guild.prestige, guild.honor, guild.id_guild, "
                . "guild.mem_num , player.name AS lord_name, player.avatar, player.porm,"
                . "guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "guild JOIN player ON player.id_player = guild.id_leader",
                "1 ORDER BY guild.mem_num DESC  LIMIT 1 OFFSET $rank");
        
    }
    
}

