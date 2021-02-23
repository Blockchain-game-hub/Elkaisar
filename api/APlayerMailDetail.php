<?php

class APlayerMailDetail
{
    
    function getIncomMail()
    {
        global $idPlayer;
        $idMessage    = validateID($_GET["idMessage"]);
        $Message = selectFromTable("*", "msg_income", "id_to = :idp  AND id_msg = :idm ", ["idp"=>$idPlayer, "idm" => $idMessage]);
        if(!count($Message))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        if($Message[0]["from_"] == 1)
            $Message[0] ["name"] = "النظام";
        else
            $Message[0] ["name"] = LPlayer::getName($Message[0] ["id_from"]);
            
        $Message[0]["time_stamp"] = date("j M , H:i", ($Message[0]    ["time_stamp"]));


        if($Message[0]["seen"] == 0)
            updateTable("seen = 1", "msg_income", " id_msg = :idm ", ["idm" => $idMessage]);
        
        return [
            "state" => "ok",
            "Message" => $Message[0]
        ];
    
    }
    
    function getMiscMail()
    {
        
        global $idPlayer;
        $idMessage    = validateID($_GET["idMessage"]);
        $Message = selectFromTable("*", "msg_diff", " id_to = :idp  AND id_msg = :idm ", ["idp" => $idMessage, "idm" => $idPlayer]);
        if(!count($Message))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        $Message[0]["time_stamp"] = date("j M , H:i", ($Message[0]["time_stamp"]));
        if($Message[0]["seen"] == 0){
            updateTable(" seen = 1", "msg_diff", "id_msg = :idm", ["idm" => $idMessage]);
        }
   
        return [
                "state" => "ok",
                "Message" => $Message[0]
            ];
    }
    
    function getOutBoxMail()
    {
        
        global $idPlayer;
        $idMessage    = validateID($_GET["idMessage"]);
        $Message = selectFromTable("*", "msg_out", "id_from = :idp  AND id_msg = :idm", ["idp" => $idPlayer, "idm" => $idMessage]);
        if(!count($Message))
                return ["state" => "error_0", "TryToHack" => TryToHack()];
        
        if($Message[0]["id_to"] == 0){
            $Message[0] ["name"] = "النظام";
        }else{
            $Message[0] ["name"] = LPlayer::getName($Message[0] ["id_to"]);
        }
        $Message[0]["time_stamp"] = date("j M , H:i", ($Message[0]    ["time_stamp"]));
        
        return [
                    "state" => "ok",
                    "Message" => $Message[0]
                ];
    }
    
    
    function getBattelReport()
    {
        global $idPlayer;
        $idReport = validateID($_GET["idReport"]);
        $Report = selectFromTable("report_battel.*, player.name ", "report_battel JOIN player ON player.id_player = report_battel.attacker", "id_report = :idr", ["idr" => $idReport]);
        $PlayerReport = selectFromTable("report_player.*, player.name", "report_player JOIN player ON player.id_player = report_player.id_player", "report_player.id_player = :idp AND report_player.id_report = :idr", ["idp" => $idPlayer, "idr" => $idReport]);
        
        return [
            "idReport"      => $idReport,
            "idAttacker"    => $Report[0]["attacker"],
            "honor"         => $PlayerReport[0]["honor"],
            "AttackerName"  => $Report[0]["name"],
            "task"          => $Report[0]["task"],
            "x"             => $Report[0]["x"],
            "y"             => $Report[0]["y"],
            "sideWin"       => $Report[0]["side_win"],
            "roundNum"      => $Report[0]["round_num"],
            "timeStamp"     => $Report[0]["time_stamp"],
            "side"          => $PlayerReport[0]["side"],
            "ResourcePrize" => selectFromTable("*", "report_res_prize", "id_player = :idp AND id_report = :idr", ["idp" => $idPlayer, "idr" => $idReport]),
            "ItemPrize"     => selectFromTable("*", "report_mat_prize", "id_player = :idp AND id_report = :idr", ["idp" => $idPlayer, "idr" => $idReport]),
            "AttackHeros"   => selectFromTable(
                    "report_hero.*, player.name AS PlayerName, hero.name AS HeroName",
                    " `report_hero` LEFT JOIN hero ON hero.id_hero = report_hero.id_hero LEFT JOIN player ON player.id_player = report_hero.id_player",
                    "report_hero.id_report = :idr AND report_hero.side = :s", ["idr" => $idReport, "s" => BATTEL_SIDE_ATT]),
            "DefenceHeros"  => selectFromTable(
                    "report_hero.*, player.name AS PlayerName, hero.name AS HeroName",
                    " `report_hero` LEFT JOIN hero ON hero.id_hero = report_hero.id_hero LEFT JOIN player ON player.id_player = report_hero.id_player",
                    "report_hero.id_report = :idr AND report_hero.side = :s", ["idr" => $idReport, "s" => BATTEL_SIDE_DEF])
        ];
        
    }
    
    function getCityReport()
    {
        global $idPlayer;
        $idReport = validateID($_GET["idReport"]);
        
        $Report = selectFromTable("id_report, spy_for", "spy_report", "id_report = :idr AND id_player = :idp", ["idp" => $idPlayer, "idr" => $idReport]);
        if(!count($Report))
            return [TryToHack()];
        
        $CityReport = selectFromTable(
                "spy_city.*, player.name AS PlayerName, city.name AS CityName, city.x As xCoord, city.y AS yCoord", 
                "spy_city JOIN city ON city.id_city = spy_city.id_city JOIN player ON player.id_player = spy_city.id_player", 
                "id_report = :idr", ["idr" => $idReport]);
        
        if(!count($CityReport))
            return [];
        return $CityReport[0];
        
    }
    
    function getBarrayReport()
    {
        global $idPlayer;
        $idReport = validateID($_GET["idReport"]);
        
        $Report = selectFromTable("id_report, spy_for", "spy_report", "id_report = :idr AND id_player = :idp", ["idp" => $idPlayer, "idr" => $idReport]);
        if(!count($Report))
            return [TryToHack()];
        
        $BarraryReport =  selectFromTable("*", "spy_barray", "id_report = :idr", ["idr" => $idReport]);
        if(!count($BarraryReport))
            return [];
        
        $BarraryReport[0]["xCoord"] = $Report[0]["x_to"];
        $BarraryReport[0]["yCoord"] = $Report[0]["y_to"];
        
        return $BarraryReport[0];
    }
    
    
}