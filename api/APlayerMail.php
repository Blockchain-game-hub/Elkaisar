<?php

class APlayerMail
{
    
    function getIncomeMail()
    {
        global $idPlayer;
        $offset   = validateID($_GET["offset"]);
        
        $msg = selectFromTable(" id_from , head , time_stamp , id_msg  , from_  , seen", "msg_income", " id_to = :idp ORDER BY id_msg DESC LIMIT 10 OFFSET $offset", ["idp" => $idPlayer]);
        foreach ($msg as &$one){
             if($one["from_"] == 1){
                $one ["name"] = "النظام";
            }else{
                $player            = selectFromTable("name, guild, avatar, porm", "player", "id_player = :idp", ["idp" => $one["id_from"]])[0];
                $one ["name"]      = $player["name"];
                $one ["GuildName"] = $player["guild"];
                $one ["avatar"]    = $player["avatar"];
                $one ["porm"]      = $player["porm"];
            }
            $one["time_stamp"] = date("j M , H:i", ($one    ["time_stamp"]));
        }

        return ($msg);
        
    }
    
    function getMiscMail()
    {
        global $idPlayer;
        $offset   = validateID($_GET["offset"]);
        $msgs = selectFromTable("head , time_stamp , id_msg ,seen", "msg_diff", "id_to = :idp  ORDER BY time_stamp DESC LIMIT 10 OFFSET $offset ", ["idp" => $idPlayer]);
        foreach ($msgs as &$one){
            $one["time_stamp"] = date("j M , H:i", ($one    ["time_stamp"]));
        }
        return $msgs;
    }
    
    
    function getOutBoxMail()
    {
        global $idPlayer;
        $offset   = validateID($_GET["offset"]);
        $msgs = selectFromTable("id_to , head , time_stamp , id_msg", "msg_out", "id_from = :idp   ORDER BY time_stamp DESC LIMIT 10 OFFSET $offset", ["idp" => $idPlayer]);
    
        foreach ($msgs as &$one){

             if($one["id_to"] == 0){
                $one ["name"] = "النظام";
            }else{
                $player            = selectFromTable("name, guild, avatar, porm", "player", "id_player = :idp", ["idp" => $one["id_to"]])[0];
                $one ["name"]      = $player["name"];
                $one ["GuildName"] = $player["guild"];
                $one ["avatar"]    = $player["avatar"];
                $one ["porm"]      = $player["porm"];
            }
            $one["time_stamp"] = date("j M , H:i", ($one    ["time_stamp"]));

        }
        return $msgs;
    }
    
    function getBattelReport()
    {
        global $idPlayer;
        $offset   = validateID($_GET["offset"]);
        
        $reports = selectFromTable(" DISTINCT id_report, seen", "report_player", "id_player  = :idp ORDER BY id_report DESC LIMIT 10 OFFSET $offset", ["idp" => $idPlayer]);
        $list = [];
        foreach ($reports as $one){      
            
            $report = selectFromTable("world.x AS xCoord, world.y AS yCoord, world.ut AS unitType, world.l AS unitLvl, report_battel.*", "report_battel JOIN world ON world.x = report_battel.x AND world.y = report_battel.y", "report_battel.id_report = :idr", ["idr" => $one["id_report"]])[0];
            $report["time_stamp"] = date("j M y", ($report["time_stamp"]));
            $list[] = $report;
        }
    
        return ($list);
        
    }
    
    function getSpyReport()
    {
        global $idPlayer;
        $offset   = validateID($_GET["offset"]);
        
        $spy_report = selectFromTable("spy_report.*, world.x AS xCoord, world.y AS yCoord, world.ut AS unitType, world.l AS unitLvl", "spy_report  JOIN world ON world.x = spy_report.x_coord AND world.y = spy_report.y_coord", "spy_report.id_player = :idp ORDER BY spy_report.id_report DESC LIMIT 10 OFFSET $offset" , ["idp"=> $idPlayer]);

        foreach ($spy_report as &$one){   
            $one["time_stamp"] = date("j M y", ($one["time_stamp"]));
        }
    
        return ($spy_report);
        
    }
    
    function deleteMessage()
    {
        global $idPlayer;
        $mgs      = explode(",", ($_POST["msgs"]));
        $idMsgs    = [];
        $table     = validateGameNames($_POST["deleteFrom"]);
        
        if(!count($mgs))
            return ["state" => "ok"];
        
        foreach ($mgs as $val){
            $idMsgs[] = validateID($val);
        }
        
        if($table == "msg_diff"){ 
            $con = "id_to = :idp";
        }else if($table == "msg_income"){ 
            $con = "id_to = :idp";
        }else if($table == "msg_out"){ 
            $con = "id_from = :idp";
        }

        if($table == "report_player" || $table == "spy_report"){

            $ids ="( id_report = " . implode(" OR id_report = ", $idMsgs).") AND  id_player = :idp";

        }else{

            $ids =" ( id_msg = " . implode(" OR id_msg = ", $idMsgs).") AND $con";

        }

        deleteTable( "`$table`", $ids, ["idp" => $idPlayer]);
        
        return ["state" => "ok"];
    }


    function deleteAllMessages()
    {
        global $idPlayer;
        $table     = validateGameNames($_POST["deleteFrom"]);
    
        if($table == "msg_diff"){ 
            $con = "id_to = :idp AND seen = 0";
        }else if($table == "msg_income"){ 
            $con = "id_to = :idp AND seen = 0";
        }else if($table == "msg_out"){ 
            $con = "id_from = :idp AND seen = 0";
        }else if($table == "report_player"){
            $con = "id_player = :idp AND seen = 0";
        }else if($table == "spy_report"){

            $con = "id_player = :idp AND seen = 0";

        }else{
            TryToHack();
        }
        deleteTable("`$table`", $con, ["idp" => $idPlayer]);
        
        return ["state" => "ok"];
    }
}