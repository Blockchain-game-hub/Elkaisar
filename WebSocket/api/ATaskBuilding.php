<?php

class ATaskBuilding
{
    
    function Finish()
    {
        $Now =  time() + 1;
        $TimeFinishedTask = selectFromTable("*", "city_worker", "time_end <= :t", ["t" => time() + 1]);
        if(!count($TimeFinishedTask))
            return [];
        
        $last = $TimeFinishedTask[count($TimeFinishedTask) - 1];
        
        deleteTable("city_worker", "time_end <= :id", ["id"=> $Now]);
        
        
        foreach ($TimeFinishedTask as &$oneTask)
        {
            if($oneTask["lvl_to"] > 30) continue;
            
            updateTable("`{$oneTask["place"]}` = :lvlTo", "city_building_lvl", 
                "id_city = :idc AND id_player = :idp",
                ["idc" => $oneTask["id_city"], "idp" => $oneTask["id_player"], "lvlTo" => $oneTask["lvl_to"]]);
            
            if($oneTask["lvl_to"] == 0)
                updateTable("`{$oneTask["place"]}` = 0", "city_building", 
                "id_city = :idc AND id_player = :idp",
                ["idc" => $oneTask["id_city"], "idp" => $oneTask["id_player"]]);
                
            LBuilding::buildingUpgraded($oneTask);
            
        }
        
        return $TimeFinishedTask;
    }
    
}

