<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';

DbConnect(2);
echo updateTable("in_city = 1", "hero", "in_city = 0")." Hero Back From Ellith.</br>";
DbConnect(4);
echo updateTable("in_city = 1", "hero", "in_city = 0")." Hero Back From Elkima";

