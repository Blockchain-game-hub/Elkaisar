<?php

$ServerList = [
    1 => [
        "Port" => "8080",
        "serverDbName" => "elkaisar_s_1__",
        "name"  => "الجبابرة"
    ],
    2 => [
        "Port" => "8081",
        "serverDbName" => "elkaisar_s_2",
        "name"  => "الليث"
    ],
    4 => [
        "Port" => "8083",
        "serverDbName" => "elkaisar_s_4",
        "name"  => "القمة"
    ]
];

$dbh ;

function DbConnect($idServer)
{
    global $dbh;
    global $ServerList;
    if(!isset($ServerList[$idServer]))
      catchError ();
    
    $dbh = new PDO("mysql:host=localhost;dbname={$ServerList [$idServer]["serverDbName"]};charset=utf8mb4", "elkaisar_game", "MyWifeSoma1231");
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $dbh->setAttribute(PDO::ATTR_STRINGIFY_FETCHES, false);
    
}


define("BASE_URL", "http://app.elkaisar.com");
define("WEB_SOCKET_HOST", "ws.elkaisar.com");

define("URL_LANDMARK_INDEX", 0);
define('BASE_BATH',      dirname(__FILE__) . DIRECTORY_SEPARATOR);

define("PLAYER_LIMIT_CITY_COUNT", 5);

define("JS_VERSION", "-0.0.12");

function selectFromTable($string , $table , $condetion , $parm = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("SELECT $string FROM $table WHERE $condetion");
    
    if(!$sql->execute($parm)){
        print_r($sql);
        print_r($parm);
        try{
            throw new Exception();
        }catch ( Exception $e ){
            $trace = $e->getTrace();
            file_put_contents("Qurary-select-errors.txt", print_r($trace, TRUE), FILE_APPEND);
            file_put_contents("Qurary-select-errors.txt", print_r($sql, TRUE), FILE_APPEND);
            
        }
    }
   
    
    return $sql->fetchAll(PDO::FETCH_ASSOC);
    
}

function existInTable( $table , $condetion , $pram = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("SELECT EXISTS(SELECT * FROM $table WHERE $condetion) AS val");
    if(!$sql->execute($pram )){
        
        try{
            throw new Exception();
        }catch ( Exception $e ){
            $trace = $e->getTrace();
            file_put_contents("Qurary-errors.txt", print_r($trace, TRUE), FILE_APPEND);
            file_put_contents("Qurary-errors.txt", print_r($sql, TRUE), FILE_APPEND);
            
        }
    }
   
   
    return ($sql->fetch(PDO::FETCH_ASSOC)["val"] == 0? FALSE : TRUE);
    
}

function updateTable($string , $table , $condetion , $pram = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("UPDATE $table SET  $string WHERE $condetion");
    if(!$sql->execute($pram)){
            print_r($sql);
            print_r($pram);
         try{
             throw new Exception();
         }catch ( Exception $e ){
             $trace = $e->getTrace();
             file_put_contents("Qurary-update-errors.txt", print_r($trace, TRUE), FILE_APPEND);
             file_put_contents("Qurary-update-errors.txt", print_r($sql, TRUE), FILE_APPEND);

         }
    }
    $row_count = $sql->rowCount();
    
    return $row_count;
    
}
function deleteTable($table , $condetion, $pram = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("DELETE FROM  $table WHERE $condetion");
   
    if(!$sql->execute($pram)){
        print_r($sql);
        print_r($pram);
        try{
            throw new Exception();
        }catch ( Exception $e ){
            $trace = $e->getTrace();
            file_put_contents("Qurary-delete-errors.txt", print_r($trace, TRUE), FILE_APPEND);
            file_put_contents("Qurary-delete-errors.txt", print_r($sql, TRUE), FILE_APPEND);
            
        }
    }
    $row_count = $sql->rowCount();
    
    return $row_count;
    
}

function insertIntoTable($string ,$table , $pram = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("INSERT IGNORE INTO $table SET  $string ");

    if(!$sql->execute($pram)){
        print_r($sql);
        print_r($pram);
        try{
            throw new Exception();
        }catch ( Exception $e ){
            $trace = $e->getTrace();
            file_put_contents("Qurary-insert-errors.txt", print_r($trace, TRUE), FILE_APPEND);
            file_put_contents("Qurary-insert-errors.txt", print_r($sql , TRUE), FILE_APPEND);
            
        }
    }
    
    
    return $dbh->lastInsertId();
    
}


function queryExe($quray , $pram = [])
{
    
    global $dbh;
    $sql = $dbh->prepare($quray);
    $x = $sql->execute($pram);
    
    if(!$x){
        try{
            throw new Exception();
        }catch ( Exception $e ){
            $trace = $e->getTrace();
            file_put_contents("Qurary-Ex-errors.txt", print_r($trace, TRUE), FILE_APPEND);
            file_put_contents("Qurary-Ex-errors.txt", print_r($sql , TRUE), FILE_APPEND);
            
        }
    }
    return [
        "suc"=>$x,
        "count"=>$sql->rowCount(),
        "lastId"=>$dbh->lastInsertId(),
        "Rows" => $sql->fetchAll(PDO::FETCH_ASSOC)
    ];
    
}



