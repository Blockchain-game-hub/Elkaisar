<?php

class AWorldUnit {

    function plundePrize() {
        global $idPlayer;


        $xCoord = validateID($_POST["xCoord"]);
        $yCoord = validateID($_POST["yCoord"]);

        $Unit = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord]);

        if (!count($Unit)) return ["state" => "error_0"];
        if (LWorldUnit::isRepelCastle($Unit[0]["ut"]))
                return $this->plundeRepleCastlePrize($Unit[0]);
        if (LWorldUnit::isQueenCity($Unit[0]["ut"]))
                return $this->plundeQueenCityPrize($Unit[0]);



        return [
            "state" => "error_1"
        ];
    }

    private function plundeRepleCastlePrize($Unit) {

        global $idPlayer;

        $Domainant   = selectFromTable("*", "world_unit_rank", "x = :x AND y = :y", ["x" => $Unit["x"], "y" => $Unit["y"]]);
        $playerGuild = selectFromTable("id_guild, time_join", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $PrizeTaken  = selectFromTable("*"  ,"world_prize_taken", "x = :x AND y = :y AND id_player = :idp", ["x" => $Unit["x"], "y" => $Unit["y"], "idp" => $idPlayer]);

        if (!count($Domainant)) return ["state" => "error_1_0"];
        if (count($playerGuild) == 0) return ["state" => "error_1_1"];
        if ($Domainant[0]["id_dominant"] != $playerGuild[0]["id_guild"])
                return ["state" => "error_1_2"];
        if (count($PrizeTaken) > 0) return ["state" => "error_1_3"];
        if ($playerGuild[0]["time_join"] + 3 * 24 * 60 * 60 > time())
                return ["state" => "error_1_4"];

        $PrizeList = selectFromTable("*", "world_unit_prize_plunder", "unitType = :ut ORDER BY RAND()", ["ut" => $Unit["ut"]]);

        $PrizWin = [];
        foreach ($PrizeList as $onePrize) {

            $Luck = rand(1, 1000);
            if ($Luck > $onePrize["win_rate"]) continue;

            $amount = rand($onePrize["amount_min"], $onePrize["amount_max"]);
            LItem::addItem($onePrize["prize"], $amount);
            $PrizWin[] = [
                "Item" => $onePrize["prize"],
                "amount" => $amount
            ];
        }
        queryExe("INSERT INTO world_prize_taken(x, y, id_player) VALUES(:x, :y, :idp) ON DUPLICATE KEY UPDATE take_time = take_time + 1", ["x" => $Unit["x"], "y" => $Unit["y"], "idp" => $idPlayer]);
        return [
            "state" => "ok",
            "PrizeList" => $PrizWin
        ];
    }

    private function plundeQueenCityPrize($Unit) {

        global $idPlayer;

        $Domainant = selectFromTable("*", "world_unit_rank", "x = :x AND y = :y ORDER BY id_round DESC LIMIT 1", ["x" => $Unit["x"], "y" => $Unit["y"]]);
        $playerGuild = selectFromTable("id_guild, time_join", "guild_member", "id_player = :idp", ["idp" => $idPlayer]);
        $PrizeTaken = selectFromTable("*","world_prize_taken", "x = :x AND y = :y AND id_player = :idp", ["x" => $Unit["x"], "y" => $Unit["y"], "idp" => $idPlayer]);

        if (!count($Domainant)) return ["state" => "error_1_0"];
        if (count($playerGuild) == 0) return ["state" => "error_1_1"];
        if ($Domainant[0]["id_dominant"] != $playerGuild[0]["id_guild"])
                return ["state" => "error_1_2"];
        if (count($PrizeTaken) > 0) return ["state" => "error_1_3"];
        if ($playerGuild[0]["time_join"] + 3 * 24 * 60 * 60 > time())
                return ["state" => "error_1_4"];

        $PrizeList = selectFromTable("*", "world_unit_prize_plunder", "unitType = :ut ORDER BY RAND()", ["ut" => $Unit["ut"]]);

        $PrizWin = [];
        foreach ($PrizeList as $onePrize) {

            $Luck = rand(1, 1000);
            if ($Luck > $onePrize["win_rate"]) continue;

            $amount = rand($onePrize["amount_min"], $onePrize["amount_max"]);
            LItem::addItem($onePrize["prize"], $amount);
            $PrizWin[] = [
                "Item" => $onePrize["prize"],
                "amount" => $amount
            ];
        }
        queryExe("INSERT INTO world_prize_taken(x, y, id_player) VALUES(:x, :y, :idp) ON DUPLICATE KEY UPDATE take_time = take_time + 1", ["x" => $Unit["x"], "y" => $Unit["y"], "idp" => $idPlayer]);
        return [
            "state" => "ok",
            "PrizeList" => $PrizWin
        ];
    }

    function supportByHero() {

        global $idPlayer;

        $idHero = validateID($_POST["idHero"]);
        $xTo = validateID($_POST["xTo"]);
        $yTo = validateID($_POST["yTo"]);


        $Hero = selectFromTable("city.id_city, city.x, city.y, hero.in_city, hero.id_player", "hero JOIN city ON city.id_city = hero.id_city", "hero.id_hero = :idh AND hero.id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        $UnitTo = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xTo, "y" => $yTo]);
        if (!count($Hero))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!count($UnitTo))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($Hero[0]["in_city"] != HERO_IN_CITY)
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if (!LWorldUnit::isBarrary($UnitTo[0]["ut"]) && !LWorldUnit::isCity($UnitTo[0]["ut"]))
                return ["state" => "error_3", "TryToHack" => TryToHack()];


        $timeBack = time() + LWorldUnit::calAttackTime($idHero, $Hero[0], $UnitTo[0]);

        insertIntoTable(
                "id_hero = :idh, id_player = :idp, x_from = :xf, y_from = :yf, x_to = :xt, y_to = :yt, time_back = :tb, task = :t",
                "hero_back",
                [
                    "idh" => $idHero, "idp" => $Hero[0]["id_player"], "xf" => $Hero[0]["x"], "yf" => $Hero[0]["y"],
                    "xt" => $xTo, "yt" => $yTo, "tb" => $timeBack, "t" => BATTEL_TASK_SUPPORT
        ]);
        updateTable("in_city = :ic", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer, "ic" => HERO_IN_BATTEL]);

        return ["state" => "ok"];
    }

    function transHero() {

        global $idPlayer;

        $idHero = validateID($_POST["idHero"]);
        $xTo = validateID($_POST["xTo"]);
        $yTo = validateID($_POST["yTo"]);


        $Hero = selectFromTable("hero.in_city, city.id_player, city.x, city.y", "hero JOIN city ON city.id_city = hero.id_city", "hero.id_hero = :idh AND hero.id_player = :idp", ["idp" => $idPlayer, "idh" => $idHero]);
        $City = selectFromTable("id_player", "city", "x = :x AND  y = :y AND id_player = :idp", ["idp" => $idPlayer, "x" => $xTo, "y" => $yTo]);

        $UnitTo = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xTo, "y" => $yTo]);
        if (!count($Hero))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!count($UnitTo))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($Hero[0]["in_city"] != HERO_IN_CITY)
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if (!LWorldUnit::isCity($UnitTo[0]["ut"]))
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if (!count($City) || $City[0]["id_player"] != $idPlayer)
                return ["state" => "error_4", "TryToHack" => TryToHack()];


        $timeBack = time() + LWorldUnit::calAttackTime($idHero, $Hero[0], $UnitTo[0]);

        insertIntoTable(
                "id_hero = :idh, id_player = :idp, x_from = :xf, y_from = :yf, x_to = :xt, y_to = :yt, time_back = :tb, task = :t",
                "hero_back",
                [
                    "idh" => $idHero, "idp" => $Hero[0]["id_player"], "xf" => $Hero[0]["x"], "yf" => $Hero[0]["y"],
                    "xt" => $xTo, "yt" => $yTo, "tb" => $timeBack, "t" => BATTEL_TASK_HERO_TRANS
        ]);

        updateTable("in_city = :ic", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer, "ic" => HERO_IN_BATTEL]);

        return ["state" => "ok"];
    }

    function getWorldUnitDominator() {

        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
        $unitType = validateID($_GET["unitType"]);
        $Name = [["Name" => ""]];
        if (LWorldUnit::isRepelCastle($unitType) || LWorldUnit::isQueenCity($unitType))
                $Name = selectFromTable("guild.name AS Name", "guild JOIN world_unit_rank ON world_unit_rank.id_dominant = guild.id_guild", "world_unit_rank.x = :x AND world_unit_rank.y = :y ORDER BY id_round DESC LIMIT 1", ["x" => $xCoord, "y" => $yCoord]);
        if (LWorldUnit::isArmyCapital($unitType))
                $Name = selectFromTable("player.name AS Name", "player JOIN world_unit_rank ON world_unit_rank.id_dominant = player.id_player", "world_unit_rank.x = :x AND world_unit_rank.y = :y ORDER BY id_round DESC LIMIT 1", ["x" => $xCoord, "y" => $yCoord]);
        if (count($Name)) return $Name[0];
        return ["Name" => ""];
    }

}
