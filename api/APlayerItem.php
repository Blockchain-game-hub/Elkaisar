<?php

class APlayerItem
{
    
    function getPlayerItems()
    {
        
        global $idPlayer;

        return selectFromTable(
                "*", 
                "player_item",
                "id_player = :idp", ["idp" => $idPlayer]
                );
        
    }
    
}

