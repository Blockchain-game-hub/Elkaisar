<?php

ini_set("memory_limit", "-1");
set_time_limit(0);
require_once '../lib/LConfig.php';
require_once '../config.php';
require_once '../base.php';


$File = json_decode(file_get_contents("../js-0.0.12/json/questBase.json"), true);

foreach ( $File as $unitType =>  &$Unit){
    $Unit["showCond"] = [];
}


print_r(json_encode($File, JSON_PRETTY_PRINT));

