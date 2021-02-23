<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
require_once '../../lib/LConfig.php';


DbConnect(2);

$Heros = selectFromTable("*", "hero", "p_b_a + p_b_b + p_b_c > 60");
foreach ($Heros as $one){
    $HeroPoint = LHero::pointsForLvl($one["b_lvl"]);
    updateTable("p_b_a = :p1, p_b_b = :p2, p_b_c = :p3", "hero", "id_hero = :idh", ["p1" => $HeroPoint["pba"], "p2" => $HeroPoint["pbb"], "p3" => $HeroPoint["pbc"], "idh" => $one["id_hero"]]);
}

DbConnect(4);

$Heros = selectFromTable("*", "hero", "p_b_a + p_b_b + p_b_c > 60");
foreach ($Heros as $one){
    $HeroPoint = LHero::pointsForLvl($one["b_lvl"]);
    updateTable("p_b_a = :p1, p_b_b = :p2, p_b_c = :p3", "hero", "id_hero = :idh", ["p1" => $HeroPoint["pba"], "p2" => $HeroPoint["pbb"], "p3" => $HeroPoint["pbc"], "idh" => $one["id_hero"]]);
}
