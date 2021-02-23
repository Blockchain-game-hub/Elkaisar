<?php

class ACityBarrary
{
    
    function getAllCitiesBarray()
    {
        
        global $idPlayer;

      
        return 
                selectFromTable(
                        "world.l AS Lvl, world.ut AS Type, city_bar.*",
                        "city_bar JOIN world ON world.x = city_bar.x_coord AND world.y = city_bar.y_coord",
                        "city_bar.id_player = :idp", ["idp" => $idPlayer]);
        
    }
    
    function getCityBarray()
    {
        global $idPlayer;
        $idCity = validateID($_GET["idCity"]);
        return 
                selectFromTable(
                        "world.l AS Lvl, world.ut AS Type, city_bar.*",
                        "city_bar JOIN world ON world.x = city_bar.x_coord AND world.y = city_bar.y_coord",
                        "city_bar.id_player = :idp AND id_city = :idc", ["idp" => $idPlayer, "idc" => $idCity]);
    }
    
}
