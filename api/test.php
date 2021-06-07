<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../lib/LConfig.php';
require_once '../config.php';
require_once '../base.php';
DbConnect(1);

$idPlayer = 95;

LSaveState::coinInState(942);

print_r(selectFromTable("coin_in", "city", "id_city = 942"));