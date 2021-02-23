<?php

class AExchange {

    function getCurrentExchange() {

        return selectFromTable("*", "exchange", "1");
    }

    function removeExchange() {

        $idEx = validateID($_POST["idEx"]);

        deleteTable("exchange", "id_ex = :ie", ["ie" => $idEx]);
    }

    function addExchangeItem() {

        $player_porm = validateID($_POST["player_porm"]);
        $player_max  = validateID($_POST["player_max"]);
        $server_max  = validateID($_POST["server_max"]);
        $max_to_have = validateID($_POST["max_to_have"]);
        $reword      = ($_POST["reword"]);
        $req         = ($_POST["req"]);
        $cat         = validateGameNames($_POST["cat"]);

        if (!$server_max || $server_max < 1) {
            $server_max = "NULL";
        }

        $quary = "req = :r , reword = :rew , player_max = :im , "
                . "server_max = $server_max , player_porm = :pp, cat = :cat ,"
                . "max_to_have = :mth";

        insertIntoTable($quary, "exchange", ["r" => $req, "rew" => $reword, "im" => $player_max, "pp" => $player_porm, "cat" => $cat, "mth" => $max_to_have]);
        queryExe("INSERT IGNORE INTO exchange_player(id_trade , id_player) SELECT exchange.id_ex , player.id_player FROM player JOIN exchange WHERE 1");
    }

}
