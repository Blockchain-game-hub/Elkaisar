<?php

class APlayerQuest
{
    
    function getPlayerQuest()
    {
        global $idPlayer;
        return 
                selectFromTable("*", "quest_player", "id_player = :idp", ["idp" => $idPlayer]);
        
    }
    
    
    function acceptQuest()
    {
        global $idPlayer;
        $idQuest     = validateGameNames($_POST["idQuest"]);
        $Quest       = selectFromTable("*", "quest", "id_quest = :idq", ["idq" => $idQuest]);
        $playerQuest = selectFromTable("*", "quest_player", "id_player = :idp AND id_quest = :idq", ["idp" => $idPlayer, "idq" => $idQuest]);
        $ListOfNeed  = json_decode($Quest[0]["ListOfNeed"], true);
        $Reword      = json_decode($Quest[0]["Reword"], true);
        
        if(!count($Quest))
            return ["state" => "error_0", TryToHack()];
        if(!count($playerQuest))
            return ["state" => "error_1", TryToHack()];
        if($playerQuest[0]["done"] != 0)
            return ["state" =>"error_2", TryToHack()];
        
        if(!$this->verifyListOfNeed($ListOfNeed))
            return ["state" =>"error_3", TryToHack()];
        
        if(!$this->takeListOfNeed($ListOfNeed))
            return ["state" =>"error_4", TryToHack()];
        
        $this->giveReword($Reword);
        updateTable("done = 1", "quest_player", "id_player = :idp AND id_quest = :idq", ["idp" => $idPlayer, "idq" => $idQuest]);
        return ["state" => "ok"];
        
    }
    
    
    private function verifyListOfNeed($listOfNeed)
    {
        $idCity   = validateID($_POST["idCity"]);
        global $idPlayer;
        $player = selectFromTable("porm , prestige, gold", "player", "id_player = $idPlayer");
        if(!count($player)) return false;
        
        foreach ($listOfNeed as $one):
            
            if($one["type"] == "promotion"){
                if($player[0]["porm"] < $one["promotion"]) return false; 
            }else if($one["type"]  == "item"){
                if(LItem::getAmount($one["item"]) < $one["amount"])  return false;
            }else if($one["type"]   == 'prestige'){
                if($player[0]["prestige"] < $one["amount"]) return FALSE;
            }else if($one["type"] == "gold") {
                if($player[0]["gold"] < $one["amount"]) return FALSE;
            }else if($one["type"] == "building"){
                $Building = LCityBuilding::buildingWithHeighestLvl($idCity, $one["buildingType"]);
                if($Building["Lvl"] < $one["lvl"])  return false;
            }else if($one["type"] == "resource"){
                if(!LCity::isResourceEnough([$one["resourceType"] => $one["amount"]], $idCity))
                    return false;
            }else if($one["type"] == "population"){
                if(selectFromTable("pop", "city", "id_city = :idc", ["idc" => $idCity])[0]["pop"] < $one["amount"])
                    return false;
            }else if($one["type"] == "jop"){
                if(selectFromTable($one["jopFor"], "city_jop", "id_city = :idc", ["idc" => $idCity])[0][$one["jopFor"]] < $one["amount"])
                    return false;
            }else if($one["type"] == "playerState"){
                if(selectFromTable($one["stateFor"], "player_state", "id_player = :idp", ["idp" => $idPlayer])[0][$one["stateFor"]] < time())
                    return false;
            }
            
        endforeach;
        
        return true;
    }
    
    private function takeListOfNeed($listOfNeed)
    {
        $idCity   = validateID($_POST["idCity"]);
        foreach ($listOfNeed as $one):
            
             if($one["type"]  == "item"){
                if(!LItem::useItem($one["item"], $one["amount"]))
                    return false;
            } else if($one["type"] == "gold") {
                if(!LPlayer::tekeGold($one["amount"]))
                    return FALSE;
            } else if($one["type"] == "resource"){
                if(!LCity::isResourceTaken([$one["resourceType"] => $one["amount"]], $idCity))
                    return false;
            }

        endforeach;
        
        return true;
        
    }
    
    private function giveReword($Reword)
    {
        global $idPlayer;
        $idCity   = validateID($_POST["idCity"]);
        foreach ($Reword as $one){
            
            if($one["type"] == "item")
                LItem::addItem($one["item"], $one["amount"]);
            else if($one["type"] == "prestige")
                LPlayer::addPrestige($one["amount"]);
            else if($one["type"] == "resource")
                LCity::addResource([$one["resourceType"] => $one["amount"]], $idCity);
            else if($one["type"] == "population")
                updateTable("pop = pop + :a", "city", "id_city = :idc AND id_player = :idp", ["a" => $one["amount"], "idc" => $idCity, "idp" => $idPlayer]);
            else if($one["type"] == "equip")
                for($ii = 0; $ii < $one["amount"]; $ii++){
                    LEquip::addEquip($one["equip"], $one["part"], $one["lvl"]);
                }
            else if($one["type"] == "jop")
                updateTable("`{$one["jopFor"]}` = `{$one["jopFor"]}` + :a", "city_jop", "id_city = :idc", ["a" => $one["amount"], "idc" => $idCity]);
            else if($one["type"] == "promotion")
                updateTable("porm =  porm + 1", "player", "id_player = :idp AND porm < 30", ["idp" => $idPlayer]) ;
            else
                TryToHack();
        }
        
    }
}
