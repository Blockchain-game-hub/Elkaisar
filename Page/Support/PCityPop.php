<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
require_once '../../lib/LConfig.php';




DbConnect(4);
$Cities = selectFromTable("id_city", "city", "1");

foreach ($Cities as $oneCity)
{
    
    LCity::refreshPopCap($oneCity["id_city"]);
    print_r(selectFromTable("pop_cap", "city", "id_city = {$oneCity["id_city"]}"));
    
}

