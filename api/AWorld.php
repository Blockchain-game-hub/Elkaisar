<?php

class AWorld {

    function getWorldUnitPrize() {

        return [
            "Win"     => selectFromTable("*", "world_unit_prize",         "1"),
            "Lose"    => selectFromTable("*", "world_unit_prize_lose",    "1"),
            "Sp"      => selectFromTable("*", "world_unit_prize_sp",      "1"),
            "Plunder" => selectFromTable("*", "world_unit_prize_plunder", "1")
        ];
    }

    function getWorldCity() {
        return selectFromTable(
                "world.x, world.y, world.ut AS t,city.id_city AS idCity, city.name AS CityName, player.name AS PlayerName, "
                . "city.flag AS FlagName, player.guild AS GuildName, player.id_guild AS idGuild, city.lvl, player.id_player AS idPlayer",
                "world JOIN city ON city.x = world.x AND city.y = world.y JOIN player ON player.id_player = city.id_player",
                "world.ut BETWEEN :s AND :e",
                ["s" => WUT_CITY_LVL_0, "e" => WUT_CITY_LVL_3]);
    }

    function getBarrayConolizer() {
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);

        $Barr = selectFromTable(
                "p.name as PlayerName, p.id_player AS idPlayer ,p.guild AS GuildName ,  p.id_guild , c.name as CityName , c.x , c.y",
                "player p, city_bar b, city c",
                "c.id_player = p.id_player  AND b.x_coord = :x AND b.y_coord = :y AND b.id_city = c.id_city ", ["x" => $xCoord, "y" => $yCoord]);
        if (!count($Barr)) return [];

        if (!$Barr[0]["id_guild"]) return $Barr;

        $Guild = selectFromTable("name AS GuildName, slog_top, slog_btm, slog_cnt", "guild", "id_guild = :idg", ["idg" => $Barr[0]["id_guild"]]);
        if (!count($Guild)) return $Barr;
        $Barr[0]["GuildName"] = $Guild[0]["GuildName"];
        $Barr[0]["slog_top"] = $Guild[0]["slog_top"];
        $Barr[0]["slog_btm"] = $Guild[0]["slog_btm"];
        $Barr[0]["slog_cnt"] = $Guild[0]["slog_cnt"];

