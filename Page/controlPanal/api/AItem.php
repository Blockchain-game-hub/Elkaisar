<?php

class AItem {
    
    function getItemData(){
        return selectFromTable("*", "item", "1");
    }
    function getItemBoxOpen(){
        return selectFromTable("*", "item_box_open", "1");
    }
    
    function saveItemProp(){
        
        $ItemPrice          = validateID($_POST["ItemPrice"]);
        $ItemStartingAmount = validateID($_POST["ItemStartingAmount"]);
        $ItemSelectPlace    = validateGameNames($_POST["ItemSelectPlace"]);
        $ItemMaxPrize       = validateID($_POST["ItemMaxPrize"]);
        $idItem             = validateID($_POST["idItem"]);
        
        updateTable(
                "gold = :g, tab = :t, startingAmount = :sa, prizeLimit = :pl", 
                "item", "id_item = :id", [
                    "g"  => $ItemPrice,
                    "t"  => $ItemSelectPlace, 
                    "sa" => $ItemStartingAmount,
                    "pl" => $ItemMaxPrize,
                    "id" => $idItem
                ]);
        
    }
    
    
    
    function addPrizeToItem(){
        
        $idItem        = validateGameNames($_POST["idItem"]);
        $idItemPrize   = validateGameNames($_POST["idItemPrize"]);
        $winRate       = validateID($_POST["winRate"]);
        $itemAmountMax = validateID($_POST["itemAmountMax"]);
        $itemAmountMin = validateID($_POST["itemAmountMin"]);
        
        insertIntoTable(
                "id_item = :idi, id_item_prize = :idp, amount_min = :min, amount_max = :max, win_rate = :wr",
                "item_box_open", [
                    "idi" => $idItem, 
                    "idp" => $idItemPrize,
                    "min" => $itemAmountMin,
                    "max" => $itemAmountMax,
                    "wr"   => $winRate
                ]);
    }
    
    
    
    function removePrize(){
        
        $idPrize = validateID($_POST["idPrize"]);
        deleteTable("item_box_open", "id_prize = :idp", ["idp" => $idPrize]);
        
    }
    
    
    function addPrizeEquipToItem(){
        $idItem        = validateGameNames($_POST["idItem"]);
        $idEquipPrize   = validateGameNames($_POST["idEquipPrize"]);
        $winRate       = validateID($_POST["winRate"]);
        $itemAmountMax = validateID($_POST["itemAmountMax"]);
        $itemAmountMin = validateID($_POST["itemAmountMin"]);
        insertIntoTable(
                "id_item = :idi, id_item_prize = :idp, amount_min = :min, amount_max = :max, win_rate = :wr, prize_type = 'E'",
                "item_box_open", [
                    "idi" => $idItem, 
                    "idp" => $idEquipPrize,
                    "min" => $itemAmountMin,
                    "max" => $itemAmountMax,
                    "wr"   => $winRate
                ]);
        
        
    }
    
    
    function getItemPlayerRank(){
        $idItem        = validateGameNames($_GET["idItem"]);
        
        return selectFromTable(
                "player_item.*, player.name",
                "player_item JOIN player ON player.id_player = player_item.id_player",
                "player_item.id_item = :idi ORDER BY player_item.amount DESC LIMIT 30", ["idi" => $idItem]);
    }
    
    function setServerAmount(){
        $idItem = validateGameNames($_POST["idItem"]);
        $amount = validateID($_POST["amount"]);
        updateTable("amount = :a", "player_item", "id_item = :idi", ["a" => $amount, "idi" => $idItem]);
       
    }
}