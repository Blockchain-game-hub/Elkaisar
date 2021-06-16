<?php

class LBattel {

    static function MaxJoinNum($type) {

        if (
                LWorldUnit::isCarthasianGang($type) || LWorldUnit::isCarthageTeams($type) || LWorldUnit::isCarthageRebals($type) || LWorldUnit::isArmyCapital($type) || LWorldUnit::isStatueWalf($type) || LWorldUnit::isStatueWar($type)) {
            return 3;
        } else if (LWorldUnit::isCarthageForces($type) || LWorldUnit::isCarthageCapital($type)) {
            return 5;
        }else if(LWorldUnit::isSeaCity($type))
            return 200;

        return 750;
    }

    static function startingPrice($unitType) {
        if (LWorldUnit::isAsianSquads($unitType)) {
            $item = "truce_pack";
        } else if (LWorldUnit::isGangStar($unitType)) {
            $item = "t_map";
        } else if (LWorldUnit::isCamp($unitType) || LWorldUnit::isMonawrat($unitType)) {
            $item = "necklace_4";
        } else if (LWorldUnit::isCarthasianGang($unitType) || LWorldUnit::isCarthageTeams($unitType)) {
            $item = "repel_trumpet_1";
        } else if (LWorldUnit::isCarthageForces($unitType) || LWorldUnit::isCarthageRebals($unitType)) {
            $item = "repel_trumpet_2";
        } else if (LWorldUnit::isCarthageCapital($unitType)) {
            $item = "repel_medal";
        } else if (LWorldUnit::isArmyCapital($unitType)) {
            $item = "warrior_medal";
        } else if (LWorldUnit::isQueenCityS($unitType)) {
            $item = "bronze_order";
        } else if (LWorldUnit::isQueenCityM($unitType)) {
            $item = "silver_order";
        } else if (LWorldUnit::isQueenCityH($unitType)) {
            $item = "gold_order";
        } else if (LWorldUnit::isRepelCastleS($unitType)) {
            $item = "bronze_horn";
        } else if (LWorldUnit::isRepelCastleM($unitType)) {
            $item = "silver_horn";
        } else if (LWorldUnit::isRepelCastleH($unitType)) {
            $item = "gold_horn";
        } else if (LWorldUnit::isStatueWar($unitType)) {
            $item = "evil_army_pass";
        } else if (LWorldUnit::isStatueWalf($unitType)) {
            $item = "evil_pass";
        } else {
            return [];
        }
        return [
            [
                "Item" => $item,
                "amount" => 1
            ]
        ];
    }

    static function takeStartingPrice($unitType) {

        foreach (static::startingPrice($unitType) as $oneItem) {

            if (!LItem::useItem($oneItem["Item"], $oneItem["amount"]))
                    return false;
        }

        return true;
    }

    static function takeJoinPrice($unitType) {

        if (LWorldUnit::isCarthasianGang($unitType) || LWorldUnit::isCarthageTeams($unitType)) {
            $item = "repel_trumpet_1";
            $table = "matrial_main";
        } else if (LWorldUnit::isCarthageForces($unitType) || LWorldUnit::isCarthageRebals($unitType)) {
            $item = "repel_trumpet_2";
            $table = "matrial_main";
        } else if (LWorldUnit::isCarthageCapital($unitType)) {
            $item = "repel_medal";
            $table = "matrial_main";
        } else {
            return true;
        }

        return LItem::useItem($item);
    }

    static function takeHeroPower($idHero, $unitType) {
        $power = 10;

        if ($unitType >= WUT_MONAWRAT && $unitType <= WUT_BRAVE_THUNDER)
                $power = 40;
        else if (LWorldUnit::isGangStar($unitType) || LWorldUnit::isCarthasianGang($unitType))
                $power = 20;
        else if (LWorldUnit::isCarthageTeams($unitType)) $power = 30;
        else if (LWorldUnit::isCarthageRebals($unitType)) $power = 40;
        else if (LWorldUnit::isCarthageForces($unitType) || LWorldUnit::isArmyCapital($unitType))
                $power = 50;
        else if (LWorldUnit::isCarthageCapital($unitType)) $power = 60;
        else if (LWorldUnit::isStatueWalf($unitType) || LWorldUnit::isStatueWar($unitType))
                $power = 100;
        else if (LWorldUnit::isQueenCityS($unitType) || LWorldUnit::isRepelCastleS($unitType))
                $power = 40;
        else if (LWorldUnit::isQueenCityM($unitType) || LWorldUnit::isRepelCastleM($unitType))
                $power = 50;
        else if (LWorldUnit::isQueenCityH($unitType) || LWorldUnit::isRepelCastleH($unitType) || LWorldUnit::isSeaCity($unitType))
                $power = 60;



        $heroPower = selectFromTable("power", "hero", "id_hero = :idh", ["idh" => $idHero]);
        if (!count($heroPower)) return 0;
        if ($heroPower[0]["power"] < $power) return 0;
        updateTable("power = power - :po", "hero", "id_hero = :idh", ["idh" => $idHero, "po" => $power]);
        return $power;
    }

