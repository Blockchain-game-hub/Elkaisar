<?php

class AplayerEquip 
{
    function getPlayerEquip()
    {
        
        $idPlayer = validateID(cryptUserId($_GET["token"]));
        return json_encode(selectFromTable("*", "equip", "id_player = :idp" , ["idp" => $idPlayer]), JSON_NUMERIC_CHECK);
        
    }
    
}

