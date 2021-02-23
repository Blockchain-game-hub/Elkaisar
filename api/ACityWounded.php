<?php

class ACityWounded
{
    
    function getCityWounded()
    {
        global $idPlayer;
        $idCity   = validateID($_GET["idCity"]);
        
        return selectFromTable("*", "city_wounded", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0];
        
        
    }
    
}

