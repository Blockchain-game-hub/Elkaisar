<?php

class AContribute
{
    
    function upgradeEquip()
    {
        global $idPlayer;
        $idCont  = validateGameNames($_POST["idCont"]);
        $idEquip = validateID($_POST["idEquip"]);
        $Cont    = selectFromTable("*", "contribute", "id = :id", ["id" => $idCont]);
        $Equip   = selectFromTable("*", "equip", "id_equip =:idq AND id_player = :idp", ["idp" => $idPlayer, "idq" => $idEquip]);
        
        if(!count($Cont))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($Equip))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Equip[0]["id_hero"] > 0 || !is_null($Equip[0]["id_hero"]))
            return  ["state" => "error_2", "idHero" => $Equip[0]["id_hero"],"TryToHack" => TryToHack()];
        
        $Reword = json_decode($Cont[0]["Reword"], true);
        
        if(!count($Reword) || $Reword[0]["type"] != "equip")
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        if($Reword[0]["part"] != $Equip[0]["part"] || $Reword[0]["equip"] != $Equip[0]["type"])
            return ["state" => "error_4", "TryToHack" => TryToHack()];
        
        $ListOfNeed = json_decode($Cont[0]["ListOfNeed"], true);
        if(!$this->verifyListOfNeed($ListOfNeed, $Equip[0]))
            return ["state" => "error_5"]; 
        if(!$this->takeListOfNeed($ListOfNeed))
            return ["state" => "error_6"]; 
        
        updateTable("lvl = :l", "equip", "id_player = :idp AND id_equip = :idq", ["idp" => $idPlayer, "idq" => $idEquip, "l" => $Reword[0]["lvl"]]);
        
        return [
            "state" => "ok",
            "Equip" => selectFromTable("*", "equip", "id_equip = :idq", ["idq" => $idEquip])[0]
        ];
        
    }
    
    
    function contribute()
    {
        $idCont  = validateGameNames($_POST["idCont"]);
        $Cont    = selectFromTable("*", "contribute", "id = :id", ["id" => $idCont]);
        if(!count($Cont))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $ListOfNeed = json_decode($Cont[0]["ListOfNeed"], true);
        $Reword = json_decode($Cont[0]["Reword"], true);
        
        if(!$this->verifyListOfNeed($ListOfNeed))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!$this->takeListOfNeed($ListOfNeed))
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        $this->giveReword($Reword);
        
        return [
            "state" => "ok"
        ];
    }


    
    private function verifyListOfNeed($listOfNeed, $Equip = [])
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
            }else if($one["type"] == "equip"){
                if($Equip["part"] != $one["part"] || $Equip["type"] != $one["equip"] || $Equip["lvl"] != $one["lvl"] )
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
            else if($one["type"] == "equip")
                for($ii = 0; $ii < $one["amount"]; $ii++){
                    LEquip::addEquip($one["equip"], $one["part"], $one["lvl"]);
                }
            else
                TryToHack();
        }
        
    }
}
