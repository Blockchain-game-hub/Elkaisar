<?php

class ARankingPlayer
{
    
    function generalRank()
    {
        $offset = validateID($_GET["offset"]);
        
        return selectFromTable(
                "player.name , player.prestige , player.guild ,player.honor, "
                . "player.avatar, player.id_player , player.porm, "
                . "player_title.*, guild.slog_top, guild.slog_cnt, guild.slog_btm, "
                . "guild.lvl AS guildLvl, guild.id_guild",
                "player JOIN player_title ON player.id_player = player_title.id_player LEFT JOIN guild "
                . "ON guild.id_guild = player.id_guild" ,
                "1 ORDER BY  porm DESC , prestige DESC  LIMIT 10 OFFSET $offset"
                );
        
    }
    function honorRank()
    {
        $offset = validateID($_GET["offset"]);
        
        return selectFromTable(
                "player.name , player.prestige , player.guild ,player.honor, "
                . "player.avatar, player.id_player , player.porm, player.avatar, "
                . "player_title.*, guild.slog_top, guild.slog_cnt, guild.slog_btm, "
                . "guild.lvl AS guildLvl, guild.id_guild",
                "player JOIN player_title ON player.id_player = player_title.id_player LEFT JOIN guild "
                . "ON guild.id_guild = player.id_guild" ,
                "1 ORDER BY  prestige DESC  LIMIT 10 OFFSET $offset"
                );
        
    }
    
    function prestigeRank()
    {
        $offset = validateID($_GET["offset"]);
        
        return selectFromTable(
                "player.name , player.prestige , player.guild ,player.honor, "
                . "player.avatar, player.id_player , player.porm, player.avatar, "
                . "player_title.*, guild.slog_top, guild.slog_cnt, guild.slog_btm, "
                . "guild.lvl AS guildLvl, guild.id_guild",
                "player JOIN player_title ON player.id_player = player_title.id_player LEFT JOIN guild "
                . "ON guild.id_guild = player.id_guild" ,
                "1 ORDER BY  honor DESC  LIMIT 10 OFFSET $offset"
                );
        
    }
    
    function searchByName()
    {
        
        $name = validatePlayerWord($_GET["searchName"]);
        
        return selectFromTable(
                "player.name , player.prestige , player.guild ,player.honor, "
                . "player.avatar, player.id_player , player.porm, player.avatar, "
                . "player_title.*, guild.slog_top, guild.slog_cnt, guild.slog_btm, "
                . "guild.lvl AS guildLvl, guild.id_guild",
                "player JOIN player_title ON player.id_player = player_title.id_player LEFT JOIN guild "
                . "ON guild.id_guild = player.id_guild" ,
                " player.name LIKE :n ORDER BY prestige DESC  LIMIT 10", ["n" => "%$name%"]
                );
    }
    
    function searchByRank()
    {
        
        $rank = validateID($_GET["rank"]);
        return selectFromTable(
                "player.name , player.prestige , player.guild ,player.honor, "
                . "player.avatar, player.id_player , player.porm, player.avatar, "
                . "player_title.*, guild.slog_top, guild.slog_cnt, guild.slog_btm, "
                . "guild.lvl AS guildLvl, guild.id_guild",
                "player JOIN player_title ON player.id_player = player_title.id_player LEFT JOIN guild "
                . "ON guild.id_guild = player.id_guild",
                " 1 ORDER BY porm DESC , prestige DESC   LIMIT 1 OFFSET $rank"
                );
    }
    
}

