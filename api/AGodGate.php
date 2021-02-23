<?php

class AGodGate
{
    
    function getGodGateData()
    {
        
        global $idPlayer;
        $godGateData = selectFromTable("*", "god_gate", "id_player = :idp", ["idp" => $idPlayer]);
        $godGate = [];
        
        if(!count($godGateData))
            return [];
        
        $g_1 = selectFromTable("*", "god_gate_1", "id_player = :idp", ["idp"=>$idPlayer]);
        $g_2 = selectFromTable("*", "god_gate_2", "id_player = :idp", ["idp"=>$idPlayer]);
        $g_3 = selectFromTable("*", "god_gate_3", "id_player = :idp", ["idp"=>$idPlayer]);
        $g_4 = selectFromTable("*", "god_gate_4", "id_player = :idp", ["idp"=>$idPlayer]);

        $godGate["GodGate1"] = count($g_1) > 0? $g_1[0] : NULL;
        $godGate["GodGate2"] = count($g_2) > 0? $g_2[0] : NULL;
        $godGate["GodGate3"] = count($g_3) > 0? $g_3[0] : NULL;
        $godGate["GodGate4"] = count($g_4) > 0? $g_4[0] : NULL;
    
        $godGate["GodGateData"] = $godGateData[0];
        return $godGate;

    }
    
    
    function getGodGateRankCount()
    {
        
        return [
            "gate1" => selectFromTable("COUNT(*) AS c", "god_gate_1", "1")[0]["c"],
            "gate2" => selectFromTable("COUNT(*) AS c", "god_gate_2", "1")[0]["c"],
            "gate3" => selectFromTable("COUNT(*) AS c", "god_gate_3", "1")[0]["c"],
            "gate4" => selectFromTable("COUNT(*) AS c", "god_gate_4", "1")[0]["c"]
        ];

    }
    
    function openGate()
    {
        global $idPlayer;
        
        $gateIndex = validateID($_POST["gateIndex"]);
        $godGate = selectFromTable("god_gate.points , player.porm",
            "player JOIN god_gate ON player.id_player = god_gate.id_player",
            "player.id_player = :idp", ["idp"=>$idPlayer]);
        $godGateData = CGodGate::$GateData[("gate_".$gateIndex)];
        
        if(!count($godGate))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($godGateData))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($godGate[0]["porm"] < $godGateData["porm"])
            return ["state" => "error_2", "porm" => $godGateData["porm"], "TryToHack" => TryToHack()];
        if($godGate[0]["points"] < $godGateData["points"])
            return ["state" => "error_3", "points" => $godGateData["points"], "TryToHack" => TryToHack()];
        
        $p_1 = rand(1, 20);
        $p_2 = rand(1, 20);
        $p_3 = rand(1, 20);

        $quary = "cell_1_type =  'vit' , cell_2_type = 'attack' , cell_3_type = 'damage',"
                . "cell_1_score = $p_1, cell_2_score = $p_2, cell_3_score = $p_3 , id_player = :idp";
        
        insertIntoTable($quary, "`god_gate_$gateIndex`", ["idp"=>$idPlayer]);
        
        updateTable("`gate_$gateIndex` = $p_1 + $p_2 + $p_3", "god_gate", "id_player = :idp", ["idp" => $idPlayer]);
        updateTable("points = points - {$godGateData["points"]}", "god_gate", "id_player = :idp", ["idp"=>$idPlayer]);
        
