<?php
ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
require_once '../../lib/LConfig.php';




DbConnect(2);
$cc = 0;
$ps = selectFromTable("*", "world_unit_prize", "unitType = 76");
foreach ($ps as $one)
{
    insertIntoTable("type = 31, unitType = 71, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 72, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 73, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 74, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 75, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 77, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 78, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 79, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 80, lvl = {$one["lvl"]}, mat_tab = '{$one["mat_tab"]}', prize = '{$one["prize"]}', amount_min = '{$one["amount_min"]}', amount_max = '{$one["amount_max"]}', win_rate = '{$one["win_rate"]}'", "world_unit_prize");
    echo "done $cc \n";
    $cc++;
}
