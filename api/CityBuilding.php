<?php


class CityBuilding
{
    
    function getCities($idPlayer = 1)
    {
        $type = selectFromTable("*", "city_building", "id_player = :idp",  ["idp" => $idPlayer]);
        $Lvl  = selectFromTable("*", "city_building_lvl", "id_player = :idp",  ["idp" => $idPlayer]);
        
        for($iii =0; $iii < count($Lvl); $iii++){
            unset($type[$iii]["id_player"]);
            unset($type[$iii]["id_city"]);
            unset($Lvl[$iii]["id_player"]);
            unset($Lvl[$iii]["id_city"]);
        }
        $return ;
        
        for($iii =0; $iii < count($Lvl); $iii++){
            $return[$iii + 1] = [
                "lvl"=>$Lvl[$iii],
                "type"=>$type[$iii]
            ];
        }
        
        
        return json_encode($return, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES);
    }
    
}

