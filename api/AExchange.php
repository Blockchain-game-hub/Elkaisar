<?php

class AExchange
{
    
    function getExchangeItem()
    {
        global $idPlayer;


        return
                selectFromTable(
                        "exchange.*, exchange_player.take_times",
                        "exchange JOIN exchange_player ON exchange.id_ex = exchange_player.id_trade", 
                        "exchange_player.id_player = :idp", ["idp" => $idPlayer]
                        );
    }
    
    function buyExchange()
    {
        global $idPlayer;
        $idExchange     = validateID($_POST["idExchange"]);
        $idCity         = validateID($_POST["idCity"]);
        $amountToTrade  = validateID($_POST["amountToTrade"]);
        $Exchnage       = selectFromTable( "exchange.*, exchange_player.take_times",
                        "exchange JOIN exchange_player ON exchange.id_ex = exchange_player.id_trade", 
                        "exchange_player.id_player = :idp AND exchange.id_ex = :idx", ["idp" => $idPlayer, "idx" => $idExchange] );
        $Reword         = json_decode($Exchnage[0]["reword"], true);
        $Req            = json_decode($Exchnage[0]["req"], true);
        
        if(!count($Exchnage))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($Reword) || !count($Req))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!$this->canHaveMore($Exchnage[0], $Reword, $amountToTrade))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        if(!$this->verifyReq($Exchnage[0], $amountToTrade, $idCity))
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        $rewordAmount = 1;
        if(isset ($Reword["amount"]))
            $rewordAmount = $Reword["amount"];
        
        if($Reword["type"] === "matrial")
            LItem::addItem($Reword["matrial"], $amountToTrade*$rewordAmount);
        else if($Reword["type"] === "equip")
            for($iii = 0; $iii < $amountToTrade ; $iii++){
                for($ii  = 0; $ii < $rewordAmount; $ii++){
                    LEquip::addEquip ($Reword["Equip"], $Reword['Part'], $Reword["lvl"]);
                }
            }
        
        updateTable("server_take = server_take + :a", "exchange", "id_ex = :idx", ["idx" => $idExchange, "a" => $amountToTrade]);
        updateTable("take_times  = take_times  + :a", "exchange_player", "id_trade = :idx AND id_player = :idp", ["idx"=>$idExchange, "idp"=>$idPlayer, "a" => $amountToTrade]);
        
        return [
            "state" => "ok",
            "Exchange" => selectFromTable("*", "exchange_player", "id_player = :idp", ["idp" => $idPlayer])
        ];
    
    }
    
    private function canHaveMore($Exchnage, $Reword, $Amount)
    {
        global $idPlayer;
        
        if($Reword["type"] === "matrial")
            $playerAmount = LItem::getAmount($Reword["matrial"]);
        else if($Reword["type"] === "equip")
            $playerAmount = selectFromTable( "COUNT(*) AS c", "equip",   "id_player = :idp AND part = :p AND type = :e", ["idp" =>$idPlayer, "p"=>$Reword['Part'], "e"=>$Reword["Equip"]])[0]["c"];
    
        
        if($playerAmount + $Amount > $Exchnage["max_to_have"])
            return false;
        
        else  if($Exchnage["server_take"] + $Amount > $Exchnage["server_max"] && !is_null($Exchnage["server_max"]))
            return false;

        else if($Exchnage["take_times"] + $Amount > $Exchnage["player_max"])
            return false;
        
        return true;
        
    }
    
    private function verifyReq($Exchnage, $amountToTrade, $idCity)
    {
        global $idPlayer;
        $Req  = json_decode($Exchnage["req"], true);
        if(!count($Req))
            return false;
        
        LSaveState::saveCityState($idCity);
        
        foreach ($Req  as $one){
            $a =  $one["amount"] * $amountToTrade;
            if($one["type"] == "matrial"){
                if(!LItem::useItem($one["matrial"], $a))
                    return false;
            }else if($one["type"] === "resource"){
                if(!LCity::isResourceTaken([$one["resource_type"] => $a], $idCity))
                    return false;
            }else if($one["type"] == "equip"){
                if(deleteTable("equip", "id_player = :idp AND part = :p AND type = :t AND lvl = :l AND on_hero = 0 LIMIT $a", ["idp"=>$idPlayer, "p"=>$one["Part"], "t"=>$one["Equip"], "l" => $one["lvl"]]) < 1)
                    return false;
            }else if($one["type"] == "gold"){
                if(!LPlayer::tekeGold($a))
                    return false;
            }
            
        }
        
        return true;
        
    }
    
}



//echo json_encode(selectFromTable("*", "exchange", "1"), JSON_PRETTY_PRINT |  JSON_NUMERIC_CHECK  | JSON_UNESCAPED_SLASHES);