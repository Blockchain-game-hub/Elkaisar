<?php

class APlayerEdu
{
    
    function getPlayerEduLvl()
    {
        global $idPlayer;
        return
                selectFromTable("*", "player_edu", "id_player = :idp", ["idp" => $idPlayer])[0];
        
    }
    
    function upgradeStudyLvl()
    {
        global $idPlayer;
        $idStudy       = validateGameNames($_POST["idStudy"]);
        $idCity        = validateID($_POST["idCity"]);
        $Study         = selectFromTable("player_edu.`$idStudy` AS studyLvl", "player_edu", "id_player = :idp", ["idp" => $idPlayer]);
        $StudyReq      = LEdu::fulfillCondition($idCity, ["Type" => $idStudy, "Lvl" => $Study[0]["studyLvl"]]);
        $countSTP      = selectFromTable("COUNT(*) AS c", "study_tasks", "id_player = :idp AND study =  :s", ["idp" => $idPlayer,  "s" => $idStudy])[0]["c"];
        $countSTC      = selectFromTable("COUNT(*) AS c", "study_tasks", "id_city   = :idc AND study_in  = :s", ["idc" => $idCity, "s" => $StudyReq["study_in"]])[0]["c"];
        
        if(!count($Study))              return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($Study[0]["studyLvl"] >= 30) return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($StudyReq == false)          return ["state" => "error_2", "TryToHack" => TryToHack()];
        if($countSTP > 0)               return ["state" => "error_3", "TryToHack" => TryToHack()];
        if($countSTC > 0)               return ["state" => "error_4", "TryToHack" => TryToHack()];
        
        $timeRequired = json_decode($StudyReq["up_req"], true)["time"];
        $TotalTime = time() + $timeRequired;
        insertIntoTable(
                "id_city = :idc ,  id_player = :idp , study = :st ,  time_start = :ts , lvl_to = :lt , time_end = :te, time_end_org = :tor, study_in = :si",
                "study_tasks",
                [
                    "idc"=> $idCity, "idp" => $idPlayer, "st" => $idStudy, "ts" => time(),
                    "lt"=>$Study[0]["studyLvl"] + 1, "te"=> $TotalTime, "tor"=> $TotalTime, "si" => $StudyReq["study_in"]
                ]);
        
        return [
            "state" => "ok",
            "list"  => selectFromTable("*", "study_tasks", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer]),
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $idCity])[0]
        ];
    }
    
    function cancelStudyUpgarding()
    {
        global $idPlayer;
        $idTask     = validateID($_POST["idTask"]);
        $StudyTask  = selectFromTable("*", "study_tasks", "id = :idt AND id_player = :idp", ["idt" => $idTask, "idp" => $idPlayer]);
        $Study      = selectFromTable("*", "study", "id_study = :ids AND lvl = :l", ["l" => $StudyTask[0]["lvl_to"] - 1, "ids" => $StudyTask[0]["study"]]);
        $gainRes    = json_decode($Study[0]["up_req"], true);
        unset($gainRes["condetion"]);
        unset($gainRes["time"]);
        
        LSaveState::saveCityState($StudyTask[0]["id_city"]);
        if(!count($StudyTask))
            return ["state" => "error_0"];
        if(deleteTable("study_tasks", "id_player = :idp AND id = :ids", ["idp" => $idPlayer, "ids" => $idTask]))
            LCity::addResource ($gainRes, $StudyTask[0]["id_city"]);
        
        return [
            "state"   => "ok",
            "list"    => selectFromTable("*", "study_tasks", "id_city = :idc AND id_player = :idp", ["idc" => $StudyTask[0]["id_city"], "idp" => $idPlayer]),
            "cityRes" => selectFromTable("*", "city", "id_city = :idc", ["idc" => $StudyTask[0]["id_city"]])[0]
        ];
    }
    
    function speedUpStudyTask()
    {
        global $idPlayer;
        $idTask    = validateID($_POST["idTask"]);
        $itemToUse = validateGameNames($_POST["itemToUse"]);
        $idCity    = selectFromTable("id_city", "study_tasks", "id = :id AND id_player = :idp", ["id" => $idTask, "idp"=> $idPlayer]);
        $now = time();
        
        if(!count($idCity))
            return ["state" => "error_0"];
        
        if($itemToUse     == "archim_a") $equation = "time_end = time_end - 15*60";
        elseif($itemToUse == "archim_b") $equation = "time_end = time_end - 60*60";
        elseif($itemToUse == "archim_c") $equation = "time_end = time_end - 3*60*60";
        elseif($itemToUse == "archim_d") $equation = "time_end = time_end - (time_end - $now)*0.3";
        else return ["state" => "error_2", "TryToHack" => TryToHack()]; 
        
        if(!LItem::useItem($itemToUse))
            return ["state" => "error_1"];
        
        updateTable($equation, "study_tasks", "id = :id AND id_player = :idp", ["id" => $idTask, "idp" =>$idPlayer]);
        
        return [
            "state"   => "ok",
            "list"    => selectFromTable("*", "study_tasks", "id_city = :idc AND id_player = :idp", ["idc" => $idCity[0]["id_city"], "idp" => $idPlayer])
        ];
    
    }
}
