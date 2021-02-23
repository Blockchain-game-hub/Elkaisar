<?php

class ATaskArmy
{
    
    function Finish()
    {
        global $idPlayer;
        $Now = time() + 1;
        $ArmyTasks = selectFromTable("*", "build_army", "time_end <= :t", ["t" => $Now]);
        if(!count($ArmyTasks))
            return [];
        
        deleteTable("build_army", "time_end <= :i", ["i" => $Now]);
        $Tasks = [];
        foreach ($ArmyTasks as &$oneTask)
        {
            
            $idPlayer = $oneTask["id_player"];


            $GainPres = LPrestige::armyGainPrestige($oneTask["army_type"], $oneTask["amount"]);
            LPrestige::addPres($oneTask["id_player"], $GainPres);
            
            updateTable("exp = exp + :e", "hero", "id_hero = (SELECT console FROM city WHERE id_city = :idc)", ["e" =>$GainPres*2, "idc" => $oneTask["id_city"]]);
            updateTable("`{$oneTask["army_type"]}` = `{$oneTask["army_type"]}` + :a", "city", "id_city = :idc", ["a" => $oneTask["amount"], "idc" => $oneTask["id_city"]]);
            
            LSaveState::saveCityState($oneTask["id_city"]);
            LSaveState::foodOutState($oneTask["id_city"]);
            $Tasks[] = [
                "Task" => $oneTask,
                "City" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $oneTask["id_city"]])[0],
                "prestige" => $GainPres
            ];
           
        }
        
        return $Tasks;
        
    }
    
}