    static function join($Battel, $Hero, $side) {
        global $idPlayer;
        $ord = time();
        if ($side == BATTEL_SIDE_ATT)
                updateTable("attackNum = attackNum + 1", "battel", "id_battel = :idb", ["idb" => $Battel["id_battel"]]);
        else if ($side == BATTEL_SIDE_DEF)
                updateTable("defenceNum = defenceNum + 1", "battel", "id_battel = :idb", ["idb" => $Battel["id_battel"]]);


        (new LWebSocket())->send(json_encode([
            "url" => "Battel/Join",
            "data" => [
                "Hero" => ["id_player" => $Hero["id_player"], "id_hero" => $Hero["id_hero"], "id_city" => $Hero["id_city"], "side" => $side],
                "Battel" => $Battel
        ]]));

        return insertIntoTable("id_battel = :idb , id_player = :idp ,id_hero = :idh ,  side = :si , ord = :od", "battel_member",
                ["idb" => $Battel["id_battel"], "idp" => $idPlayer, "idh" => $Hero["id_hero"], "si" => $side, "od" => $ord]);
    }

    static function getBattel($Battel) {
        $city = selectFromTable("city.name AS c_name , player.name AS p_name",
                        "city JOIN player ON city.id_player = player.id_player",
                        "city.x = :bxc AND city.y = :byc", ["bxc" => $Battel["x_city"], "byc" => $Battel["y_city"]])[0];

        return ([
            "state" => "ok",
            "time_end" => $Battel["time_end"],
            "time_start" => $Battel["time_start"],
            "unit_type" => $Battel["t"],
            "unit_lvl" => $Battel["l"],
            "x_city" => $Battel["x_city"],
            "y_city" => $Battel["y_city"],
            "c_name" => $city["c_name"],
            "p_name" => $city["p_name"],
            "attack_num" => selectFromTable("COUNT(*) AS count", "battel_member", "id_battel = :idb AND side = :si", ["idb" => $Battel["id_battel"], "si" => SIDE_ATTACK])[0]["count"],
            "defence_num" => selectFromTable("COUNT(*) AS count", "battel_member", "id_battel = :idb AND side = :si", ["idb" => $Battel["id_battel"], "si" => SIDE_DEFANCE])[0]["count"]
        ]);
    }

    static function abortBattel($Hero, $Battel) {
        $now = time();
        $in_duration = 2 * $now - $Battel["time_start"];

        insertIntoTable(
                "id_hero = :idh, id_player = :idp , x_from = :xc ,  y_from = :yc , time_back = :tb , task = :t",
                "hero_back",
                ["idh" => $Hero["id_hero"], "idp" => $Hero["id_player"], "xc" => $Battel["x_coord"], "yc" => $Battel["y_coord"], "tb" => $in_duration, "t" => $Battel["task"]]);
    }

    static function involvedPlayers($Unit) {
        $ArmyCapital = [];
        $City = [];
        $Garrison = selectFromTable("id_player", "world_unit_garrison", "x_coord = :x AND y_coord = :y", ["x" => $Unit["x"], "y" => $Unit["y"]]);



        if (LWorldUnit::isCity($Unit["ut"]))
                $City = selectFromTable("id_player", "city", "x = :x AND y = :y", ["x" => $Unit["x"], "y" => $Unit["y"]]);
        if (LWorldUnit::isArmyCapital($Unit["ut"]))
                $ArmyCapital = selectFromTable("id_dominant", "world_unit_rank", "x = :xc AND y = :yc ORDER BY id_round DESC LIMIT 1", ["xc" => $Unit["x"], "yc" => $Unit["y"]]);



        return
                array_unique(
                array_merge(
                        array_values(array_column($City, "id_player")),
                        array_values(array_column($Garrison, "id_player")),
                        array_values(array_column($ArmyCapital, "id_dominant"))
                )
        );
    }

    static function announceStart($Unit) {
        global $idPlayer;
        if (!LWorldUnit::isQueenCity($Unit["ut"]) && !LWorldUnit::isRepelCastle($Unit["ut"]))
                return;



        $Player = selectFromTable(
                "player.id_player, player.name AS PlayerName, guild.name AS GuildName, guild.slog_top, guild.slog_cnt, guild.slog_btm, player.id_guild",
                "player LEFT JOIN guild ON guild.id_guild = player.id_guild", "player.id_player = :idp", ["idp" => $idPlayer]);
        if (!count($Player)) return catchError();
        $Player[0]["x_coord"] = $Unit["x"];
        $Player[0]["y_coord"] = $Unit["y"];

        (new LWebSocket())->send(json_encode([
            "url" => "ServerAnnounce/BattelStarted",
            "data" => $Player[0]]));
    }

