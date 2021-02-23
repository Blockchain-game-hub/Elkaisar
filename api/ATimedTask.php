<?php

class ATimedTask
{
    
    function getAllTasks()
    {
        global $idPlayer;
        $Tasks    = [];
        $Tasks["Building"] = selectFromTable("*", "city_worker", "id_player = :idp", ["idp" => $idPlayer]);
        $Tasks["Study"]    = selectFromTable("*", "study_tasks", "id_player = :idp", ["idp" => $idPlayer]);
        $Tasks["Army"]     = selectFromTable("*", "build_army", "id_player = :idp", ["idp" => $idPlayer]);
        $Tasks["Jop"]      = selectFromTable("*", "city_jop_hiring", "id_player = :idp", ["idp" => $idPlayer]);
        return ( $Tasks );
        
    }
    
    function getCityBuildingTasks()
    {
        
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "city_worker", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
    }
    function getCityStudyTasks()
    {
        
        global $idPlayer;
         $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "study_tasks", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
    }
    function getCityArmyTasks()
    {
        
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "build_army", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
    }
    
    function getCityJopTasks()
    {
        
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return selectFromTable("*", "city_jop_hiring", "id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
    }
    
}
