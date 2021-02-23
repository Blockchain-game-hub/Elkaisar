<?php

class LItem
{
    static function useItem($Item, $amount = 1, $playerId = 0)
    {
        global $idPlayer;
        if($playerId == 0)
            $playerId = $idPlayer;
        
        if($amount <= 0)
            return false;
        
        if(!static::isEnough($Item, $amount, $playerId ))
            return false;
        
        updateTable("amount = amount - :a", "player_item", "id_player = :idp AND id_item = :idt", ["a" => $amount, "idp" =>$playerId, "idt" => $Item]);
        return true;
    
    }
    
    static function addItem($item, $amount, $playerId = 0)
    {
        global $idPlayer;
        if($amount <= 0)
            return ;
        if($playerId == 0)
            $playerId = $idPlayer;
        
        updateTable("amount = amount + :a", "player_item","id_player = :idp AND id_item = :idt", ["a" => $amount, "idp" =>$playerId, "idt" => $item]);
    }

    static function getAmount($item , $playerId = 0)
    {
        global $idPlayer;
        if($playerId == 0)
            $playerId = $idPlayer;
        
        $itemCount = selectFromTable("amount", "player_item", "id_item = :idt AND id_player = :idp", ["idt" => $item, "idp" => $playerId]);
        if(!count($itemCount))
            return 0;
        return $itemCount[0]["amount"];
    }
    
    static function isEnough($item , $amount, $playerId = 0)
    {
        global $idPlayer;
        if($playerId == 0)
            $playerId = $idPlayer;
        
        if($amount <= 0)
            return false;
        return (static::getAmount($item, $idPlayer) >= $amount);
    }
    
}

