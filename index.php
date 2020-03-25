<?php
include_once 'config.php';
include_once 'base.php';
include_once 'lib/base/Request.php';
include_once 'lib/base/Router.php';
include_once 'lib/base/Session.php';

$router = new Router(new Request);
new Session();
$_SESSION["id_user"] = 2;

$USER =  NULL;
$idU = isset($_SESSION["id_user"]) ?  ($_SESSION["id_user"]) : NULL;

$formatedRoute = explode("/", rtrim($router->formatRoute($router->request->requestUri) , "/"));

if(strtolower($formatedRoute[URL_LANDMARK_INDEX]) == "api"){
    header('Content-Type: application/json');
    $package = "";
    $class = $formatedRoute[URL_LANDMARK_INDEX + 1];
    $func  = $formatedRoute[URL_LANDMARK_INDEX + 2];
    
    
    $fileBath = __DIR__. DIRECTORY_SEPARATOR."api".DIRECTORY_SEPARATOR.$class.".php";
   
    if(file_exists($fileBath)){
        require_once $fileBath;
    }
    
    $inst       = new $class();
    echo $inst->{$func}();
    
    
}