        return $Barr;
    }

    function BuildNewCity() {
        
        global $idPlayer;

        $xCoord = validateID($_POST["xCoord"]);
        $yCoord = validateID($_POST["yCoord"]);
        $idCity = validateID($_POST["idCity"]);
        $CityName = validatePlayerWord($_POST["CityName"]);

        $Player = selectFromTable("porm", "player", "id_player = :idp", ["idp" => $idPlayer]);
        $cityCount = selectFromTable("COUNT(*) AS c_count", "city", "id_player = :idp", ["idp" => $idPlayer]);
        $unit = selectFromTable("ut", "world", "x = :xc AND y = :yc", ["xc" => $xCoord, "yc" => $yCoord]);

        if (!count($Player) || !count($cityCount) || !count($unit))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($unit[0]["ut"] != WUT_EMPTY)
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if (mb_strlen($CityName) > 8) return ["state" => "error_2"];

        if ($cityCount[0]["c_count"] > 4)
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if ($Player[0]['porm'] < $cityCount[0]["c_count"] * 2)
                return ["state" => "error_4", "TryToHack" => TryToHack()];

        $Res = [
            "food" => pow(10, $cityCount[0]["c_count"] + 2),
            "wood" => pow(10, $cityCount[0]["c_count"] + 2),
            "stone" => pow(10, $cityCount[0]["c_count"] + 2),
            "metal" => pow(10, $cityCount[0]["c_count"] + 2),
            "coin" => pow(10, $cityCount[0]["c_count"] + 2)
        ];

        if (!LCity::isResourceTaken($Res, $idCity))
                return ["state" => "error_5", "TryToHack" => TryToHack()];

        LCity::addCity($xCoord, $yCoord, $CityName);
        (new LWebSocket())->send(json_encode(["url" => "World/refreshWorldUnit", "data" => [
                "Units" => [
                    ["x" => $xCoord, "y" => $yCoord]
                ]
        ]]));

        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }

    function refreshWorldUnitLvl() {
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
        return selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord])[0];
    }

    function refreshWorldUnit() {

        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);

        $Unit = selectFromTable("x, y, ut AS t, s, l, lo, p", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord])[0];
        $Army = [
            "army_a" => 0, "army_b" => 0, "army_c" => 0,
            "army_d" => 0, "army_e" => 0, "army_f" => 0,
            0 => 0,
        ];

        if ($Unit["t"] <= WUT_WOODS_LVL_10)
                return [
                "Unit" => $Unit,
                "Army" => $Army
            ];

        $Heros = selectFromTable("*", "world_unit_hero", "x = :x AND y =:y AND lvl = :l", ["x" => $Unit["x"], "y" => $Unit["y"], "l" => $Unit["l"]]);
        foreach ($Heros as $oneHero) {
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_1_type"]]] += $oneHero["f_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_2_type"]]] += $oneHero["f_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_3_type"]]] += $oneHero["f_3_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_1_type"]]] += $oneHero["b_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_2_type"]]] += $oneHero["b_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_3_type"]]] += $oneHero["b_3_num"];
        }
        return [
            "Unit" => $Unit,
            "Army" => $Army
        ];
    }

    function getWorldUnitRank() {
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
        $UnitType = validateId($_GET["unitType"]);

        if (LWorldUnit::isArenaGuild($UnitType) || LWorldUnit::isQueenCity($UnitType) || LWorldUnit::isRepelCastle($UnitType)) {

            $names = "guild.name AS GuildName,guild.slog_top, guild.slog_btm, guild.slog_cnt";
            $joiner = "JOIN guild
                    ON guild.id_guild = world_unit_rank.id_dominant";
        } else {

            $names = "player.name  PlayerName, player.guild AS GuildName";
            $joiner = "JOIN player
                    ON player.id_player = world_unit_rank.id_dominant";
        }

        return selectFromTable(
                "world_unit_rank.id_dominant ,
                    SUM(world_unit_rank.duration) AS totalDuration,
                    SUM(world_unit_rank.win_num) AS roundNum,
                    $names"
                , "world_unit_rank  $joiner "
                , " world_unit_rank.x = :xc 
                    AND 
                    world_unit_rank.y = :yc 
                    GROUP BY world_unit_rank.id_dominant
                    ORDER BY totalDuration DESC LIMIT 5",
                ["xc" => $xCoord, "yc" => $yCoord]);
    }

    function getCityData() {
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);

        $City = selectFromTable(
                "city.name AS CityName, player.name AS PlayerName, player.id_guild, player.id_player, city.id_city, player.prestige",
                "city JOIN player ON player.id_player = city.id_player", "city.x = :x AND city.y = :y", ["x" => $xCoord, "y" => $yCoord]);

        if (!count($City)) return [];
        if (!$City[0]["id_guild"]) return $City[0];

        $Guild = selectFromTable("id_guild, name, slog_top, slog_btm, slog_cnt", "guild", "id_guild = :idg", ["idg" => $City[0]["id_guild"]]);

        $City[0]["GuildName"] = $Guild[0]["name"];
        $City[0]["slog_top"] = $Guild[0]["slog_top"];
        $City[0]["slog_btm"] = $Guild[0]["slog_btm"];
        $City[0]["slog_cnt"] = $Guild[0]["slog_cnt"];

        return $City[0];
    }

    function getUnitEquip() {

        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
        $lvl = validateID($_GET["lvl"]);
        return selectFromTable("*", "world_unit_equip", "x = :x AND y = :y AND l = :l", ["x" => $xCoord, "y" => $yCoord, "l" => $lvl]);
    }

    function getUnitArmy() {

        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);
        $lvl = validateID($_GET["lvl"]);
        $Army = [
            0 => 0,
            "army_a" => 0, "army_b" => 0, "army_c" => 0,
            "army_d" => 0, "army_e" => 0, "army_f" => 0
        ];
        $Heros = selectFromTable("*", "world_unit_hero", "x = :x AND y = :y AND lvl = :l", ["x" => $xCoord, "y" => $yCoord, "l" => $lvl]);
        foreach ($Heros as $oneHero):
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_1_type"]]] += $oneHero["f_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_2_type"]]] += $oneHero["f_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["f_3_type"]]] += $oneHero["f_3_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_1_type"]]] += $oneHero["b_1_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_2_type"]]] += $oneHero["b_2_num"];
            $Army[CArmy::$ArmyCityPlace[$oneHero["b_3_type"]]] += $oneHero["b_3_num"];
        endforeach;
        return $Army;
    }

    function getFavUnit() {

        global $idPlayer;
        return
                selectFromTable("*", "world_unit_fav", "id_player = :idp", ["idp" => $idPlayer]);
    }

    function addUnitToFav() {
        global $idPlayer;
        $xCoord = validateID($_POST["xCoord"]);
        $yCoord = validateID($_POST["yCoord"]);
        insertIntoTable("id_player = :idp, x =:x, y = :y", "world_unit_fav", ["idp" => $idPlayer, "x" => $xCoord, "y" => $yCoord]);
    }

    function getGuildAttackQue() {
        $xCoord = validateID($_GET["xCoord"]);
        $yCoord = validateID($_GET["yCoord"]);

        return LWorld::getWorldAttackQueueForGuild(["x" => $xCoord, "y" => $yCoord]);
    }

}
