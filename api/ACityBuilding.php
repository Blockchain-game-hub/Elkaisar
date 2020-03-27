<?php

class ACityBuilding
{
    
    public function getAllCityBuilding()
    {
        
        $idPlayer = validateID($_GET["idPlayer"]);
        $CB = selectFromTable("*", "city_building", "id_player = :idp", ["idp" => $idPlayer]);
        $CBL = selectFromTable("*", "city_building_lvl", "id_player = :idp", ["idp" => $idPlayer]);
        
    }
    
    
}

