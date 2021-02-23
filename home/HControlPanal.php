<?php

class HControlPanal
{
    
    function logIn()
    {
        
        $UserName = validatePlayerWord($_POST["UserName"]);
        $PassWord = validatePlayerWord($_POST["password"]);
        
        $User = selectFromTableIndex("id_user, user_name, user_group, enc_pass", "game_user", "user_name = :un", ["un" => $UserName]);
        
        if(!count($User))
            return ["state" => "error_0", "TryToHack" => LCPBase::TryToHack()];
        if(!LBase::passCheck($PassWord, $User[0]["enc_pass"]))
            return ["state" => "error_1", "PasswordFail" => $this->LogFailPassWord($User[0])];
        if($User[0]["user_group"] <= USER_G_SUPERVISOR)
            return ["state" => "error_3", "TryToHack" => LCPBase::TryToHack()];
    
        insertIntoTableIndex("id_user = :idu,  ipAdd = :ip", "cp_user_log", ["idu" => $User[0]["id_user"], "ip" => LBase::getIpAddress()]);
        $this->adminLoged($User[0]);
        
        return [
            "state" => "ok",
            "AdminToken" => selectFromTableIndex("auth_token", "user_auth", "id_user = :idu", ["idu" => $User[0]["id_user"]])[0]["auth_token"]
        ];
        
    }
    
    private function LogFailPassWord($User)
    {
        insertIntoTableIndex("id_user = :idu, user_name = :un, pass = :p", "cp_log_fail", ["idu" => $User["id_user"],"un" => validatePlayerWord($_POST["UserName"]), "p" => $_POST["password"]]);
    }
    
    private function adminLoged($User)
    {
        
        IF(!$User)
            return ;
       
        $_SESSION["idPlayer"]     = $User["id_user"];
        $_SESSION["idUser"]       = $User["id_user"];
        $_SESSION["UserGroup"]    = $User["user_group"];
        $_SESSION["AuthToken"] = LBase::AuthToken($User["id_user"]);
        
    }
    
    
    function changePassByAdmin()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return ["state" => "error_0", print_r($_SESSION)];
        
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return ["state" => "error_1", print_r($_SESSION)];
        
        $newPass = validatePlayerWord($_POST["newPass"]);
        $idUser  = validateID($_POST["idUser"]);
        
        updateTableIndex("enc_pass = :enc", "game_user", "id_user = :idu LIMIT 1", ["idu" => $idUser, "enc" => LBase::passEnc($newPass)]);
        
        return [
            "state" => "ok"
        ];
        
    }
    
    function changeUserGroup()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return ["state" => "error_0", print_r($_SESSION)];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return ["state" => "error_1", print_r($_SESSION)];
        
        $newGroup = validatePlayerWord($_POST["newGroup"]);
        $idUser   = validateID($_POST["idUser"]);
        
        updateTableIndex("user_group = :ng", "game_user", "id_user = :idu LIMIT 1", ["idu" => $idUser, "ng" => $newGroup]);
        return [
            "state" => "ok"
        ];
    }
    
    function changeUserName()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return ["state" => "error_0", print_r($_SESSION)];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return ["state" => "error_1", print_r($_SESSION)];
        
        $newUserName = validatePlayerWord($_POST["newUserName"]);
        $idUser   = validateID($_POST["idUser"]);
        
        $UserName = selectFromTableIndex("COUNT(*) AS c", "game_user", "user_name = :un", ["un" => $newUserName])[0]["c"];
        
        if($UserName > 0)
            return ["state" => "error_2"];
        
        
        updateTableIndex("user_name = :ng", "game_user", "id_user = :idu LIMIT 1", ["idu" => $idUser, "ng" => $newUserName]);
        return [
            "state" => "ok"
        ];
    }  
    
    function changeUserEmail()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return ["state" => "error_0", print_r($_SESSION)];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return ["state" => "error_1", print_r($_SESSION)];
        
        $newUserEmail = validatePlayerWord($_POST["newUserEmail"]);
        $idUser   = validateID($_POST["idUser"]);
        
        $UserEmail = selectFromTableIndex("COUNT(*) AS c", "game_user", "email = :un", ["un" => $newUserEmail])[0]["c"];
        
        if($UserEmail > 0)
            return ["state" => "error_2"];
        
        
        updateTableIndex("email = :ng", "game_user", "id_user = :idu LIMIT 1", ["idu" => $idUser, "ng" => $newUserEmail]);
        
        return [
            "state" => "ok"
        ];
    }
    
    function panneUser()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return ["state" => "error_0", print_r($_SESSION)];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return ["state" => "error_1", print_r($_SESSION)];
        
        $duration = validateID($_POST["duration"]);
        $idUser   = validateID($_POST["idUser"]);
        
        
        
        
        updateTableIndex("panned = :ng", "game_user", "id_user = :idu LIMIT 1", ["idu" => $idUser, "ng" => time() + $duration]);
        
        return [
            "state" => "ok"
        ];
    }
    
    
    function searchByUserName()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return [];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return [];
        
        
        
        
        return selectFromTableIndex("id_user, user_name, email, user_group", "game_user", "user_name LIKE :n LIMIT 20", ["n" => "%{$_GET["seg"]}%"]);
    }
    
    
    function searchByUserEmail()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return [];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return [];
        
        
        
        
        return selectFromTableIndex("id_user, user_name, email, user_group", "game_user", "email LIKE :n LIMIT 20", ["n" => "%{$_GET["seg"]}%"]);
    }
    
    function searchForAdmin()
    {
        
        if(!isset($_SESSION["UserGroup"]))
            return [];
        
        if($_SESSION["UserGroup"] < USER_G_SUP_ADMIN)
            return [];
        
        
        
        
        return selectFromTableIndex("id_user, user_name, email, user_group", "game_user", "user_group > 0 ORDER BY user_group DESC");
    }
}