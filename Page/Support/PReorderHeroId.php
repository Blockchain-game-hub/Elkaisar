<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
DbConnect(2);



$Players = selectFromTable("id_player", "player", "1 ORDER BY id_player DESC");

queryExe("SET foreign_key_checks = 0");

foreach ($Players as $onePlayer) {

    $FirstHeroId = ($onePlayer["id_player"] - 1 ) * 1000 + 1;
    $FirstPlayerHero = selectFromTable("id_hero", "hero", "id_player  = :idp ORDER BY id_hero ASC LIMIT 1", ["idp" => $onePlayer["id_player"]]);
    if (!count($FirstPlayerHero)) continue;
    if ($FirstPlayerHero[0]["id_hero"] == $FirstHeroId)  continue;
    $HeroWithSameId = selectFromTable("*", "hero", "id_hero = :idh", ["idh" => $FirstHeroId]);
    if(count($HeroWithSameId)){
        if($HeroWithSameId[0]["id_player"] != $onePlayer["id_player"])
            echo "Error Id Player {$onePlayer["id_player"]}";
    }
    $idHeroNew = $FirstHeroId;
    
 
    
    updateTable("id_hero         = :ni", "hero", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "battel", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "battel_member", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_army", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_back", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "arena_player_challange_hero", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_deleted", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_equip", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "equip", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_medal", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "hero_theater", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "report_hero", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("id_hero         = :ni", "world_unit_garrison", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    updateTable("console         = :ni", "city", "console = :ido", ["ni" => $idHeroNew, "ido" => $FirstPlayerHero[0]["id_hero"]]);
    echo "Hero Finished $idHeroNew \n ";
}



queryExe("SET foreign_key_checks = 0");
$count = 0;

foreach ($Players as $onePlayer) {
    global $idPlayer;
    $idPlayer = $onePlayer["id_player"];
    $Heros = selectFromTable("id_hero", "hero", "id_player = :idp ORDER BY id_hero DESC", ["idp" => $onePlayer["id_player"]]);
    $index = count($Heros);
    if($index > 0){
        if($Heros[0]["id_hero"] == ($idPlayer - 1)*1000 + $index + 1){
            echo ("Player Is Good $idPlayer\n");
            continue;
        }
           
    }
    
    foreach ($Heros as $oneHero) {

        $idHeroNew = LHero::getNewHeroId();
        if($oneHero["id_hero"] == ($onePlayer["id_player"] - 1 ) * 1000 + 1)
            break;
        
        $index --;

        updateTable("id_hero         = :ni", "hero", "id_hero = :ido AND id_player = :idp", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"], "idp" => $onePlayer["id_player"]]);
        updateTable("id_hero         = :ni", "battel", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "battel_member", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_army", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_back", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "arena_player_challange_hero", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_deleted", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_equip", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_medal", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "hero_theater", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "report_hero", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "world_unit_garrison", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("console         = :ni", "city", "console = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
        updateTable("id_hero         = :ni", "equip", "id_hero = :ido", ["ni" => $idHeroNew, "ido" => $oneHero["id_hero"]]);
    }
    $count++;
    echo "done Player " . $onePlayer["id_player"] . "With total of $count" . "\n";
}


queryExe("SET foreign_key_checks = 1");

echo 'done';

