<?php

class APlayerEquip 
{
    function getPlayerEquip()
    {
        
        global $idPlayer;
        return selectFromTable("*", "equip", "id_player = :idp" , ["idp" => $idPlayer]);
        
    }
    
    
    function getEquipPower()
    {
        
        return selectFromTable("*", "equip_power", "1");
        
    }
    
}

