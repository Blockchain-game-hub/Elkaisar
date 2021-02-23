<?php

class ASpy
{
    
    function start()
    {
        
        global $idPlayer;
        $spyNum = validateID($_POST["spyNum"]);
        $idCity = validateID($_POST["idCity"]);
        $xCoord = validateID($_POST["xCoord"]);
        $yCoord = validateID($_POST["yCoord"]);
        
        $City = selectFromTable("spies, x, y", "city", "id_city = :idc", ["idc" => $idCity]);
        $Unit = selectFromTable("*", "world", "x = :x AND y = :y", ["x" => $xCoord, "y" => $yCoord]);
        
        if(!count($Unit) || !count($City))
            return ["state" => "error_0", "TryTOHack" => TryToHack()];
        if($City[0]["spies"] < $spyNum)
            return ["state" => "error_1", "TryTOHack" => TryToHack()];
        
        
        if(LWorldUnit::isCity($Unit[0]["ut"]))
            $spyOn =  "city";
        else
            $spyOn = "barrary";
        

        
        $distance    =  LWorldUnit::calDist($City[0]["x"], $xCoord, $City[0]["y"], $yCoord);
        $timeArrive = time() +  $distance/2000;
        updateTable("spies = spies - :s", 'city', "id_city = :idc", ["s" => $spyNum, "idc" => $idCity]);


        $quary = "id_player = :idp , id_city = :idc , x_to = :xt , "
            . "y_to = :yt , spy_num = :sn , time_arrive = :ta ,"
            . " spy_on = :so";
        insertIntoTable($quary, "spy", [
            "idp" => $idPlayer, "idc" => $idCity, "xt" => $xCoord, "yt" => $yCoord, "sn" => $spyNum,
            "ta" => $timeArrive, "so" => $spyOn
        ]);
        LSaveState::saveCityState($idCity);
        return ([
            "state"=>"ok",
            "City"=> selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ]);
    
    }
    
    
    function cancel()
    {
        
        global $idPlayer;
        $idSpy = validateID($_POST["idSpy"]);
        $idCity = validateID($_POST["idCity"]);
        
        deleteTable("spy", "id_spy = :ids AND id_player = :idp", ["ids" => $idSpy, "idp" => $idPlayer]);
        LSaveState::saveCityState($idCity);
        return [
            "state" => "ok",
            "City" => selectFromTable("*", "city", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity])[0]
        ];
    
    }
}

