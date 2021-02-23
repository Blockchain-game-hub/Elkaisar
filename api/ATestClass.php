<?php

require_once '../config.php';
DbConnect(1);
$units = selectFromTable("*", "world", "ut NOT IN (60, 61, 62, 63, 0)");

$String = [];

foreach ($units as $oneUnit)
{
   $String[]  = "{$oneUnit["x"]}_{$oneUnit["y"]}_{$oneUnit["ut"]}_{$oneUnit["l"]}_{$oneUnit["p"]}_{$oneUnit["t"]}";
}

file_put_contents("WorldBarrary.json", "\"". implode(",", $String)."\"");