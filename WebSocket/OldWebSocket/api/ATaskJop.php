<?php

class ATaskJop
{
    
    function Finish()
    {
        global $idPlayer;
        $Now = time() + 1;
        $TimeFinishedTask = selectFromTable("*", "city_jop_hiring", "time_end <= :t", ["t" => $Now]);
        if(!count($TimeFinishedTask))
            return [];
        
        
        deleteTable("city_jop_hiring", "time_end <= :id", ["id"=> $Now]);
        $Tasks = [];
        
        foreach ($TimeFinishedTask as $oneTask)
        {
            $idPlayer = $oneTask["id_player"];
            $GainPres = LPrestige::jopGainPrestige($oneTask["num"]);
            LPrestige::addPres($oneTask["id_player"], $GainPres);
            updateTable("exp = exp + :e", "hero", "id_hero = (SELECT console FROM city WHERE id_city = :idc)", ["e" =>$GainPres*2, "idc" => $oneTask["id_city"]]);
            
            $Produce = CJop::$JopReq[$oneTask["jop_place"]]["produce"];
            updateTable("`$Produce` = `$Produce` + :a", "city_jop", "id_city = :idc", ["idc" =>  $oneTask["id_city"], "a" => $oneTask["num"]]);
            LSaveState::resInState($oneTask["id_city"], $Produce);
            
            $Tasks[] = [
                "Task"     => $oneTask,
                "City"     => selectFromTable("*", "city", "id_city = :idc", ["idc" => $oneTask["id_city"]])[0],
                "CityJop"  => selectFromTable("*", "city_jop", "id_city = :idc", ["idc" =>  $oneTask["id_city"]])[0],
                "prestige" => $GainPres
            ];
            
        }
        
        return $Tasks;
        
    }
    
}

