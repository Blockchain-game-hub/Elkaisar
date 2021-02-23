<?php

class AItemUse {

    function useMotivSpeech() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $motiv = selectFromTable('motiv', "player_stat", "id_player =:idp", ["idp" => $idPlayer]);

        if ($Item != "motiv_60" && $Item != "motiv_7")
                return ["state" => "error", "TryToHack" => TryToHack()];
        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!count($motiv))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_2", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "motiv_60")
                $newTime = max($newTime + 60 * 60 * 60 * $amount, $motiv[0]["motiv"] + 60 * 60 * 60 * $amount);
        else if ($Item == "motiv_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $motiv[0]["motiv"] + 60 * 60 * 24 * 7 * $amount);

        updateTable("motiv = :nt", "player_stat", "id_player = :idp", ["idp" => $idPlayer, "nt" => $newTime]);

        return ["state" => "ok"];
    }

    function useProtPop() {

        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem("prot_pop", $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];

        updateTable("pop = pop + GREATEST(100*$amount , pop_cap*0.2*$amount)", 'city', "id_city = :idc", ["idc" => $idCity]);
        LSaveState::saveCityState($idCity);
        LSaveState::coinInState($idCity);
        LSaveState::resInState($idCity, "food");
        LSaveState::resInState($idCity, "wood");
        LSaveState::resInState($idCity, "stone");
        LSaveState::resInState($idCity, "metal");

        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }

    function useCeaseFire() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $Truce = selectFromTable('peace', "player_stat", "id_player =:idp", ["idp" => $idPlayer]);

        if (!LItem::useItem("peace", $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!count($Truce))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        if ($Truce[0]["peace"] > time())
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        updateTable("peace = :nt", "player_stat", "id_player = :idp", ["nt" => time() + 60 * 60 * 24, "idp" => $idPlayer]);
        return ["state" => "ok"];
    }

    function useTheatrics() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem("a_play", $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        updateTable(" dis_loy = 0 , loy = 100, pop_state = 1", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }

    function useFreedomHelp() {

        global $idPlayer;
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem("freedom_help", 1))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        $Colonize = selectFromTable("*", "city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]);
        $City = selectFromTable("id_city, id_player", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);

        if (!count($City))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if (!count($Colonize))
            return ["state" => "error_2"];

        deleteTable("city_colonize", "id_city_colonized = :idc", ["idc" => $idCity]);
        LSaveState::afterCityColonized($Colonize[0]["id_city_colonized"]);
        LSaveState::afterCityColonizer($Colonize[0]["id_city_colonizer"]);
        (new LWebSocket())->send(json_encode(["url" => "World/refreshWorldColonizedCities"]));
        return ["state" => "ok"];
    }

    function useMedicalStatue() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $medical = selectFromTable("medical", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["medical"];

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "medical_moun")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $medical + 60 * 60 * 24 * $amount);
        if ($Item == "mediacl_statue")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $medical + 60 * 60 * 24 * 7 * $amount);
        updateTable(" medical = :nt", 'player_stat', "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        return ["state" => "ok"];
    }

    function useAttackAdvancer() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $attack = selectFromTable("attack_10", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["attack_10"];
        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "sparta_stab")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $attack + 60 * 60 * 24 * $amount);
        if ($Item == "qulinds_shaft")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $attack + 60 * 60 * 24 * 7 * $amount);

        updateTable("attack_10 = :nt", "player_stat", "id_player = :idp", ["idp" => $idPlayer, "nt" => $newTime]);
        return ["state" => "ok"];
    }

    function useDefenceAdvancer() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $defence = selectFromTable("defence_10", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["defence_10"];
        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "marmlo_helmet")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $defence + 60 * 60 * 24 * $amount);
        if ($Item == "march_prot")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $defence + 60 * 60 * 24 * 7 * $amount);

        updateTable("defence_10 = :nt", "player_stat", "id_player = :idp", ["idp" => $idPlayer, "nt" => $newTime]);
        return ["state" => "ok"];
    }

    function useRandomMove() {

        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $province = validateID($_POST["province"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        $EmptyPlace = LWorld::getEmptyPlace($province);
        $Unit = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $EmptyPlace[0]["x"], "y" => $EmptyPlace[0]["y"]]);
        $City = selectFromTable("x, y, lvl", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);

        if (!count($City)) return ["state" => "error_no_city"];
        if (!count($Unit) || !count($EmptyPlace))
                return ["state" => "error_no_place"];
        if ($Unit[0]["ut"] != WUT_EMPTY)
                return ["state" => "error_no_place_empty"];
        if (!LItem::useItem("random_move", $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        updateTable("t = 0 ,ut = 0, s = 0 , l = 0", "world", "x = :x AND  y = :y", ["x" => $City[0]["x"], "y" => $City[0]["y"]]);
        updateTable("ut = :t, l = :l, s = 0", "world", "x = :x AND y = :y", ["x" => $EmptyPlace[0]["x"], "y" => $EmptyPlace[0]["y"], "t" => WUT_CITY_LVL_0 + $City[0]["lvl"], "l" => $City[0]["lvl"]]);
        updateTable("x = :x, y = :y", "city", "id_city = :idc", ["x" => $EmptyPlace[0]["x"], "y" => $EmptyPlace[0]["y"], "idc" => $idCity]);
        updateTable("x_coord = :x, y_coord = :y", "world_unit_garrison", "x_coord = :xo AND y_coord = :yo", ["x" => $EmptyPlace[0]["x"], "y" => $EmptyPlace[0]["y"], "xo" => $City[0]["x"], "yo" => $City[0]["y"]]);
        (new LWebSocket())->send(json_encode(["url" => "World/refreshWorldUnit", "data" => [
                "Units" => [
                    ["x" => $EmptyPlace[0]["x"], "y" => $EmptyPlace[0]["y"]],
                    ["x" => $City[0]["x"], "y" => $City[0]["y"]]
                ]
        ]]));
        return ["state" => "ok"];
    }

    function useCertainMove() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $newX = validateID($_POST["newX"]);
        $newY = validateID($_POST["newY"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);
        $Unit = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $newX, "y" => $newY]);
        $City = selectFromTable("x, y, lvl", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]);

        if (!count($City)) return ["state" => "error_no_city"];
        if (!count($Unit)) return ["state" => "error_no_place"];
        if ($Unit[0]["ut"] != WUT_EMPTY)
                return ["state" => "error_no_place_empty"];
        if (!LItem::useItem("certain_move", $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        updateTable("t = 0 ,ut = 0, s = 0 , l = 0", "world", "x = :x AND  y = :y", ["x" => $City[0]["x"], "y" => $City[0]["y"]]);
        updateTable("ut = :t, l = :l", "world", "x = :x AND y = :y", ["x" => $newX, "y" => $newY, "t" => WUT_CITY_LVL_0 + $City[0]["lvl"], "l" => $City[0]["lvl"]]);
        updateTable("x = :x, y = :y", "city", "id_city = :idc", ["x" => $newX, "y" => $newY, "idc" => $idCity]);
        updateTable("x_coord = :x, y_coord = :y", "world_unit_garrison", "x_coord = :xo AND y_coord = :yo", ["x" => $newX, "y" => $newY, "xo" => $City[0]["x"], "yo" => $City[0]["y"]]);
        (new LWebSocket())->send(json_encode(["url" => "World/refreshWorldUnit", "data" => [
                "Units" => [
                    ["x" => $newX, "y" => $newY],
                    ["x" => $City[0]["x"], "y" => $City[0]["y"]]
                ]
        ]]));
        return ["state" => "ok"];
    }

    function useWheat() {
        global $idPlayer;
        $lastTime = selectFromTable("wheat", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["wheat"];
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "wheat_1")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $lastTime + 60 * 60 * 24 * $amount);
        if ($Item == "wheat_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $lastTime + 60 * 60 * 24 * 7 * $amount);
        updateTable("wheat = :nt", "player_stat", "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        $City = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one):
            LSaveState::saveCityState($one["id_city"]);
            LSaveState::resInState($one["id_city"], "food");
        endforeach;

        return ["state" => "ok", "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]];
    }

    function useStone() {
        global $idPlayer;
        $lastTime = selectFromTable("stone", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["stone"];
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();

        if ($Item == "stone_1")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $lastTime + 60 * 60 * 24 * $amount);
        if ($Item == "stone_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $lastTime + 60 * 60 * 24 * 7 * $amount);
        updateTable("stone = :nt", "player_stat", "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        $City = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one):
            LSaveState::saveCityState($one["id_city"]);
            LSaveState::resInState($one["id_city"], "stone");
        endforeach;

        return ["state" => "ok", "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]];
    }

    function useWood() {
        global $idPlayer;
        $lastTime = selectFromTable("wood", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["wood"];
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "wood_1")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $lastTime + 60 * 60 * 24 * $amount);
        if ($Item == "wood_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $lastTime + 60 * 60 * 24 * 7 * $amount);
        updateTable("wood = :nt", "player_stat", "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        $City = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one):
            LSaveState::saveCityState($one["id_city"]);
            LSaveState::resInState($one["id_city"], "wood");
        endforeach;

        return ["state" => "ok", "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]];
    }

    function useMetal() {
        global $idPlayer;
        $lastTime = selectFromTable("metal", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["metal"];
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "metal_1")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $lastTime + 60 * 60 * 24 * $amount);
        if ($Item == "metal_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $lastTime + 60 * 60 * 24 * 7 * $amount);
        updateTable("metal = :nt", "player_stat", "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        $City = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one):
            LSaveState::saveCityState($one["id_city"]);
            LSaveState::resInState($one["id_city"], "metal");
        endforeach;

        return ["state" => "ok", "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]];
    }

    function useCoin() {
        global $idPlayer;
        $lastTime = selectFromTable("coin", "player_stat", "id_player = :idp", ["idp" => $idPlayer])[0]["coin"];
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $idCity = validateID($_POST["idCity"]);

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $newTime = time();
        if ($Item == "coin_1")
                $newTime = max($newTime + 60 * 60 * 24 * $amount, $lastTime + 60 * 60 * 24 * $amount);
        if ($Item == "coin_7")
                $newTime = max($newTime + 60 * 60 * 24 * 7 * $amount, $lastTime + 60 * 60 * 24 * 7 * $amount);
        updateTable("coin = :nt", "player_stat", "id_player = :idp", ["nt" => $newTime, "idp" => $idPlayer]);
        $City = selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $idPlayer]);
        foreach ($City as $one):
            LSaveState::saveCityState($one["id_city"]);
            LSaveState::coinInState($one["id_city"]);
        endforeach;

        return ["state" => "ok", "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]];
    }

    function useGoldPack() {
        global $idPlayer;
        $Item = validateGameNames($_POST["Item"]);
        $amount = validateID($_POST["amount"]);
        $goldAmount = [
            "gold_1" => 1, "gold_5" => 5, "gold_10" => 10,
            "gold_25" => 25, "gold_75" => 75, "gold_100" => 100,
            "gold_500" => 500, "gold_1000" => 1000
        ];

        if (!LItem::useItem($Item, $amount))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if ($amount <= 0)
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if (!isset($goldAmount[$Item]))
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        updateTable("gold = gold + :g", "player", "id_player = :idp", ["g" => $goldAmount[$Item] * $amount, "idp" => $idPlayer]);
        return [
            "state" => "ok"
        ];
    }

}
