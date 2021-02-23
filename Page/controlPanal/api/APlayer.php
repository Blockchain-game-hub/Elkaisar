<?php

class APlayer
{
    
    function changePlayerName()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newName  = validatePlayerWord($_POST["newName"]);
        
        updateTable("name = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newName]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function changePlayerGroup()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newGroup  = validatePlayerWord($_POST["newGroup"]);
        
        updateTable("user_group = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newGroup]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    
    function changePlayerPrestige()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newVal   = validatePlayerWord($_POST["newVal"]);
        
        updateTable("prestige = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newVal]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    
    
    function changePlayerHonor()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newVal   = validatePlayerWord($_POST["newVal"]);
        
        updateTable("honor = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newVal]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function changePlayerPorm()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newVal   = validatePlayerWord($_POST["newVal"]);
        
        updateTable("porm = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newVal]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function changePlayerGold()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newVal   = validatePlayerWord($_POST["newVal"]);
        
        updateTable("gold = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newVal]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function pannPlayer()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
        $idPlayer = validateID($_POST["idPlayer"]);
        $newVal   = validatePlayerWord($_POST["newVal"]);
        
        updateTable("panned = :n", "player", "id_player = :idp", ["idp" => $idPlayer, "n" => $newVal + time()]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function searchByName()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
       
        $seg   = validatePlayerWord($_GET["seg"]);
      
        return selectFromTable("id_player, name, porm, gold, honor, prestige, guild, user_group, panned", "player", "name LIKE :n ORDER BY prestige DESC LIMIT 20 ", ["n" => "%$seg%"]);
        
    }
    
    function searchByGuild()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
       
        $seg   = validatePlayerWord($_GET["seg"]);
      
        return selectFromTable("id_player, name, porm, gold, honor, prestige, guild, user_group, panned", "player", "guild LIKE :n ORDER BY prestige DESC LIMIT 20 ", ["n" => "%$seg%"]);
        
    }
    
    
    function searchForAdmin()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        
       
        return selectFromTable("id_player, name, porm, gold, honor, prestige, guild, user_group, panned", "player", "user_group > 0 ORDER BY user_group DESC LIMIT 150 ");
        
    }
    
}
