<?php

class ACityJop
{
    function getCityJop()
    {
        global $idPlayer;
        $idCity   = validateID($_GET["idCity"]);
        
        return selectFromTable("*", "city_jop", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0];
        
        
    }
    
    function updateJopProductionRate()
    {
        global $idPlayer;
        $idCity    = validateID($_POST["idCity"]);
        $foodRate  = validateID($_POST["foodRate"]);
        $woodRate  = validateID($_POST["woodRate"]);
        $stoneRate = validateID($_POST["stoneRate"]);
        $metalRate = validateID($_POST["metalRate"]);
        if(!is_numeric($foodRate)  || $foodRate  > 100 || $foodRate  < 0) return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!is_numeric($woodRate)  || $woodRate  > 100 || $woodRate  < 0) return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!is_numeric($stoneRate) || $stoneRate > 100 || $stoneRate < 0) return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!is_numeric($metalRate) || $metalRate > 100 || $metalRate < 0) return ["state" => "error_0", "TryToHack" => TryToHack()];

        LSaveState::saveCityState($idCity);

        $upParm =[
            "fr"=> $foodRate, "wr"=> $woodRate, "sr"=> $stoneRate,
            "mr"=> $metalRate, "idc"=> $idCity, "idp"=> $idPlayer
        ];


        updateTable("food_rate = :fr, wood_rate = :wr, stone_rate = :sr, metal_rate = :mr", "city_jop", "id_city =  :idc AND id_player = :idp", $upParm);

        LSaveState::resInState($idCity, "food");
        LSaveState::resInState($idCity, "wood");
        LSaveState::resInState($idCity, "stone");
        LSaveState::resInState($idCity, "metal");

        return [

            "state"=>"ok",
            "city"=> selectFromTable("food,wood,stone,metal,coin,food_in,wood_in,stone_in,metal_in", "city", "id_city = :idc", ["idc"=>$idCity])[0],
            "cityJop"=> selectFromTable("*", "city_jop", "id_city = :idc", ["idc"=>$idCity])[0]
        ];
        
    }
    
    function hire()
    {
        global $idPlayer;
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $idCity        = validateID($_POST["idCity"]);
        $amountToHire  = validateID($_POST["amountToHire"]);
        $JopPlace      = LCity::getBuildingAtPlace($buildingPlace, $idCity);
        $UnitReq       = CJop::$JopReq[$JopPlace["Place"]];
        
        if($amountToHire <= 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!$UnitReq)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if(!isset(CJop::$JOP_AVAIL_PLACE[$JopPlace["Lvl"]]) || CJop::$JOP_AVAIL_PLACE[$JopPlace["Lvl"]] < $amountToHire)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        $UnitReq["food"]  *= $amountToHire; $UnitReq["wood"]  *= $amountToHire;
        $UnitReq["stone"] *= $amountToHire; $UnitReq["metal"] *= $amountToHire;
        $totalTime = $amountToHire*($UnitReq["time"] - $JopPlace["Lvl"]);
        unset($UnitReq["condetion"]);
        unset($UnitReq["time"]);
        unset($UnitReq["produce"]);
        if(!LCity::isResourceTaken($UnitReq, $idCity))
            return ["state" => "error_2"];
        
        
        $now = time();
        insertIntoTable(
                "id_city = :idc, id_player = :idp, jop_place = :jp, num = :a, time_end = :te, time_start = :ts, time_end_org = :teo", 
                "city_jop_hiring",
                ["idp" => $idPlayer, "idc" => $idCity, "jp" => $JopPlace["Place"], "a" => $amountToHire, "te" => $now + $totalTime, "ts" => $now, "teo" => $now + $totalTime]);
        LSaveState::saveCityState($idCity);
        return [
            "state" => "ok",
            "JopTaskList" => selectFromTable("*", "city_jop_hiring", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]),
            "cityRes"     => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function cancelHiring()
    {
        global $idPlayer;
        $idTask  = validateID($_POST["idTask"]);
        $JopTask = selectFromTable("id_city, jop_place, num", "city_jop_hiring", "id = :idt AND id_player = :idp", ["idt" => $idTask, "idp" => $idPlayer]);
        
        if(!count($JopTask))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $UnitReq           = CJop::$JopReq[$JopTask[0]["jop_place"]];
        $UnitReq["food"]  *= $JopTask[0]["num"] * JOP_CANCELING_GAIN_RATE; 
        $UnitReq["wood"]  *= $JopTask[0]["num"] * JOP_CANCELING_GAIN_RATE;
        $UnitReq["stone"] *= $JopTask[0]["num"] * JOP_CANCELING_GAIN_RATE; 
        $UnitReq["metal"] *= $JopTask[0]["num"] * JOP_CANCELING_GAIN_RATE;
        unset($UnitReq["condetion"]);
        unset($UnitReq["time"]);
        unset($UnitReq["produce"]);
        LSaveState::saveCityState($JopTask[0]["id_city"]);
        LCity::addResource($UnitReq, $JopTask[0]["id_city"]);
        deleteTable("city_jop_hiring", "id = :id AND id_player = :idp", ["id" => $idTask, "idp" => $idPlayer]);
        
        return [
            "state"       => "ok",
            "JopTaskList" => selectFromTable("*", "city_jop_hiring", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]),
            "cityRes"     => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function speedUpHiring()
    {
        global $idPlayer;
        $idTask     = validateID($_POST["idTask"]);
        $itemToUse  = validateGameNames($_POST["itemToUse"]);
        $JopTask    = selectFromTable("id_city, jop_place, num", "city_jop_hiring", "id = :idt AND id_player = :idp", ["idt" => $idTask, "idp" => $idPlayer]);
        $now = time();
        
        if(!count($JopTask))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        if($itemToUse == "polit_a") $equation = "time_end - 15*60";
        elseif($itemToUse == "polit_b") $equation = "time_end - 60*60";
        elseif($itemToUse == "polit_c") $equation = "time_end - 3*60*60";
        elseif($itemToUse == "polit_d") $equation = "time_end - (time_end - $now)*0.3";
        else return ["state"  => "error_2", "TryToHack" => TryToHack()]; 
        
        if(!LItem::useItem($itemToUse))
            return ["state" => "error_1"];
        
        updateTable("time_end = $equation", "city_jop_hiring", "id = :id AND id_player = :idp", ["id" => $idTask, "idp" => $idPlayer]);
        
        return [
            "state" => "ok",
            "JopTaskList" => selectFromTable("*", "city_jop_hiring", "id_city = :idc AND id_player = :idp", ["idc" => $JopTask[0]["id_city"], "idp" => $idPlayer])
        ];
    }
    
    function fireLabor()
    {
        
        global $idPlayer;
        $idCity        = validateID($_POST["idCity"]);
        $buildingPlace = validateGameNames($_POST["buildingPlace"]);
        $amountToFire  = validateGameNames($_POST["amountToFire"]);
        $JopPlace      = LCityBuilding::getBuildingAtPlace($buildingPlace, $idCity);
        $UnitReq       = CJop::$JopReq[$JopPlace["Place"]];
        
        if($amountToFire <= 0)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!$UnitReq)
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        $JopNum        = selectFromTable($UnitReq["produce"], "city_jop", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        
        if(!count($JopNum) || !$JopNum[0][$UnitReq["produce"]] || $JopNum[0][$UnitReq["produce"]] < $amountToFire)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        $UnitReq["food"]  *= $amountToFire; $UnitReq["wood"]  *= $amountToFire;
        $UnitReq["stone"] *= $amountToFire; $UnitReq["metal"] *= $amountToFire;
        
        updateTable("{$UnitReq["produce"]} = {$UnitReq["produce"]} - $amountToFire", "city_jop", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
        
        unset($UnitReq["condetion"]);
        unset($UnitReq["time"]);
        unset($UnitReq["produce"]);
        
        LSaveState::saveCityState($idCity);
        
        LCity::addResource($UnitReq, $idCity);
        
        return [
            "state" => "ok",
            "cityJop" => selectFromTable("*", "city_jop", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0],
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    
    }
}

