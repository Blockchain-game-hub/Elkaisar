<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../../base.php';
require_once '../../../config.php';
require_once '../../../configHome.php';
require_once './ATransferPlayer.php';

$Trans = new ATransferPlayer();

$ServerFrom = 3;
$ServerTo   = 2;


$_SESSION["UserGroup"] = USER_G_SUB_ADMIN;
$_POST["idServerFrom"] = $ServerFrom;
$_POST["idServerTo"]   = $ServerTo;

DbConnect($ServerTo);
queryExe("SET foreign_key_checks = 0");
echo "Find Host Guilds \n";

$Guilds = selectFromTable("id_leader, lvl", "guild", "1");

echo "found ". count($Guilds)." Guild On Host Server";

foreach ($Guilds as $oneGuild)
{
    if($oneGuild["lvl"] <= 10)
        LItem::addItem("union_slogan", 1, $oneGuild["id_leader"]);
    if($oneGuild["lvl"] <= 20)
        LItem::addItem("union_declar", 1, $oneGuild["id_leader"]);
    if($oneGuild["lvl"] <= 30)
        LItem::addItem("union_era", 1, $oneGuild["id_leader"]);
    
    echo "guild matrial Sent TO player".$oneGuild["id_leader"]."\n";
}



echo "Start Removing Guilds\n";

echo "Delete Guild Inv ".deleteTable("guild_inv"      , "1")."\n";
echo "Delete Guild Mem ".deleteTable("guild_member"   , "1")."\n";
echo "Delete Guild Rel ".deleteTable("guild_relation" , "1")."\n";
echo "Delete Guild Req ".deleteTable("guild_req"      , "1")."\n";
echo "Delete Guild BSE ".deleteTable("guild"          , "1")."\n";
echo "Delete World Gar ".deleteTable("world_unit_garrison", "1")."\n";


$Cities = selectFromTable("id_city, x, y, id_player, lvl", "city", "1");

foreach ($Cities as $oneCity)
{
    
    updateTable("t = 0, ut = 0, l = 0", "world", "x = :x AND y = :y", ["x" => $oneCity["x"], "y" => $oneCity["y"]]);
    $EmputyUnit = selectFromTable("x, y", "world", "ut = 0 ORDER BY RAND() LIMIT 1");
    updateTable("t = :t, ut = :ut", "world", "x = :x AND y = :y", ["t" => 17 + $oneCity["lvl"], "ut" => 60 + $oneCity["lvl"], "x" => $EmputyUnit[0]["x"], "y" => $EmputyUnit[0]["y"]]);
    
    echo "City {$oneCity["id_city"]} Removed From World For Player {$oneCity["id_player"]}\n";
}



DbConnect($ServerFrom);
queryExe("SET foreign_key_checks = 0");

echo "Find Host Guilds \n";

$Guilds = selectFromTable("id_leader, lvl", "guild", "1");

echo "found ". count($Guilds)." Guild On Host Server";

foreach ($Guilds as $oneGuild)
{
    if($oneGuild["lvl"] <= 10)
        LItem::addItem("union_slogan", 1, $oneGuild["id_leader"]);
    if($oneGuild["lvl"] <= 20)
        LItem::addItem("union_declar", 1, $oneGuild["id_leader"]);
    if($oneGuild["lvl"] <= 30)
        LItem::addItem("union_era", 1, $oneGuild["id_leader"]);
    
    echo "guild matrial Sent TO player".$oneGuild["id_leader"]."\n";
}



echo "Start Removing Guilds\n";

echo "Delete Guild Inv ".deleteTable("guild_inv"      , "1")."\n";
echo "Delete Guild Mem ".deleteTable("guild_member"   , "1")."\n";
echo "Delete Guild Rel ".deleteTable("guild_relation" , "1")."\n";
echo "Delete Guild Req ".deleteTable("guild_req"      , "1")."\n";
echo "Delete Guild BSE ".deleteTable("guild"          , "1")."\n";
echo "Delete World Gar ".deleteTable("world_unit_garrison", "1")."\n";


DbConnect($ServerFrom);
$PlayerIds = selectFromTable("id_player", "player", "1");

$PlayerCount = 0;

foreach ($PlayerIds as $oneId)
{
    
    $_POST["idPlayer"] = $oneId["id_player"];
    echo "Player Start Trans {$oneId["id_player"]}\n";
    print_r($Trans->startTrans());
    
    echo "Player TransPorted {$oneId["id_player"]} With Total Count $PlayerCount\n";
    $PlayerCount++;
}
