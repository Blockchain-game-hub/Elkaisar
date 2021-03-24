<?php

$BRdbh = new PDO("mysql:host=localhost;dbname=elkaisar_battel_replay;charset=utf8mb4", "root", "");
$BRdbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);



function BRselectFromTable($string , $table , $condetion, $parm = [])
{
    
    global $BRdbh;
    $sql = $BRdbh->prepare("SELECT $string FROM $table WHERE $condetion");
    if(!$sql->execute($parm)){
        
            file_put_contents("Qurary-select-errors.txt", print_r($_POST, TRUE), FILE_APPEND);
            file_put_contents("Qurary-select-errors.txt", print_r($_GET, TRUE), FILE_APPEND);
            file_put_contents("Qurary-select-errors.txt", print_r($sql, TRUE), FILE_APPEND);
            file_put_contents("Qurary-select-errors.txt", print_r($parm, TRUE), FILE_APPEND);
            
        
    }
   
   
    return $sql->fetchAll(PDO::FETCH_ASSOC);
    
}

