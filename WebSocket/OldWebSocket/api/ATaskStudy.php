<?php


class ATaskStudy
{
    function Finish()
    {
        $Now = time() + 1;
        $TimeFinishedTask = selectFromTable("*", "study_tasks", "time_end <= :t", ["t" => time() + 1]);
        if(!count($TimeFinishedTask))
            return [];
        
        $Last = $TimeFinishedTask[count($TimeFinishedTask) - 1];
        deleteTable("study_tasks", "time_end <= :i", ["i" => $Now]);
        $Tasks = [];
        
        foreach ($TimeFinishedTask as &$oneTask)
        {
            
            $GainPres = LPrestige::eduGainPrestige($oneTask["study"], $oneTask["lvl_to"] - 1);
            LPrestige::addPres($oneTask["id_player"], $GainPres);
            updateTable("exp = exp + :e", "hero", "id_hero = (SELECT console FROM city WHERE id_city = :idc)", ["e" =>$GainPres*2, "idc" => $oneTask["id_city"]]);
            
            updateTable("`{$oneTask["study"]}` = :lt", "player_edu", "id_player = :idp", ["lt" => $oneTask["lvl_to"], "idp" => $oneTask["id_player"]]);
            
            $Tasks[] = [
                "Task" => $oneTask,
                "Edu" => selectFromTable("*", "player_edu", "id_player = :idp", ["idp" => $oneTask["id_player"]])[0],
                "prestige" => $GainPres
            ];
        }
        
        return $Tasks;
        
    }
    
}


