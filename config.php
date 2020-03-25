<?php


$dbh = new PDO("mysql:host=localhost;dbname=server_1;charset=utf8mb4", "root", "");


$dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

define("BASE_URL", "http://localhost/Elkaisar");
define("URL_LANDMARK_INDEX", 1);


define("PLAYER_LIMIT_CITY_COUNT", 5);



function selectFromTable($string , $table , $condetion , $parm = [])
{
    
    global $dbh;
    $sql = $dbh->prepare("SELECT $string FROM $table WHERE $condetion");
    
    if(!$sql->execute($parm)){
        
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
    if(!$sql->execute()){
        
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
    $sql = $dbh->prepare("INSERT INTO $table SET  $string ");

    if(!$sql->execute($pram)){
        
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


function queryExe($quray , $pram = []){
    
    global $dbh;
    $sql = $dbh->prepare($quray);
    return [
        "suc"=>$sql->execute($pram),
        "count"=>$sql->rowCount(),
        "lastId"=>$dbh->lastInsertId()
    ];
    
}




