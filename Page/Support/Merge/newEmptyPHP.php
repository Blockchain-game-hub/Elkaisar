<?php
/*
 DELETE FROM arena_player_challange WHERE  id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM arena_player_challange_buy WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM arena_player_challange_hero WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM build_army WHERE id_player NOT IN (SELECT id_player FROM player);
 
 DELETE FROM city WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM city_bar WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM city_building WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM city_building_lvl WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM city_jop WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM city_storage WHERE id_city NOT IN (SELECT id_city FROM city);
 DELETE FROM city_theater WHERE id_city NOT IN (SELECT id_city FROM city);
 
 DELETE FROM city_wounded WHERE id_city NOT IN (SELECT id_city FROM city);
 DELETE FROM edu_uni WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM edu_acad WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM equip WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM exchange_player WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM god_gate WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM god_gate_1 WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM god_gate_2 WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM god_gate_3 WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM god_gate_4 WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM guild      WHERE id_leader NOT IN (SELECT id_player FROM player);
 
 DELETE FROM guild_member WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM guild_req    WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM hero         WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM hero_army    WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM hero_back    WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM hero_equip    WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM hero_medal    WHERE id_hero NOT IN (SELECT id_hero FROM hero);
 DELETE FROM hero_theater  WHERE id_city NOT IN (SELECT id_city FROM city);

 DELETE FROM player_auth  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM player_edu  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM player_item  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM player_logs  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM player_stat  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM player_title  WHERE id_player NOT IN (SELECT id_player FROM player);
 DELETE FROM quest_player  WHERE id_player NOT IN (SELECT id_player FROM player);

*/
