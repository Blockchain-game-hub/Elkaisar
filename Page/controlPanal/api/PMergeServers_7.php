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


DbConnect($ServerFrom);
queryExe("SET foreign_key_checks = 0");


DbConnect($ServerFrom);
$PlayerIds = selectFromTable("id_player", "player", "1 LIMIT 100 OFFSET 700");

$PlayerCount = 0;

foreach ($PlayerIds as $oneId)
{
    
    $_POST["idPlayer"] = $oneId["id_player"];
    echo "Player Start Trans {$oneId["id_player"]}\n";
    print_r($Trans->startTrans());
    
    echo "Player TransPorted {$oneId["id_player"]} With Total Count $PlayerCount\n";
    $PlayerCount++;
}
