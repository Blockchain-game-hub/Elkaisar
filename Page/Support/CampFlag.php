<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../base.php';
require_once '../../config.php';
require_once '../../configHome.php';
require_once '../../lib/LConfig.php';




DbConnect(2);

/*              
  $flag = "flag_france";
  $flag = "flag_magul";
  $flag = "";*/
/*
define("WUT_CAMP_ASIANA"  ,   73);
define("WUT_CAMP_GAULS"   ,   74);
 */
$FLAG_WIN_RATE = [
        250, 250, 250, 250, 250, 250, 250, 250, 250, 350,
        280, 280, 280, 280, 280, 280, 280, 280, 280, 380,
        310, 310, 310, 310, 310, 310, 310, 310, 310, 380,
        350, 350, 350, 350, 350, 350, 350, 350, 350, 390,
        370, 370, 370, 370, 370, 370, 370, 370, 370, 400
    ];
for ($iii = 1; $iii <= 50; $iii ++) {
    $amountMax = 1;
    if ($iii == 50 || $iii == 40) {
        $amountMax = 5;
    } else if ($iii > 40) {
        $amountMax = 3;
    } else if ($iii > 30) {
        $amountMax = 2;
    }
    insertIntoTable("type = 31, unitType = 71, lvl = $iii, prize = 'flag_england', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 72, lvl = $iii, prize = 'flag_germany', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 73, lvl = $iii, prize = 'flag_magul', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 74, lvl = $iii, prize = 'flag_france', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 75, lvl = $iii, prize = 'flag_macdoni', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 76, lvl = $iii, prize = 'flag_spain', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 77, lvl = $iii, prize = 'flag_roma', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 78, lvl = $iii, prize = 'flag_greek', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 79, lvl = $iii, prize = 'flag_cartaga', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    insertIntoTable("type = 31, unitType = 80, lvl = $iii, prize = 'flag_egypt', amount_min = 1, amount_max = $amountMax, win_rate = {$FLAG_WIN_RATE[$iii -1]}", "world_unit_prize");
    
    
}
