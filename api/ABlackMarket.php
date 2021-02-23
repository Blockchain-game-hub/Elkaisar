<?php

class ABlackMarket {

    function getItemList() {

        $offset = validateID($_GET["offset"]);

        return selectFromTable(
                "buy_item.* , player.name AS p_name",
                "buy_item JOIN player ON player.id_player = buy_item.id_player",
                " 1 LIMIT 10 OFFSET $offset"
        );
    }

    function getItemCount() {

        return ["Count" => selectFromTable("COUNT(*)  AS item_count", "buy_item", "1")[0]["item_count"]];
    }

    function getItemBlackList() {
        return ["BlackList" => selectFromTable("*", "buy_item_black_list", "1")];
    }

    function buyItem() {
        global $idPlayer;
        $amount = validateID($_POST["amount"]);
        $idItem = validateID($_POST["idItem"]);
        $Item = selectFromTable("*", "buy_item", "id_item = :it", ["it" => $idItem]);
        $BuyerGold = selectFromTable("gold", "player", "id_player = :idp", ["idp" => $idPlayer])[0]["gold"];

        if ($amount <= 0)
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (count($Item))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($Item[0]["amount"] < $amount)
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if ($BuyerGold < $amount * $Item[0]["price"])
                return ["state" => "error_3", "TryToHack" => TryToHack()];
        if (!LItem::useItem("buy_voucher", $amount))
                return ["state" => "error_4", "TryToHack" => TryToHack()];
        updateTable("gold = gold - :g", "player", "id_player = :idp AND porm >= 5 ", ["g" => $amount * $Item[0]["price"], "idp" => $idPlayer]);
        updateTable("gold = gold + :g", "player", "id_player = :idp", ["g" => $amount * $Item[0]["price"], "idp" => $Item[0]['id_player']]);
        if ($amount == $Item[0]["amount"])
                deleteTable("buy_item", "id_item = :idt", ["idt" => $idItem]);
        else
                updateTable("amount = amount - :a", "buy_item", "id_item = :idt", ["a" => $amount, "idt" => $idItem]);

        insertIntoTable(
                "id_buyer = :idb, id_seller = :ids, amount = :a, old_amount = :oa, item = :i, gold = :g, unit_price = :up, total_price = :tp",
                "buy_item_history",
                [
                    "idb" => $idPlayer, "ids" => $Item[0]["id_player"], "a" => $amount,
                    "oa" => $Item[0]["amount"], "i" => $Item[0]["item"], "g" => $BuyerGold,
                    "up" => $Item[0]["price"], ["tp"] => $amount * $Item[0]["price"]
                ]
        );
        return ["state" => "ok"];
    }

}
