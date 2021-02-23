<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';

DbConnect(4);
$Players = selectFromTable("id_player", "player", "1 ORDER BY prestige DESC  LIMIT 500 OFFSET 100");
   





