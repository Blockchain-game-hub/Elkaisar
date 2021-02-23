<?php

ini_set("memory_limit", "-1");
set_time_limit(0);

require_once '../../../base.php';
require_once '../../../config.php';
require_once '../../../configHome.php';


DbConnect((2));


$Players = selectFromTable("*", "player", "prestige > 1000000");

echo "DELETE  FROM player            WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM player_stat       WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM quest_player      WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM god_gate          WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM god_gate_1        WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM god_gate_2        WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM god_gate_3        WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM god_gate_4        WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "UPDATE  player SET id_guild = NULL , guild = NULL        WHERE id_player IN(". implode(", ", array_column($Players, "id_player"))."); \n";
echo "DELETE  FROM city              WHERE id_player NOT IN( SELECT id_player FROM player); \n";
echo "DELETE  FROM quest_player      WHERE id_player NOT IN( SELECT id_player FROM player); \n";
echo "DELETE  FROM city_building     WHERE id_player NOT IN( SELECT id_player FROM player); \n";
echo "DELETE  FROM city_building_lvl WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM city_jop          WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM city_storage      WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM city_wounded      WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM edu_acad          WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM edu_uni           WHERE id_player NOT IN(SELECT id_player FROM player); \n";


echo "DELETE  FROM hero              WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM hero_army         WHERE id_hero   NOT IN(SELECT id_hero FROM hero); \n";
echo "DELETE  FROM hero_medal        WHERE id_hero   NOT IN(SELECT id_hero FROM hero); \n";

echo "DELETE  FROM matrial_acce      WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_box       WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_box_plus  WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_flags     WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_luxury    WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_main      WHERE id_player NOT IN(SELECT id_player FROM player); \n";
echo "DELETE  FROM matrial_product   WHERE id_player NOT IN(SELECT id_player FROM player); \n";



