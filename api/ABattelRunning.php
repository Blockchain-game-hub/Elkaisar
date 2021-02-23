<?php

class ABattelRunning {

    function joinBattel() {

        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $side = validateID($_POST["side"]);
       
        $idBattel = validateID($_POST["idBattel"]);
        $Hero = selectFromTable("id_city, in_city, id_player, id_hero", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        $Battel = selectFromTable("*", "battel JOIN world ON world.x = battel.x_coord AND world.y = battel.y_coord ", "battel.id_battel = :idb", ["idb" => $idBattel]);
        if (!count($Hero))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($Hero[0]["in_city"] != HERO_IN_CITY)
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if (!count($Battel))
                return ["state" => "error_2", "TryToHack" => TryToHack()];

        if (LWorldUnit::limitedHero($Battel[0]["ut"]))
                if ($this->reachedLimitHero($Battel[0], $side))
                    return ["state" => "error_3", "TryToHack" => TryToHack()];
        if (LWorldUnit::isGuildWar($Battel[0]["ut"])) {
            if (!LGuild::inSameGuild($Battel[0]["id_player"]) && $side == BATTEL_SIDE_ATT)
                    return ["state" => "error_5", "TryToHack" => TryToHack()];
            if (!LGuild::canDefenceGuildWar($Battel[0]) && $side == BATTEL_SIDE_DEF)
                    return ["state" => "error_5_1", "TryToHack" => TryToHack()];
        }

        if ($side == BATTEL_SIDE_DEF && !LWorldUnit::isDefencable($Battel[0]["ut"]))
                return ["state" => "error_8"];
        if (!LBattel::takeJoinPrice($Battel[0]["ut"]))
                return ["state" => "error_6", "TryToHack" => TryToHack()];
        if (LBattel::takeHeroPower($idHero, $Battel[0]["ut"]) <= 0)
                return ["state" => "error_7", "TryToHack" => TryToHack()];


        LBattel::join($Battel[0], $Hero[0], $side);
        updateTable("in_city = :inc", "hero", "id_hero = :idh", ["inc" => HERO_IN_BATTEL, "idh" => $idHero]);
        $newBattel = LBattel::getBattelById($idBattel);

        return [
            "state" => "ok",
            "Battel" => $newBattel
        ];
    }

    private function reachedLimitHero($Battel, $side) {
        $countAttack = selectFromTable("COUNT(*) AS joiner", "battel_member", "id_battel = :idb AND side = :si", ["idb" => $Battel["id_battel"], "si" => BATTEL_SIDE_ATT])[0]["joiner"];
        $countDef = selectFromTable("COUNT(*) AS joiner", "battel_member", "id_battel = :idb AND side = :si", ["idb" => $Battel["id_battel"], "si" => BATTEL_SIDE_DEF])[0]["joiner"];
        if ($countAttack >= LBattel::MaxJoinNum($Battel["ut"]) && $side == BATTEL_SIDE_ATT)
                return true;
        else if ($countDef >= LBattel::MaxJoinNum($Battel["ut"]) && $side == BATTEL_SIDE_DEF)
                return true;


        return false;
    }

    function getBattels() {
        global $idPlayer;

        $Battel = queryExe("SELECT DISTINCT battel.*, city.name AS CityName, player.name AS PlayerName FROM `battel`
                                JOIN battel_member ON battel_member.id_battel = battel.id_battel
                                JOIN city ON city.x = battel.x_city AND city.y = battel.y_city
                                JOIN player ON player.id_player = battel.id_player 
                            WHERE battel_member.id_player = :idp1 

                            UNION

                            SELECT DISTINCT battel.*, AttCi.name AS CityName, player.name AS PlayerName FROM `battel`
                                JOIN city AS myC ON myC.x = battel.x_coord AND myC.y = battel.y_coord 
                                JOIN city AS AttCi ON AttCi.x = battel.x_city AND AttCi.y = battel.y_city
                                JOIN player ON player.id_player = battel.id_player 
                                WHERE AttCi.id_player = :idp2

                            UNION

                            SELECT DISTINCT battel.*, city.name AS CityName, player.name AS PlayerName FROM `battel`
                                JOIN world_unit_garrison ON world_unit_garrison.x_coord = battel.x_coord AND world_unit_garrison.y_coord  = battel.y_coord 
                                JOIN city ON city.x = battel.x_city AND city.y = battel.y_city
                                JOIN player ON player.id_player = battel.id_player 
                                WHERE world_unit_garrison.id_player = :idp3",
                ["idp1" => $idPlayer, "idp2" => $idPlayer, "idp3" => $idPlayer]);

        return $Battel["Rows"];
    }

    function getHeroBack() {

        global $idPlayer;
        return selectFromTable(
                "id_hero AS idHero, x_from AS xFrom, x_to AS xTo, y_from AS yFrom, y_to AS yTo, time_back, task AS Task",
                "hero_back", "hero_back.id_player = :idp", ["idp" => $idPlayer]);
    }

    function getLeavingHero() {

        global $idPlayer;

        return selectFromTable(
                "battel_member.id_hero, battel_member.id_battel , battel.task,  battel_member.side ,"
                . " battel.time_start , battel.time_end , battel.x_coord , battel.y_coord , battel.x_city , battel.y_city",
                "battel_member JOIN  battel ON battel.id_battel = battel_member.id_battel", "battel_member.id_player = :idp", ["idp" => $idPlayer]);
    }

    function getGarrisonHeros() {
        global $idPlayer;
        return
                selectFromTable("*", "world_unit_garrison", "id_player = :idp", ["idp" => $idPlayer]);
    }

    function getSpyRuning() {

        global $idPlayer;
        return
                selectFromTable("*", "spy", "id_player = :idp", ["idp" => $idPlayer]);
    }

    function getUnitBattel() {

        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);

        return
                selectFromTable(
                "battel.*, city.name AS CityName, player.name AS PlayerName",
                "battel LEFT JOIN battel_member ON battel_member.id_battel = battel.id_battel
                    LEFT JOIN city ON city.x = battel.x_city AND city.y = battel.y_city
                    LEFT JOIN player ON player.id_player = battel.id_player ",
                "battel.x_coord = :x AND battel.y_coord = :y GROUP BY battel.id_battel", ["x" => $xCoord, "y" => $yCoord]);
    }

}
