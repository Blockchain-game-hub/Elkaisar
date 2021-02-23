<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';

    DbConnect(4);
    
    $Acad = selectFromTable("*", "edu_acad", "1");
    foreach ($Acad as $oneAcad)
    {
        $StudyArr = [];
        foreach ($oneAcad as $Study => $lvl)
        {
            
            if($Study == "id_player")
                continue;
            $StudyArr[] = "$Study = $lvl";
        }
        
        updateTable(implode(",", $StudyArr), "player_edu", "id_player = :idp", ["idp" => $oneAcad["id_player"]]);
        echo "One Acade Done For Player {$oneAcad["id_player"]}\n";
    }
    
    
    $Acad = selectFromTable("*", "edu_uni", "1");
    foreach ($Acad as $oneAcad)
    {
        $StudyArr = [];
        foreach ($oneAcad as $Study => $lvl)
        {
            
            if($Study == "id_player")
                continue;
            $StudyArr[] = "$Study = $lvl";
        }
        
        updateTable(implode(",", $StudyArr), "player_edu", "id_player = :idp", ["idp" => $oneAcad["id_player"]]);
        echo "One Uni Done For Player {$oneAcad["id_player"]}\n";
    }
    


