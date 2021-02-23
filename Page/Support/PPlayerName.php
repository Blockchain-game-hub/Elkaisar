<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
DbConnect(2);

$Players = selectFromTable("*", "player", "1 ORDER BY id_player DESC");

foreach ($Players as $onePlayer){
    
    updateTable("name = :n", "player", "id_player = :idp", ["n" => html_entity_decode($onePlayer["name"]), "idp" => $onePlayer["id_player"]]);
    echo html_entity_decode($onePlayer["name"])."\n";
}

$Guildds = selectFromTable("*", "guild", "1");
foreach ($Guildds as $oneGuild){
    updateTable("name = :n", "guild", "id_guild = :idp", ["n" => html_entity_decode($oneGuild["name"]), "idp" => $oneGuild["id_guild"]]);
    echo html_entity_decode($oneGuild["name"])."\n";
}