        return [
            "state"      => "ok",
            "Gate"       => selectFromTable("*", "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=>$idPlayer])[0],
            "PlayerGate" => selectFromTable("*", "god_gate", "id_player = :idp", ["idp" => $idPlayer])[0],
            "score"      => $p_1 + $p_2 + $p_3
        ];
        
        
    }
    
    
    function changeGateCellState()
    {
        
        global $idPlayer;
        $gateIndex = validateID($_POST["gateIndex"]);
        $cellIndex = validateID($_POST["cellIndex"]);
        $state     = validateID($_POST["state"]);
        
        updateTable("`c_".$cellIndex."_s` = '$state'", "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=>$idPlayer]);
        
        return [
            "state" => "ok",
            "Gate" => selectFromTable("*", "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=>$idPlayer])[0]
        ];
    
    }
    
    
    function changeGateUnlockedCells()
    {
        global $idPlayer;
        $gateIndex = validateID($_POST["gateIndex"]);
    
        $godGate = selectFromTable("*", "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=>$idPlayer]);
        $playerPoint = selectFromTable("points", "god_gate", "id_player = :idp", ["idp"=>$idPlayer])[0]["points"];
        if(!count($godGate))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
    
        $lockCells = 0;
        $lockedTypes = [];

        for($iii = 1 ; $iii <= 3; $iii++){

            if($godGate[0][("c_".$iii."_s")] == 0){
                $lockCells++;
                $lockedTypes[] = $godGate[0][("cell_".$iii."_type")];
            }

        }



        $reqPoints = 50 + 50*$lockCells;

        if($playerPoint <  $reqPoints)
            return [
                "state"=>"error_2",
                "point" => $reqPoints
                
            ];
            
    
        $totalScore = 0;


        for($iii = 1; $iii <= 3; $iii++){

            if($godGate[0][("c_".$iii."_s")] == 0){

                $totalScore += floor(($godGate[0][("cell_".$iii."_score")]/CGodGate::$MaxVal[$godGate[0][("cell_".$iii."_type")]])*100);
                continue;
            }

            $unlockedTyps = array_diff(["vit","attack","defence","damage"], $lockedTypes);
            $newType = array_rand($unlockedTyps);
            $lockedTypes[] = $unlockedTyps[$newType];

            $godGate[0][("cell_".$iii."_type")] = $unlockedTyps[$newType];

            $score = 0;

            $randLH = rand(0, 100);
            if($randLH > 97){
                rand(floor(CGodGate::$MaxVal[$unlockedTyps[$newType]]*0.97), CGodGate::$MaxVal[$unlockedTyps[$newType]]);
            }else if($randLH > 80){
                $score =  rand(1, floor(CGodGate::$MaxVal[$unlockedTyps[$newType]]*0.97));
            }else if($randLH > 71){
                $score = rand(1, floor(CGodGate::$MaxVal[$unlockedTyps[$newType]]*0.7));
            }else{
                $score = rand(1, floor(CGodGate::$MaxVal[$unlockedTyps[$newType]]*0.5));
            }


            $godGate[0][("cell_".$iii."_score")] = $score;
            $totalScore += floor(($score/CGodGate::$MaxVal[$godGate[0][("cell_".$iii."_type")]])*100);


        }

        $quary = "cell_1_type = '{$godGate[0]["cell_1_type"]}' , cell_2_type = '{$godGate[0]["cell_2_type"]}' ,"
        . " cell_3_type = '{$godGate[0]["cell_3_type"]}' ,  cell_4_type = '{$godGate[0]["cell_4_type"]}' , "
        . " cell_1_score = {$godGate[0]["cell_1_score"]} ,  cell_2_score = {$godGate[0]["cell_2_score"]} ,"
        . " cell_3_score = {$godGate[0]["cell_3_score"]} ,  cell_4_score = {$godGate[0]["cell_4_score"]}  ";


        updateTable("points  = points - $reqPoints, `gate_$gateIndex` = $totalScore", "god_gate", "id_player = :idp", ["idp"=>$idPlayer]);

        updateTable($quary, "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=>$idPlayer]);

        
        return [
                "state"   => "ok",
                "Gate"    => selectFromTable("*", "`god_gate_$gateIndex`", "id_player = :idp", ["idp"=> $idPlayer])[0],
                "PlayerGate" => selectFromTable("*", "god_gate", "id_player = :idp", ["idp"=> $idPlayer])[0]
                
            ];
    
        
    }
    
    function addGatePoints()
    {
        
        global $idPlayer;
        $Item  = validateGameNames($_POST['Item']);
        $amount   = validateID($_POST['amount']);


        if($amount <= 0)
            return ["state" => "error_0"];

        $pointToAdd = 0;

        if($Item == "god_point_5")
        $pointToAdd = 5;
        elseif($Item == "god_point_30")
        $pointToAdd = 30;
        elseif($Item == "god_point_75")
        $pointToAdd = 75;
        elseif($Item == "god_point_175")
        $pointToAdd = 175;
        elseif($Item == "god_point_750")
        $pointToAdd = 750;
        elseif ($Item == "god_point_1k")
        $pointToAdd = 1000;
        elseif ($Item == "god_point_2k")
        $pointToAdd = 2000;
        elseif ($Item == "god_point_5k")
        $pointToAdd = 5000;
        elseif ($Item == "god_point_10k")
        $pointToAdd = 10000;
        elseif ($Item == "god_point_50k")
        $pointToAdd = 50000;
        else {
            print_r($_POST);    
        }

        
        if(!LItem::useItem($Item, $amount))
            return ["state" => "error_1", "TryToHack" => TryToHack()];


        $pointToAdd *= $amount;

        updateTable("points = points + $pointToAdd", "god_gate", "id_player = :idp", ["idp"=>$idPlayer]);

        

        return [
            "state"=>"ok",
            "PlayerGate" => selectFromTable("*", "god_gate", "id_player = :idp", ["idp"=>$idPlayer])[0],
            "PointToAdd" => $pointToAdd
        ];
        
    }
    
    
    function getGateRank()
    {
        
        $gateIndex = validateID($_GET["gateIndex"]);
        $offset    = validateID($_GET["offset"]);
        
        $quary = " god_gate JOIN player ON player.id_player = god_gate.id_player";
    
        return selectFromTable( "player.name AS PlayerName, player.avatar, player.porm, player.id_player AS idPlayer, god_gate.`gate_$gateIndex` AS score", $quary, "1 ORDER BY god_gate.`gate_$gateIndex` DESC, player.prestige DESC LIMIT 10 OFFSET $offset");
        
    }
    
    
    function getGodGateRankPointPlus()
    {
        return CGodGate::$RankPointPluse;
        
    }
    
}
