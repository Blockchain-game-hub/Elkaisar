<?php

class HLogIn
{
    
    function playerEnterGame()
    {
        global $ServerList;
        if(!isset($_SESSION["idPlayer"]))
            return ["state" => "error_0", "Trate" =>  $_SESSION, "Header" => getallheaders()];
        if(!isset($_SESSION["AuthToken"]))
            return ["state" => "error_1"];
        if($_SESSION["AuthToken"] != LBase::AuthToken($_SESSION["idPlayer"]) && strlen($_SESSION["AuthToken"]) > 0)
            return ["state" => "error_2" , "SessionError" => LBase::SessionError()];
        
        $Player =  selectFromTableIndex("id_user , user_name , last_server", "game_user", "id_user = :idu", ["idu" => $_SESSION["idPlayer"]]);
        if(!count($Player))
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        return [
                "state"  => "ok",
                "User" => $Player[0],
                "serverName" => isset($ServerList[$Player[0]["last_server"]]) ? $ServerList[$Player[0]["last_server"]]["name"] : "---"
        ];
    }
    
    function playerEnterServer()
    {
        $idServer = validateID($_POST["server"]);
        
        DbConnect($idServer);
        global $ServerList;
        
        if(!isset($_SESSION["idPlayer"]))
            return ["state" => "error_0", "Session" => $_SESSION, "Header" => getallheaders()];
        if(!isset($_SESSION["AuthToken"]))
            return ["state" => "error_1"];
        if( $_SESSION["AuthToken"] != LBase::AuthToken($_SESSION["idPlayer"]) && strlen($_SESSION["AuthToken"]) > 0)
            return ["state" => "error_2" , "SessionError" => LBase::SessionError()];
        
        $Player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $_SESSION["idPlayer"]]);
        updateTableIndex("last_server = :ls", "game_user", "id_user = :idu", ["idu" => $_SESSION["idPlayer"], "ls" => $idServer]);
        if(!count($Player)){
           $this->AddPlayer ($_SESSION["idPlayer"]);
        }else{
         //updateTable ("player_auth = :pt", "player_auth", "id_player = :idp", ["idp" => $Player[0]["id_player"], "pt" => LBase::newPlayerToken($Player[0]["id_player"])]);  
        }
        $newToken  = LBase::newPlayerToken($Player[0]["id_player"]);
           
        queryExe("INSERT INTO player_auth(id_player, auth_token) VALUES (:idp, :t) ON DUPLICATE KEY UPDATE auth_token = :ot", ["idp" => $Player[0]["id_player"], "t" => $newToken, "ot" => $newToken]);
        $Player [0]["p_token"] =  $newToken; 
        
        return [
            "state"     => "ok",
            "Player"    => $Player[0],
            "Server"    => selectFromTable("*", "server_data", "id = 1")[0],
            "JsVersion" => JS_VERSION,
            "WsPort"    => $ServerList[$idServer]["Port"],
            "WsHost"    => WEB_SOCKET_HOST,
            "OuthToken" => $newToken,
            "idServer"  => $idServer,
            "idCities"  => array_column(selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $Player[0]["id_player"]]), "id_city")
        ];
        
    }
    
    function playerEnterServerWeb()
    {
        $idServer = validateID($_POST["server"]);
        $OuthToken = validateGameNames($_POST["outhToken"]);
        DbConnect($idServer);
        global $ServerList;
        global $idPlayer;
        


        $playerOuth = selectFromTable("*", "player_auth", "auth_token = :ot", ["ot" => $OuthToken]);
        if(!count($playerOuth))
            return ["state" => "error_0"];
        $Player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $playerOuth[0]["id_player"]]);
        updateTableIndex("last_server = :ls", "game_user", "id_user = :idu", ["idu" => $playerOuth[0]["id_player"], "ls" => $idServer]);
        
        
        $idPlayer = $Player[0]["id_player"];
        $Cities = array_column(selectFromTable("id_city", "city", "id_player = :idp", ["idp" => $Player[0]["id_player"]]), "id_city");
        foreach ($Cities as $idCity)
        {
            LSaveState::afterCityColonized($idCity);
            LSaveState::afterCityColonizer($idCity);
        }
        
        return [
            "state"     => "ok",
            "Player"    => $Player[0],
            "Server"    => selectFromTable("*", "server_data", "id = 1")[0],
            "JsVersion" => JS_VERSION,
            "WsPort"    => $ServerList[$idServer]["Port"],
            "WsHost"    => WEB_SOCKET_HOST,
            "OuthToken" => $OuthToken,
            "idServer"  => $idServer,
            "idCities"  => $Cities
        ];
        
    }
    
    function logIn()
    {
        global $ServerList;
        $UserName = validatePlayerWord($_POST["userName"]);
        $passWord = $_POST["password"];
        $User = selectFromTableIndex("id_user , user_name , last_server, enc_pass, panned", "game_user", "( user_name = :ue OR email = :em )", ["ue" =>$UserName, "em" => $UserName]);
        
        
        if(!count($User))
            return ["state"  => "error_0", "UserNameFail" => $this->LogFailUserName()];
        if(!LBase::passCheck($passWord, $User[0]["enc_pass"]))
            return ["state"  => "error_1", "PasswordFail" => $this->LogFailPassWord($User[0])];
        if($User[0]["panned"] > time())
            return  ["state" => "error_2"];
            
    
        insertIntoTableIndex("id_user = :idu, ip_address = :ip", "user_log", ["idu" => $User[0]["id_user"], "ip" => LBase::getIpAddress()]);
       
        LBase::userLogedIn($User[0]["id_user"]);
        unset($User[0]["enc_pass"]);
        return [
            "state"  => "ok",
            "User" => $User[0],
            "serverName" => isset($ServerList[$User[0]["last_server"]]) ? $ServerList[$User[0]["last_server"]]["name"] : "---"
        ];
        
    }
    
    
    private function LogFailUserName()
    {
        
    }
    
    private function LogFailPassWord($User)
    {
        insertIntoTableIndex("id_user = :idu, user_name = :un, pass = :p", "log_fail", ["idu" => $User["id_user"],"un" => validatePlayerWord($_POST["userName"]), "p" => $_POST["password"]]);
    }
    
    function AddPlayer($idUser = 0)
    {
        global $idPlayer;
        
        if($idUser == 0)
            $idUser = validateID ($_POST["idUser"]);
        $Player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $idUser]);
        if(count($Player))
            return false;
        
        $avatar = rand(0, 19);
        $PlayerName = "P-".$idUser."-". LBase::alphaID(random_int(0, 1000));
        insertIntoTable("name = :n, avatar = :a, p_token =:p, id_player = :idp", "player", ["n" => $PlayerName, "a" => $avatar, "p" => LBase::newPlayerToken($idUser), "idp" => $idUser]);
        $Player = selectFromTable("*", "player", "id_player = :idp", ["idp" => $idUser]);
        
        if(!count($Player)){
            return false;
            
        }
        
        $PlayerCount = selectFromTable("COUNT(*) AS c", "player", "1")[0]["c"];
        
        $idPlayer = $Player[0]["id_player"];
        insertIntoTable("id_player = :idp", "edu_acad",        ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp", "edu_uni",         ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp", "player_edu",      ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp", "player_stat",     ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp", "player_title",    ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp", "god_gate",        ["idp" => $Player[0]["id_player"]]);
        insertIntoTable("id_player = :idp, rank = :r", "arena_player_challange", ["idp" => $Player[0]["id_player"], "r" => $PlayerCount + 1]);
        insertIntoTable("id_player = :idp", "arena_player_challange_buy", ["idp" => $Player[0]["id_player"]]);
        
        
        queryExe("INSERT INTO quest_player(id_quest , id_player , done)          SELECT quest.id_quest , {$Player[0]["id_player"]}, 0 FROM quest WHERE 1");
        queryExe("INSERT INTO exchange_player(id_trade , id_player , take_times) SELECT exchange.id_ex , {$Player[0]["id_player"]}, 0 FROM exchange WHERE 1");
        queryExe("INSERT INTO player_item(id_item , id_player, amount)           SELECT item.id_item ,   {$Player[0]["id_player"]}, item.startingAmount FROM item WHERE 1");
        
       
        updateTable("player_num = :n", "server_data", "1", ["n" => $PlayerCount + 1]);
        
        $EmptyUnit = LWorld::getEmptyPlace(random_int(1, 10));
        if(!($EmptyUnit))
            $EmptyUnit = LWorld::getEmptyPlace(random_int(1, 10));
        LCity::addCity($EmptyUnit[0]["x"], $EmptyUnit[0]["y"]);
        LPlayer::giveNewCommerPrize($idPlayer);
    }
    
    
    function signUp()
    {
        
        $UserName  = validatePlayerWord($_POST["UserName"]);
        $Email     = validateEmail($_POST["Email"]);
        $Password  = validatePlayerWord($_POST["Password"]);
        $UserWithSameName = selectFromTableIndex("id_user", "game_user", "user_name = :un", ["un" => $UserName]);
        $UserWithSameMail = selectFromTableIndex("id_user", "game_user", "email = :un", ["un" => $Email]);
        
        if(mb_strlen($UserName) < 5)
            return ["state" => "error_1"];
        if(mb_strlen($UserName) > 15)
            return ["state" => "error_2"];
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) 
            return ["state" => "error_3"];
        if (mb_strlen($Password) > 25 || mb_strlen($Password) < 4) 
            return ["state" => "error_4"];
        if(count($UserWithSameName))
            return ["state" => "error_5"];
        if(count($UserWithSameMail))
            return ["state" => "error_6"];
       
        $enc_pass = LBase::passEnc($Password);

        $idUser = insertIntoTableIndex("user_name = :un, user_password = :up, enc_pass = :ep, email = :e", "game_user", ["un" => $UserName, "up" => md5($Password), "ep" => $enc_pass, "e" => $Email]);
        
        $User = selectFromTableIndex("id_user , user_name , last_server", "game_user", "id_user = :idu", ["idu" =>$idUser]);
        if(!count($User))
            return ["state" => "error_7"];
        
        insertIntoTableIndex("id_user = :idu, ip_address = :ip", "user_log", ["idu" => $User[0]["id_user"], "ip" => LBase::getIpAddress()]);
        
        LBase::userLogedIn($User[0]["id_user"]);
        
        return [
            "state"  => "ok",
            "User" => $User[0]
        ]; 
        
          
    }
    
    
    function logOut()
    {
        
        session_destroy();
        return [
            "state" => "ok"
        ];
        
    }
    
    
    
}
