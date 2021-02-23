<?php

class AHeroEquip
{
    
    function putEquipOnHero()
    {
        global $idPlayer;
        $idHero = validateID($_POST["idHero"]);
        $idEquip = validateID($_POST["idEquip"]);
        $Equip   = selectFromTable("*", "equip", "id_equip = :ide AND id_player = :idp", ["ide" => $idEquip, "idp" => $idPlayer]);
        $Hero = selectFromTable("in_city", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $idHero, "idp" => $idPlayer]);
        
        if(!count($Equip))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if(!count($Hero))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Equip[0]["id_hero"] > 0)
            return ["state" => "error_2", "idHero" => $Equip[0]["id_hero"], "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_3", "TryToHack" => TryToHack()];
        
        updateTable("id_hero = NULL, on_hero = 0", "equip", "id_hero = :idh AND part = :p", ["idh" => $idHero, "p" => $Equip[0]["part"]]);
        updateTable("id_hero = :idh, on_hero = 1", "equip", "id_equip = :ide", ["ide" => $Equip[0]["id_equip"], "idh" => $idHero]);
        
        return [
            "state" => "ok",
            "PlayerEquip" => selectFromTable("id_equip, on_hero, id_hero", "equip", "id_player = :idp", ["idp" => $idPlayer])
        ];
    
    }
    
    function putEquipOffHero()
    {
        global $idPlayer;
        $idEquip = validateID($_POST["idEquip"]);
        $Equip   = selectFromTable("*", "equip", "id_equip = :ide AND id_player = :idp", ["ide" => $idEquip, "idp" => $idPlayer]);
        
       
        if(!count($Equip))
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        $Hero    = selectFromTable("in_city", "hero", "id_hero = :idh AND id_player = :idp", ["idh" => $Equip[0]["id_hero"], "idp" => $idPlayer]);
        if(!count($Hero))
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        if($Hero[0]["in_city"] != HERO_IN_CITY)
            return ["state" => "error_2", "TryToHack" => TryToHack()];
        
        updateTable("id_hero = NULL, on_hero = 0", "equip", "id_equip = :ide", ["ide" => $idEquip]);
        
        return [
            "state" => "ok",
            "PlayerEquip" => selectFromTable("id_equip, on_hero, id_hero", "equip", "id_player = :idp", ["idp" => $idPlayer])
        ];
    
    }
    
    
    
}



