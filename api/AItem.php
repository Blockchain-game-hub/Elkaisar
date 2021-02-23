<?php

class AItem {

    function getAllItemPrice() {
        return selectFromTable("*", "item", "1");
    }

    function buyItem() {
        global $idPlayer;
        $Item = validateGameNames($_POST["item"]);
        $amount = validateID($_POST["amount"]);
        $itemPrice = selectFromTable("*", "item", "id_item = :ii", ["ii" => $Item]);

        if ($amount <= 0)
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!count($itemPrice))
                return ["state" => "error_1", "TryToHack" => TryToHack()];
        if ($itemPrice[0]["gold"] <= 0)
                return ["state" => "error_2", "TryToHack" => TryToHack()];
        if (!LPlayer::tekeGold($amount * $itemPrice[0]["gold"]))
                return ["state" => "error_3", "TryToHack" => TryToHack()];

        LItem::addItem($Item, $amount);

        return [
            "state" => "ok",
            "PlayerItem" => selectFromTable("*", "player_item", "id_player = :idp", ["idp" => $idPlayer])
        ];
    }

    function openItemBox() {

        global $idPlayer;
        $idItem = validateGameNames($_POST["idItem"]);
        $Item = selectFromTable("*", "item", "id_item = :idi", ["idi" => $idItem]);

        if (!count($Item))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        if (!LItem::useItem($idItem))
                return ["state" => "error_1", "TryToHack" => TryToHack()];

        $itemOpenPrize = selectFromTable("*", "item_box_open", "id_item = :idi ORDER BY RAND()", ["idi" => $idItem]);
        $PlayerPrize = [];
        foreach ($itemOpenPrize as $one) {

            if (count($PlayerPrize) >= $Item[0]["prizeLimit"]) break;


            $luck = rand(0, 1000);

            if ($one["prize_type"] == "I") {
                if ($luck <= $one["win_rate"]) {
                    $amount = rand($one["amount_min"], $one["amount_max"]);
                    LItem::addItem($one["id_item_prize"], $amount, $idPlayer);
                    $PlayerPrize[] = [
                        "Item" => $one["id_item_prize"],
                        "amount" => $amount,
                        "prizeType" => $one["prize_type"]
                    ];
                }
            } else if ($one["prize_type"] == "E") {
                
                if ($luck <= $one["win_rate"]) {
                    
                    $amount = rand($one["amount_min"], $one["amount_max"]);
                    $Equip = explode("_", $one["id_item_prize"]);
                    for($iii = 0; $iii < $amount; $iii++){
                        LEquip::addEquip($Equip[0], $Equip[1], $Equip[2]);
                    }
                        
                    $PlayerPrize[] = [
                        "Item" => $one["id_item_prize"],
                        "amount" => $amount,
                        "prizeType" => $one["prize_type"]
                    ];
                }
            }
        }

        return [
            "state" => "ok",
            "Item" => $PlayerPrize
        ];
    }

}
