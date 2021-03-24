<?php
header("Access-Control-Allow-Origin: *");
include_once 'config.php';
include_once 'base.php';
include_once 'lib/base/Request.php';
include_once 'lib/base/Router.php';
include_once 'lib/base/BSession.php';


$router = new Router(new Request);


        
$idPlayer = 0;
$idServer = 0;
    
        

$formatedRoute = explode("/", rtrim($router->formatRoute($router->request->requestUri) , "/"));
$UrlLandMark = strtolower($formatedRoute[URL_LANDMARK_INDEX]);


    
if($UrlLandMark == "api"){
    $Token = "";
    $Player = [];
   
    if(isset($_GET["token"]))
    {
        $Token = $_GET["token"];
        $idServer = validateID($_GET["server"]);
        
    } else if(isset ($_POST["token"])){
        $Token = $_POST["token"];
        $idServer = validateID($_POST["server"]);
    }else{
       // print_r($formatedRoute);
    }

    DbConnect($idServer);
     
    if($Token)
        $Player = selectFromTable("id_player", "player_auth", "auth_token = :pt", ["pt" => $Token]);
    if(!isset($Player[0])){
        file_put_contents("PlayerTokenError.txt", print_r($Token, TRUE)."\n". json_encode($VPlayer)."   ".print_r($router->request->requestUri, true)."\n\n\n\n", FILE_APPEND);
    }
        
    $idPlayer = $Player[0]["id_player"];



    //header('Content-Type: application/json');
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
    $class    = $formatedRoute[URL_LANDMARK_INDEX + 1];
    $func     = $formatedRoute[URL_LANDMARK_INDEX + 2];
    $fileBath = __DIR__. DIRECTORY_SEPARATOR."api".DIRECTORY_SEPARATOR.$class.".php";
   
    if(file_exists($fileBath))
        require_once $fileBath;
    ELSE
    {
        file_put_contents(print_r($router->request->requestUri, true), "errrrr.txt");
    }
    
    $inst       = new $class();
    echo json_encode($inst->{$func}());
    
    
}
else if ($UrlLandMark == "home")
{
    require_once 'configHome.php';
    new BSession();
    
    if(isset($_GET["server"])){
        $idServer = validateID($_GET["server"]);
        DbConnect($idServer);
        
    } else if(isset ($_POST["server"])){
        $idServer = validateID($_POST["server"]);
        DbConnect($idServer);
    }

    
    
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
    $class    = $formatedRoute[URL_LANDMARK_INDEX + 1];
    $func     = $formatedRoute[URL_LANDMARK_INDEX + 2];
    $fileBath = __DIR__. DIRECTORY_SEPARATOR."home".DIRECTORY_SEPARATOR.$class.".php";
   
    
    if(file_exists($fileBath))
        require_once $fileBath;
    
    
    $inst       = new $class();
    echo json_encode($inst->{$func}(), JSON_UNESCAPED_SLASHES  | JSON_UNESCAPED_UNICODE);
    
}
else if($UrlLandMark == "ws")
{
   
    if(isset($_GET["server"]))
        $idServer = validateID($_GET["server"]);
    else if(isset ($_POST["server"]))
        $idServer = validateID($_POST["server"]);
    else{
        file_put_contents("ErroorWebSocket.txt", print_r($router->request->requestUri, TRUE), FILE_APPEND);
        file_put_contents("ErroorWebSocket.txt", print_r($_POST, TRUE), FILE_APPEND);
        file_put_contents("ErroorWebSocket.txt", print_r($_GET, TRUE), FILE_APPEND);
    }
        

    
    DbConnect($idServer);
    
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
    $class    = $formatedRoute[URL_LANDMARK_INDEX + 2];
    $func     = $formatedRoute[URL_LANDMARK_INDEX + 3];
    $fileBath = __DIR__. DIRECTORY_SEPARATOR."WebSocket".DIRECTORY_SEPARATOR."api".DIRECTORY_SEPARATOR.$class.".php";
    
    if(file_exists($fileBath))
        require_once $fileBath;
    
    
    $inst       = new $class();
    echo json_encode($inst->{$func}());
    
}else if($UrlLandMark == "cp"){
    
    require_once 'configHome.php';
    new BSession();
    
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
    $class    = $formatedRoute[URL_LANDMARK_INDEX + 1];
    $func     = $formatedRoute[URL_LANDMARK_INDEX + 2];
    $fileBath = __DIR__. DIRECTORY_SEPARATOR."Page". DIRECTORY_SEPARATOR."controlPanal".DIRECTORY_SEPARATOR."api".DIRECTORY_SEPARATOR.$class.".php";
    
    
     if(file_exists($fileBath))
        require_once $fileBath;
     
     
    if(isset($_GET["server"]))
        $idServer = validateID($_GET["server"]);
    else if(isset ($_POST["server"]))
        $idServer = validateID($_POST["server"]);
        
    DbConnect($idServer);
    
    $inst       = new $class();
    echo json_encode($inst->{$func}());
    
}else if($UrlLandMark == "battelreplay"){

    define("BATTEL_REPLAY_ID", $formatedRoute[URL_LANDMARK_INDEX + 1]);
    require_once './configBR.php';
    define("RESOURCE_BATH", BASE_URL."/Page/Battel");
    require_once __DIR__.DIRECTORY_SEPARATOR."Page/Battel/BattelReplay.php";
    
}else{
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
  
    define("RESOURCE_BATH", BASE_URL."/Page/".$formatedRoute[URL_LANDMARK_INDEX]);
    
    require_once 'configHome.php';
    require_once __DIR__.DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR."LConfig.php";
    require_once __DIR__.DIRECTORY_SEPARATOR."Page".DIRECTORY_SEPARATOR.$formatedRoute[URL_LANDMARK_INDEX].DIRECTORY_SEPARATOR."P".$formatedRoute[URL_LANDMARK_INDEX + 1].".php";
    
}

