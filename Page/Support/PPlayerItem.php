<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';

DbConnect(4);
//queryExe("INSERT IGNORE INTO player_item(id_player, id_item) SELECT  player.id_player, item.id_item FROM player JOIN item");
$Players = queryExe("SELECT m.* FROM player_item m JOIN(SELECT id_player, SUM(amount) FROM player_item WHERE 1 GROUP BY id_player HAVING SUM(amount) <= 2) x ON x.id_player = m.id_player GROUP BY id_player");

foreach ($Players["Rows"] as $onePlayer) {
    $playerItem = [];
    $matrial_acce = selectFromTable("*", "matrial_acce", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_box = selectFromTable("*", "matrial_box", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_box_plus = selectFromTable("*", "matrial_box_plus", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_flags = selectFromTable("*", "matrial_flags", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_luxur = selectFromTable("*", "matrial_luxury", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_main = selectFromTable("*", "matrial_main", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];
    $matrial_product = selectFromTable("*", "matrial_product", "id_player = :idp", ["idp" => $onePlayer["id_player"]])[0];

    $AllMat = (array_merge($matrial_acce, $matrial_box, $matrial_box_plus, $matrial_flags, $matrial_luxur, $matrial_main, $matrial_main, $matrial_product));


    foreach ($AllMat as $key => $oneMat) {
        if ($key == "id_player") continue;
        if ($oneMat == 0) continue;
        //echo "UPDATE player_item SET amount = $oneMat WHERE id_player = {$onePlayer["id_player"]} AND id_item = '$key' LIMIT 1;\n";
        updateTable("amount = :a", "player_item", "id_player = :idp AND id_item = :i", ["a" => $oneMat, "idp" => $onePlayer["id_player"], "i" => $key]);
    }
    echo "Player In server 1 with id {$onePlayer["id_player"]}\n";
}




