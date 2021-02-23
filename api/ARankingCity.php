<?php

class ARankingCity
{
    
    function generalRank()
    {
         $offset = validateID($_GET["offset"]);
    
        return selectFromTable(
                " city.name , city.id_city , city.pop , city.lvl , "
                . " player.guild  AS GuildName, player.avatar, player.name AS lord_name, player.porm,"
                . " guild.slog_top, guild.slog_cnt, guild.slog_btm, guild.id_guild",
                "city JOIN player ON  player.id_player = city.id_player "
                . " LEFT JOIN guild ON guild.id_guild = player.id_guild",
                "1 ORDER BY pop DESC LIMIT 10 OFFSET $offset");
    }
    
}
