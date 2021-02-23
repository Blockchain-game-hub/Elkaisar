<?php

class ARankingHero
{
    
    function generalRank()
    {
        $offset = validateID($_GET["offset"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar", 
                "hero JOIN player  ON player.id_player = hero.id_player",
                "1  ORDER BY hero.lvl DESC, point_a DESC LIMIT 10 OFFSET $offset"));
    }
    
    function swayRank()
    {
        $offset = validateID($_GET["offset"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar", 
                "hero JOIN player  ON player.id_player = hero.id_player",
                "1  ORDER BY point_a DESC LIMIT 10 OFFSET $offset"));
    }
    
    function braveryRank()
    {
        $offset = validateID($_GET["offset"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar",  
                "hero JOIN player  ON player.id_player = hero.id_player",
                "1  ORDER BY point_b DESC LIMIT 10 OFFSET $offset"));
    }
    
    function parryRank()
    {
        $offset = validateID($_GET["offset"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar", 
                "hero JOIN player  ON player.id_player = hero.id_player",
                "1  ORDER BY point_c DESC LIMIT 10 OFFSET $offset"));
    }
    
    function searchByHeroName()
    {
        $heroName = validateID($_GET["heroName"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar", 
                "hero JOIN player  ON player.id_player = hero.id_player",
                "hero.name LIKE :n  ORDER BY point_c DESC LIMIT 10", ["n" => "%$heroName%"]));
    }
    function searchByPlayerName()
    {
        $playerName = validateID($_GET["playerName"]);
    
        return(selectFromTable(
                "hero.name , hero.id_hero , hero.lvl , hero.point_a , hero.point_b , "
                . "hero.point_c , player.name AS lord_name, player.avatar, hero.avatar AS heroAvatar", 
                "hero JOIN player  ON player.id_player = hero.id_player",
                "player.name LIKE :n ORDER BY point_c DESC LIMIT 10", ["n" => "%$playerName%"]));
    }
    
}