    static function getBattelById($idBattel) {

        return selectFromTable(
                        "battel.*, player.name AS PlayerName, city.name AS CityName, player.id_guild AS idGuild,player.guild AS GuildName",
                        "battel JOIN city ON city.x = battel.x_city AND city.y = battel.y_city  JOIN player ON battel.id_player = player.id_player",
                        "id_battel = :idb", ["idb" => $idBattel])[0];
    }

    public static function battelHeros($Battel) {
        return selectFromTable("battel_member.id_hero , battel_member.id_player ,"
                . "hero.point_b, hero.point_b_plus, hero.point_c, hero.point_c_plus  , "
                . "hero_medal.medal_den , hero_medal.medal_leo , battel_member.ord , "
                . "hero.id_city  , battel_member.side , city.x, city.y ",
                "`battel_member` JOIN  hero ON hero.id_hero = battel_member.id_hero JOIN hero_medal "
                . " ON hero_medal.id_hero = battel_member.id_hero JOIN city ON hero.id_city = city.id_city",
                "battel_member.id_battel = :idb ORDER BY battel_member.ord ASC", ["idb" => $Battel["id_battel"]]);
    }

    public static function getHeros($Battel, $Unit) {

        $Heros = [];

        $SystemHeros = LWorld::unitHeros($Unit);

        $garrison_heros = LWorld::unitGarrisonHero($Unit);

        $CityWall = null;

        if ($Battel["task"] == BATTEL_TASK_DOMINATE && LWorldUnit::isCity($Unit["ut"]))
                $Heros[] = LHero::prepareForBattel(LWorld::cityWall($Unit), $Battel);


        $id_fake = count($SystemHeros) * -1 - count($garrison_heros);



        foreach ($SystemHeros as $hero) {

            $Heros[] = LHero::prepareForBattel([
                        "id_hero" => ++$id_fake, "id_player" => 0,
                        "id_city" => 0, "x" => 0, "y" => 0,
                        "side" => BATTEL_SIDE_DEF, "ord" => $hero["ord"],
                        "type" => $hero["type"], "pre" => $hero["pre"],
                        "is_garrsion" => FALSE,
                            ], $Battel);
        }


        /* garison heros */
        foreach ($garrison_heros as $one_hero) {

            $one_hero["side"] = BATTEL_SIDE_DEF;
            $one_hero["is_garrsion"] = true;

            $Heros[] = LHero::prepareForBattel($one_hero, $Battel);
        }


        foreach (static::battelHeros($Battel) as $oneHero) {
            $oneHero["is_garrsion"] = false;
            $Heros[] = LHero::prepareForBattel($oneHero, $Battel);
        }

        /*   this function will prepare hero object for attack */
        static::getHerosFightEff($Heros, $Unit);
        return $Heros;
    }

    public static function getHerosFightEff(&$Heros, $Unit) {
        LArmy::prepareHeroBattel($Heros);
        LHero::prepareHeroPowerBattel($Heros, $Unit);
        LEquip::prepareHeroBattel($Heros, $Unit);
    }

    public static function getPlayers($Heros) {
        $Players = [];



        foreach ($Heros as $oneHero) {

            if (isset($Players[$oneHero["id_player"]])) {
                $Players[$oneHero["id_player"]]["heroNum"] ++;
            } else {
                $Players[$oneHero["id_player"]] = CPlayer::$BattelPlayerEmpty;
            }

            $Players[$oneHero["id_player"]]["idPlayer"] = $oneHero["id_player"];
            $Players[$oneHero["id_player"]]["side"] = $oneHero["side"];

            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["f_1"]] += $oneHero["pre"]["f_1"];

            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["f_2"]] += $oneHero["pre"]["f_2"];
            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["f_3"]] += $oneHero["pre"]["f_3"];
            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["b_1"]] += $oneHero["pre"]["b_1"];
            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["b_2"]] += $oneHero["pre"]["b_2"];
            $Players[$oneHero["id_player"]]["Troops"][$oneHero["type"]["b_3"]] += $oneHero["pre"]["b_3"];

            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["f_1"]] += $oneHero["pre"]["f_1"];
            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["f_2"]] += $oneHero["pre"]["f_2"];
            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["f_3"]] += $oneHero["pre"]["f_3"];
            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["b_1"]] += $oneHero["pre"]["b_1"];
            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["b_2"]] += $oneHero["pre"]["b_2"];
            $Players[$oneHero["id_player"]]["RemainTroops"][$oneHero["type"]["b_3"]] += $oneHero["pre"]["b_3"];
        }
        return $Players;
    }

}
