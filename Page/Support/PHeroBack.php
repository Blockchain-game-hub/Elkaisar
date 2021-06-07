<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';


foreach ($ServerList as $key => $one){
    
    DbConnect($key);
    echo  updateTable("in_city = 1", "hero", "in_city = 0")."بطل رجع  </br>";
    echo deleteTable("battel", "time_end < :t AND done = 1", ["t" => time()*1000 - 15000])." معركة معلقة  </br>";
    echo deleteTable("battel_member", "id_battel NOT IN (SELECT id_battel FROM battel WHERE 1)")."بطل فى المعركة </br>";
    echo deleteTable("hero_back"," task != 5")."بطل فى مرحلة الرجوع </br>";
    echo "تم سيرفر {$one["name"]} </br>";
    
}


