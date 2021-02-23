<?php

class AArenaChallange {

    function getArenaData() {
        global $idPlayer;
        $ArenaData = selectFromTable("*", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        return [
            "Arena" => $ArenaData[0],
            "HeroList" => selectFromTable("*", "arena_player_challange_hero", "id_player = :idp ORDER BY ord ASC LIMIT 15", ["idp" => $idPlayer]),
            "PlayerList" => selectFromTable("*", "arena_player_challange", "id_player = :idp AND rank < :r ORDER BY rank DESC LIMIT 10", ["idp" => $idPlayer, "r" => max($ArenaData[0]["rank"], 11)])
        ];
    }

    private function reOrderHeros() {
        global $idPlayer;
        $Heros = selectFromTable("id_hero", "arena_player_challange_hero", "id_player = :idp ORDER BY ord ASC ", ["idp" => $idPlayer]);
        $ord = 0;
        foreach ($Heros as $one) {

            updateTable("ord = :o", "arena_player_challange_hero", "id_player = :idp AND id_hero = :idh", ["idp" => $idPlayer, "idh" => $one["id_hero"], "o" => $ord]);
            $ord ++;
        }
    }

    function getFightList() {

        global $idPlayer;
        $ArenaData = selectFromTable("rank", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        return selectFromTable(
                " player.id_player AS idPlayer, player.name AS PlayerName, guild.id_guild AS idGuild,"
                . " guild.name AS GuildName, guild.slog_top, guild.slog_cnt, guild.slog_btm, player.porm,"
                . " arena_player_challange.rank, arena_player_challange.lvl AS arenaLvl",
                " arena_player_challange JOIN player ON player.id_player = arena_player_challange.id_player "
                . " LEFT JOIN guild ON guild.id_guild = player.id_guild",
                " arena_player_challange.rank < :r ORDER BY rank DESC LIMIT 10",
                ["r" => max($ArenaData[0]["rank"], 11)]);
    }

    function saveHeroList() {

        global $idPlayer;
        $idHeros = explode("-", $_POST["HeroList"]);
        $ord = 0;
        $Arena = selectFromTable("lvl", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);


        if (!count($Arena)) return ["state" => "error_0"];
        if (count($idHeros) > $Arena[0]["lvl"]) return ["state" => "error_1"];

        $approved = 0;
        $refused = 0;
        deleteTable("arena_player_challange_hero", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($idHeros as $oneId) {
            if (!is_numeric($oneId))
                    return ["state" => "error", "TryToHack" => TryToHack()];

            $HeroArmy = LHeroArmy::isCarringArmy($oneId);
            if ($HeroArmy == false) {
                $refused++;
                continue;
            }
            $approved++;

            insertIntoTable(
                    "id_player = :idp, id_hero = :idh, ord = :o, "
                    . "f_1_type = :f1t, f_1_num = :f1n, f_2_type = :f2t, f_2_num = :f2n, f_3_type = :f3t, f_3_num = :f3n, "
                    . "b_1_type = :b1t, b_1_num = :b1n, b_2_type = :b2t, b_2_num = :b2n, b_3_type = :b3t, b_3_num = :b3n",
                    "arena_player_challange_hero",
                    [
                        "idp" => $idPlayer, "idh" => $oneId, "o" => $ord++,
                        "f1t" => $HeroArmy["f_1_type"], "f1n" => $HeroArmy["f_1_num"],
                        "f2t" => $HeroArmy["f_2_type"], "f2n" => $HeroArmy["f_2_num"],
                        "f3t" => $HeroArmy["f_3_type"], "f3n" => $HeroArmy["f_3_num"],
                        "b1t" => $HeroArmy["b_1_type"], "b1n" => $HeroArmy["b_1_num"],
                        "b2t" => $HeroArmy["b_2_type"], "b2n" => $HeroArmy["b_2_num"],
                        "b3t" => $HeroArmy["b_3_type"], "b3n" => $HeroArmy["b_3_num"]
            ]);
        }

        $ArenaData = selectFromTable("*", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        return [
            "state" => "ok",
            "Arena" => $ArenaData[0],
            "HeroList" => selectFromTable("*", "arena_player_challange_hero", "id_player = :idp ORDER BY ord ASC LIMIT 15", ["idp" => $idPlayer]),
            "PlayerList" => selectFromTable("*", "arena_player_challange", "id_player = :idp AND rank <= :r ORDER BY rank DESC LIMIT 10", ["idp" => $idPlayer, "r" => $ArenaData[0]["rank"]]),
            "refused" => $refused, "approved" => $approved
        ];
    }

    function buyBattelAttempt() {

        global $idPlayer;
        $amount = validateID($_POST["amount"]);
        $price = 4;
        if ($amount == 10) $price = 3;
        else if ($amount == 25) $price = 2;

        
        $BuyTimes = selectFromTable("*", "arena_player_challange_buy", "id_player = :idp", ["idp" => $idPlayer]);
        
        if($BuyTimes[0]["buy_times"] > 0)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if (!LPlayer::tekeGold($amount * $price))
            return ["state" => "error_0", "TryToHack" => TryToHack()];

        updateTable("attempt = attempt + :a", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer, "a" => $amount]);
        updateTable("buy_times = buy_times + 1", "arena_player_challange_buy", "id_player = :idp", ["idp" => $idPlayer]);
        $ArenaData = selectFromTable("*", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        return [
            "state" => "ok",
            "Arena" => $ArenaData[0],
            "HeroList" => [],
            "PlayerList" => []
        ];
    }

    function getRankList() {

        global $idPlayer;
        $offset = validateID($_GET["offset"]);
        $OrderBy = validateNumber($_GET["orderBy"]);

        if ($OrderBy == 0)
                return selectFromTable(
                    "arena_player_challange.*, guild.name AS GuildName, guild.id_guild AS idGuild, player.name AS PlayerName,"
                    . " player.porm,  guild.slog_top, guild.slog_cnt, guild.slog_btm",
                    "arena_player_challange JOIN player ON player.id_player = arena_player_challange.id_player"
                    . " LEFT JOIN guild ON guild.id_guild = player.id_guild",
                    "1 ORDER BY arena_player_challange.rank ASC LIMIT 15 OFFSET $offset");



        return
                selectFromTable(
                "arena_player_challange.*, guild.name AS GuildName, player.name AS PlayerName, guild.id_guild AS idGuild, player.porm,"
                . " guild.slog_top, guild.slog_cnt, guild.slog_btm",
                "arena_player_challange JOIN player ON player.id_player = arena_player_challange.id_player"
                . " LEFT JOIN guild ON guild.id_guild = player.id_guild",
                "1 ORDER BY arena_player_challange.lvl DESC, arena_player_challange.exp DESC, arena_player_challange.win DESC LIMIT 15 OFFSET $offset");
    }

    function fightSomeOne() {

        global $idPlayer;
        $idPlayerToFight = validateID($_POST["idPlayerToFight"]);


        $Arena = selectFromTable("lastAttackTime , rank, lvl, attempt", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);
        $ArenaToFight = selectFromTable("lastAttackTime , rank, lvl", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayerToFight]);

        $AttackHeros = selectFromTable("hero.id_hero, hero.id_city, hero.id_player", "arena_player_challange_hero JOIN hero ON hero.id_hero = arena_player_challange_hero.id_hero", "arena_player_challange_hero.id_player = :idp ORDER BY arena_player_challange_hero.ord ASC", ["idp" => $idPlayer]);
        $DefenceHeros = selectFromTable("hero.id_hero, hero.id_city, hero.id_player", "arena_player_challange_hero JOIN hero ON hero.id_hero = arena_player_challange_hero.id_hero", "arena_player_challange_hero.id_player = :idp ORDER BY arena_player_challange_hero.ord ASC", ["idp" => $idPlayerToFight]);


        if (!count($Arena)) return ["state" => "error_0"];
        if ($ArenaToFight[0]["rank"] < $Arena[0]["rank"] - 10)
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($ArenaToFight[0]["rank"] > max($Arena[0]["rank"], 11))
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if ($idPlayer == $idPlayerToFight)
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if (count($AttackHeros) <= 0)
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if (count($AttackHeros) > $Arena[0]["lvl"])
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if ($Arena[0]["lastAttackTime"] + 10 * 60 > time())
                return ["state" => "error_4"];
        if ($Arena[0]["attempt"] <= 0) return ["state" => "error_5"];

        updateTable("attempt = attempt - 1, lastAttackTime = :t", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer, "t" => time()]);

        $CityFrom = selectFromTable("city.x, city.y", "city JOIN hero ON hero.id_city = city.id_city", "hero.id_hero = :idh", ["idh" => $AttackHeros[0]["id_hero"]]);
        $idBattel = insertIntoTable(
                "id_hero = :hl, time_start = :ts, time_end = :te, x_coord = :xt, y_coord = :yt, id_player = :idp, id_player_def = :idpd, x_city = :xf , y_city = :yf , task = :tk",
                "battel", [
            "hl" => $AttackHeros[0]["id_hero"], "ts" => 0, "te" => (time() + 1), "xt" => 233, "yt" => 246, "idpd" => $idPlayerToFight,
            "idp" => $idPlayer, "xf" => $CityFrom[0]["x"], "yf" => $CityFrom[0]["y"], "tk" => BATTEL_TASK_CHALLANGE
        ]);

        $Battel = LBattel::getBattelById($idBattel);

        (new LWebSocket())->send(json_encode([
            "url" => "Battel/newBattelStarted",
            "data" => [
                "Battel" => $Battel
            ]
        ]));
        foreach ($AttackHeros as $one) {
            LBattel::join($Battel, $one, BATTEL_SIDE_ATT);
        }
        foreach ($DefenceHeros as $one) {
            LBattel::join($Battel, $one, BATTEL_SIDE_DEF);
        }

        return ["state" => "ok"];
    }

    function speedUpAtte() {

        global $idPlayer;

        if (!LPlayer::tekeGold(2)) return ["state" => "error_0"];
        updateTable("lastAttackTime = 0", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer]);

        return [
            "state" => "ok",
            "Arena" => selectFromTable("*", "arena_player_challange", "id_player = :idp", ["idp" => $idPlayer])[0]
        ];
    }
    
    
    function addExpByBox(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        
        
        if(!LItem::useItem($Item))
            return ["state" => "error_0"];
        
        $amount = 0;
        
        if($Item == "arena_exp_1")
            $amount = 1;
        else if($Item == "arena_exp_5")
            $amount = 5;
        else if($Item == "arena_exp_10")
            $amount = 10;
        else if($Item == "arena_exp_25")
            $amount = 25;
        else 
            return ["state" => "error_0"];
            
        updateTable("exp = exp + :e", "arena_player_challange", "id_player = :idp", ["e" => $amount, "idp" => $idPlayer]);
        return [
            "state" => "ok",
            "Exp"  => $amount
        ];
        
    
    }
    
    function addAttByBox(){
        
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        
        
        if(!LItem::useItem($Item))
            return ["state" => "error_0"];
        
        $amount = 0;
        
        if($Item == "arena_attempt_1")
            $amount = 1;
        else if($Item == "arena_attempt_5")
            $amount = 5;
        else if($Item == "arena_attempt_10")
            $amount = 10;
        else 
            return ["state" => "error_0"];
            
        updateTable("attempt = attempt + :e", "arena_player_challange", "id_player = :idp", ["e" => $amount, "idp" => $idPlayer]);
        return [
            "state" => "ok",
            "Att"  => $amount
        ];
        
    
    }

}
