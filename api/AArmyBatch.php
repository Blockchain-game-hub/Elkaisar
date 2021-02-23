<?php

class AArmyBatch
{
    
    function cancelBatch(){   
        
        global $idPlayer;
        $idBatch  = validateID($_POST["idBatch"]);
        
        $Batch = selectFromTable("*", "build_army", "id = :idb AND id_player = :idp",["idp" => $idPlayer, "idb" => $idBatch]);
        $timeStart = time();
        if(!count($Batch))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $allNextBatches  = selectFromTable("*", "build_army", "id > :idb AND place = :pl AND id_player = :idp AND id_city = :idc", ["idb" => $idBatch, "pl" => $Batch[0]["place"], "idp" => $idPlayer, "idc" => $Batch[0]["id_city"]]);
        $oneBeforeDelete = selectFromTable("*", "build_army", "id_city = :idc AND place = :pl AND id_player = :idp AND id < :idb LIMIT 1", ["idc" => $Batch[0]["id_city"], "pl" => $Batch[0]["place"], "idb" => $idBatch, "idp" => $idPlayer]);
        
        if(count($oneBeforeDelete))
            $timeStart = $oneBeforeDelete[0]["time_end"];
        
        foreach ($allNextBatches as $oneBatch){
            $timeEnd = $timeStart + ($oneBatch["time_end"] - $oneBatch["time_start"]);
            updateTable("time_start = :ts , time_end = :te", "build_army", "id = :i", ["ts" => $timeStart, "te" => $timeEnd, "i" => $oneBatch["id"]]);
            $timeStart += ($oneBatch["time_end"] - $oneBatch["time_start"]);
        }
        
        $this->gainFromCanceling($Batch[0]["id_city"], $Batch[0]["army_type"], $Batch[0]["amount"]);
        
        deleteTable("build_army", "id = :idb AND id_player = :idp", ["idb" => $idBatch, "idp" => $idPlayer]);
        return[
            "state" =>"ok",
            "armyBatches" => selectFromTable("*", "build_army", "id_city = :idc AND id_player = :idp AND place = :pl", ["idc" => $Batch[0]["id_city"], "idp" => $idPlayer, "pl" => $Batch[0]["place"]]),
            "cityRes"     => selectFromTable("*", "city", "id_city = :idc", ["idc" => $Batch[0]["id_city"]])[0]
        ];
        
    }
    
    private function gainFromCanceling($idCity, $armyType, $amount)
    {
        $armyGainRes = CArmy::$ResourcseNeeded[$armyType];
        unset($armyGainRes["pop"]);
        unset($armyGainRes["time"]);
        unset($armyGainRes["condetion"]);
        foreach ($armyGainRes as &$one){
            $one = $one*ARMY_CANCELING_GAIN_RATE*$amount;
        }
        LSaveState::saveCityState($idCity);
        LCity::addResource($armyGainRes, $idCity);
        
    }
    
    
    function speedUpBatches()
    {
        global $idPlayer;
        $idBatch = validateID($_POST["idBatch"]);
        $itemToUse = validateGameNames($_POST["itemToUse"]);
        $speedUpFact = 0;      
        $Batch = selectFromTable("*", "build_army", "id = :idb AND id_player = :idp",["idp" => $idPlayer, "idb" => $idBatch]);
        if(!count($Batch))
            return ["state" =>"error_0", "TryToHack" => TryToHack()];
        if($Batch[0]["acce"])
            return ["state" =>"error_1", "TryToHack" => TryToHack()];
        
        if($itemToUse == "train_acce_30")
            $speedUpFact = ARMY_SPEED_UP_FACT_MIN;
        else if($itemToUse == "train_acce_50")
            $speedUpFact = ARMY_SPEED_UP_FACT_MAX;
        else if($Batch[0]["place"] == "wall" && $itemToUse = "wall_acce")
            $speedUpFact = ARMY_SPEED_UP_FACT_MAX;
        else 
            return  ["state" =>"error_2", "TryToHack" => TryToHack()];
        
        if(!LItem::useItem($itemToUse, 1))
            return ["state" =>"error_3", "TryToHack" => TryToHack()];
        
        $Batch[0]["SpeedUpFact"] = $speedUpFact;
        return $this->goAndSpeedUp($Batch[0]);
    
    }
    
    private function goAndSpeedUp($Batch)
    {
        global $idPlayer;
        $commonBlocks = LArmy::getBlockToSpeedUp($Batch["place"]);
        
        foreach ($commonBlocks as $onePlace):

            $BatchesInBuilding = selectFromTable("*", "build_army", "id_city = :idc AND id_player = :idp  AND place = :pl AND acce = 0", ["idc" => $Batch["id_city"], "idp" => $idPlayer, "pl" => $onePlace]);

            $this->speedUpOnePlace($BatchesInBuilding, $Batch["SpeedUpFact"]);

        endforeach;
        
        return [
            "state" => "ok",
            "armyBatches" => 
            selectFromTable(
                    "id, time_end, time_start, acce",
                    "build_army", 
                    "id_city = :idc AND id_player = :idp AND place IN (".implode(",", array_map(function($e){return "'$e'";}, $commonBlocks)).")", 
                    ["idp" => $idPlayer, "idc" => $Batch["id_city"]])
        ];
        
    }
    
    private function speedUpOnePlace($Batches, $speedUpFactor)
    {
        
        $newDuration = 0;
        $now = time();
        $timeStart = $now;
        
        for ($index = 0; $index < count($Batches); $index++):
            if($Batches[$index]["acce"] == 1){
               $timeStart = $Batches[$index]["time_end"];
            }else{
                $newDuration = ($Batches[$index]["time_end"] - max($Batches[$index]["time_start"], $now)) - (($Batches[$index]["time_end"] - max($Batches[$index]["time_start"], $now))*$speedUpFactor);
                updateTable("time_start = :ts, time_end = :te, acce = 1", "build_army", "id = :idb", ["ts" => $timeStart, "te" => $timeStart + $newDuration, "idb" => $Batches[$index]["id"]]);
                $timeStart += $newDuration;

            }
        endfor;
        
    }
}
