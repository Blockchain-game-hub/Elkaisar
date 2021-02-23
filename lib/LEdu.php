<?php

class LEdu
{
    static function isConditionsTrue($idCity, $Study)
    {
        
        $lvlReq = selectFromTable("*", "study", "id_study = :ids AND lvl = :l", ["ids" => $Study["Type"], "l" => $Study["Lvl"]]);
        if(!count($lvlReq))
            return false;
        $condetion = json_decode($lvlReq[0]["up_req"], true);
        
        foreach ($condetion["condetion"] as $one)
        {
            if($one["type"] == "building")
                if(LCityBuilding::buildingWithHeighestLvl($idCity, $one["buildingType"])["Lvl"] < $one["lvl"])
                {
                   
                    return false;
                }
                    
            if($one["type"] == "population")
                if(selectFromTable("pop", "city", "id_city = :idc",["idc" => $idCity])[0]["pop"] < $one["amount"])
                    return false;
            if($one["type"] == "item")
                if(LItem::getAmount($one["item"]) < $one["amount"])
                    return false;
            
        }
        return true;
    }
    
    static function fulfillCondition($idCity, $Study)
    {
       
        if(!static::isConditionsTrue($idCity, $Study))
            return false;
        
        
        $lvlReq = selectFromTable("*", "study", "id_study = :ids AND lvl = :l", ["ids" => $Study["Type"], "l" => $Study["Lvl"]]);
        
        $condetion = json_decode($lvlReq[0]["up_req"], true);
        $ReqRes = $condetion;
        
        unset($ReqRes["condetion"]);
        unset($ReqRes["time"]);
        
        if(!LCity::isResourceTaken($ReqRes, $idCity))
            return false;
        
        foreach ($condetion["condetion"] as $one)
        {
            if($one["type"] == "item")
                if(!LItem::useItem($one["item"], $one["amount"]))
                    return false;
            
        }
        return $lvlReq[0];
        
    }
    
}

