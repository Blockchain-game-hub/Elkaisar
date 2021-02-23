<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

class ASendPrize {

    function sendPrizeToPlayer() {

        $Players = json_decode($_POST["Players"], true);
        $Prizes = json_decode($_POST["Prizes"], true);
        $PlayersId = array_column($Players, "id_player");
        $PlayersPrize = [];

        foreach ($Prizes as $onePrize) {

            if ($onePrize["type"] == "matrial") {
                $OldPrize = selectFromTable("amount, id_item, id_player", "player_item", "id_player IN(" . implode(",", $PlayersId) . ") AND id_item = '{$onePrize["matrial"]}'");
                foreach ($OldPrize as $one) {
                    if (!isset($PlayersPrize[$one["id_player"]]))
                            $PlayersPrize[$one["id_player"]] = [];

                    $PlayersPrize[$one["id_player"]][] = [
                        "type" => "Item",
                        "Item" => $onePrize["matrial"],
                        "old" => $one["amount"],
                        "new" => $one["amount"] + $onePrize["amount"]
                    ];
                }
                updateTable("amount = amount + :a", "player_item", "id_player IN(" . implode(",", $PlayersId) . ") AND id_item = '{$onePrize["matrial"]}'", ["a" => $onePrize["amount"]]);
            } else {
                $Equip = explode("_", $onePrize["idEquip"]);

                foreach ($PlayersId as $one) {
                    if (!isset($PlayersPrize[$one]))
                            $PlayersPrize[$one] = [];
                    $amount = selectFromTable("COUNT(*) AS amount", "equip", "id_player = $one AND type = '{$Equip[0]}' AND part = '{$Equip[1]}'  AND lvl = '{$Equip[2]}'")[0]["amount"];
                    $PlayersPrize[$one][] = [
                        "type" => "Equip",
                        "idEquip" => $onePrize["idEquip"],
                        "old" => $amount,
                        "new" => $amount + $onePrize["amount"]
                    ];
                }

                for ($iii = 0; $iii < $onePrize["amount"]; $iii++) {
                    queryExe("INSERT INTO equip(id_player, type , part, cat, lvl) "
                            . " SELECT id_player , '{$Equip[0]}' , '{$Equip[1]}', 'main', '{$Equip[2]}' FROM player WHERE id_player IN (" . implode(",", $PlayersId) . ")");
                }
            }
        }

        $this->saveSentPrizes($PlayersPrize);

        return [
            "state" => "ok"
        ];
    }
    
    
    

    function saveSentPrizes($PlayersPrize) {
        $idBatch = selectFromTable("MAX(id_batch) AS m", "cp_send_p_his", "1")[0]["m"] + 1;
        foreach ($PlayersPrize as $idPlayer => $onePlayer) {
            insertIntoTable("prize = :p, id_player = :idp, id_batch = :idb", "cp_send_p_his", ["p" => json_encode($onePlayer, JSON_UNESCAPED_SLASHES), "idp" => $idPlayer, "idb" => $idBatch]);
        }
    }

    function sendPrizeToServer() {

        $prizes = json_decode(($_POST["prizes"]), true);
        $pormStart = (($_POST["pormStart"]));
        $pormEnd = (($_POST["pormEnd"]));
        $prestigeStart = (($_POST["prestigeStart"]));
        $prestigeEnd = (($_POST["prestigeEnd"]));
        
        $conPrestige = NULL;

        if (is_numeric($prestigeStart) && $prestigeStart > 0) {

            if (is_numeric($prestigeEnd) && $prestigeEnd > $prestigeStart && $prestigeEnd > 0) {

                $conPrestige = " prestige BETWEEN $prestigeStart AND $prestigeEnd";
            } else {

                $conPrestige = " prestige >= $prestigeStart ";
            }
        }

        $conPorm = NULL;

        if (is_numeric($pormStart) && $pormStart > -1) {

            $conPorm = is_null($conPrestige) ? "" : " AND ";


            if (is_numeric($pormEnd) && $pormEnd > $pormStart) {

                $conPorm .= " porm  BETWEEN $pormStart AND $pormEnd";
            } else {

                $conPorm .= " porm  >= $pormStart ";
            }
        }

        if (is_null($conPorm) && is_null($conPrestige)) {

            $con = "1";
        } else {

            $con = $conPrestige . " " . $conPorm;
        }

        return $this->sendToServerByCon($prizes, $con);
    }

    private function sendToServerByCon($prizes, $Con) {
        
       
        
        foreach ($prizes as $onePrize) {
            if ($onePrize["type"] == "matrial") {
                updateTable("amount = amount + :a", "player_item", "id_player IN( SELECT id_player FROM player WHERE $Con) AND id_item = '{$onePrize["matrial"]}'", ["a" => $onePrize["amount"]]);
            } else if ($onePrize["type"] == "equip") {
                $Equip = explode("_", $onePrize["idEquip"]);

                for ($iii = 0; $iii < $onePrize["amount"]; $iii++) {

                    queryExe("INSERT INTO equip(id_player, type , part, cat, lvl) SELECT id_player , '{$Equip[0]}' , '{$Equip[1]}' , 'main', '{$Equip[2]}' FROM player WHERE $Con")['count'];
                }
            }
        }
        return [
            "state" => "ok",
            "PlayerCount" => selectFromTable("COUNT(*) AS c", "player", $Con)[0]["c"]
        ];
    }
    
    
    function sendPrizeToOnline(){
        
        $Players = selectFromTable("id_player", "player", "online = 1");
        $Prizes = json_decode($_POST["Prizes"], true);
        $PlayersId = array_column($Players, "id_player");
        
        foreach ($Prizes as $onePrize) {

            if ($onePrize["type"] == "matrial") {
                updateTable("amount = amount + :a", "player_item", "id_player IN(" . implode(",", $PlayersId) . ") AND id_item = :p", ["p" => $onePrize["matrial"], "a" => $onePrize["amount"]]);
            } else {
                $Equip = explode("_", $onePrize["idEquip"]);
                for ($iii = 0; $iii < $onePrize["amount"]; $iii++) {
                    queryExe("INSERT INTO equip(id_player, type , part, cat, lvl) "
                            . " SELECT id_player , '{$Equip[0]}' , '{$Equip[1]}', 'main', '{$Equip[2]}' FROM player WHERE id_player IN (" . implode(",", $PlayersId) . ")");
                }
            }
        }
        
        return [
            "state" => "ok",
            "PlayerCount" => count($Players)
        ];
    }

}
