<?php


spl_autoload_register(function($className){
    if($className[0] == "L")
        require_once dirname(__FILE__) .DIRECTORY_SEPARATOR."lib".DIRECTORY_SEPARATOR.$className.".php";
    else if($className[0] == "C")
        require_once dirname(__FILE__) .DIRECTORY_SEPARATOR."configs".DIRECTORY_SEPARATOR.$className.".php";
});


function validateID($id)
{
    return $id;
}

function validateNumber($number)
{
    return $number;
}

function validateGameNames($name){
    return $name;
}

function validatePlayerWord($Word)
{
    return $Word;
}

function validateEmail($Word)
{
    return $Word;
}

function TryToHack(){
    
}

function makePostReq($parm, $url) {
    
    $fields = http_build_query($parm);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $textResponce = curl_exec($ch);
    curl_close($ch);
    return $textResponce;
}




function  catchError()
{
    global  $formatedRoute;
    global $idPlayer;
    if(isset($_POST["server"]))
    {
        file_put_contents("FileErrorDetailPost.txt", print_r($idPlayer, TRUE)."\n", FILE_APPEND);
        file_put_contents("FileErrorDetailPost.txt", print_r($_POST, TRUE), FILE_APPEND);
        file_put_contents("FileErrorDetailPost.txt", print_r($formatedRoute, TRUE), FILE_APPEND);
        
    }
    if(isset($_GET["server"]))
    {
        file_put_contents("FileErrorDetailGet.txt", print_r($idPlayer, TRUE)."\n", FILE_APPEND);
        file_put_contents("FileErrorDetailGet.txt", print_r($_GET, TRUE), FILE_APPEND);
        file_put_contents("FileErrorDetailGet.txt", print_r($formatedRoute, TRUE), FILE_APPEND);
        
    }
        
    
}
function passEnc($pass) {
    return password_hash(substr_replace(md5($pass), "!%@!((&", 15, 0), PASSWORD_BCRYPT);
}