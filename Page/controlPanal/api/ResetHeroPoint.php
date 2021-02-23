<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../../base.php';
require_once '../../../config.php';
require_once '../../../configHome.php';
require_once './ATransferPlayer.php';

DbConnect(2);


$Heros = selectFromTable("*", "hero", "point_a + point_b + point_c + points > 1025 AND id_player != 3603");
echo count($Heros);

exit();
foreach ($Heros as $oneHero)
{
    

    $points = ($oneHero["lvl"])*3 + floor(($oneHero["lvl"] - $oneHero["b_lvl"])/10)*$oneHero["ultra_p"];
    updateTable("points = :po , point_a = p_b_a , point_b = p_b_b  , point_c = p_b_c", "hero", "id_player = :idp AND id_hero = :idh  AND in_city = 1", ["po"=>$points,"idh"=>$oneHero["id_hero"], "idp"=>$oneHero["id_player"]]);
    
    
}