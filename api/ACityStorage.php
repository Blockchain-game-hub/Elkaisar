<?php

class ACityStorage
{
    
    function getCityStorage()
    {
        $idCity = validateID($_GET["idCity"]);
        
        return 
                selectFromTable("*", "city_storage", "id_city = :idc", ["idc" => $idCity])[0];
        
    }
    
    function updatePercentage(){
        
        global $idPlayer;
        $idCity    = validateID($_POST["idCity"]);
        $foodPrec  = validateID($_POST["foodPerc"]);
        $woodPrec  = validateID($_POST["woodPerc"]);
        $stonePrec = validateID($_POST["stonePerc"]);
        $metalPrec = validateID($_POST["metalPerc"]);
        
        if($foodPrec + $woodPrec + $stonePrec + $metalPrec > 100)
            return ["state" => "error_0", "TryToHack" => TryToHack()];
        if($foodPrec < 0 || $woodPrec < 0 || $stonePrec < 0 || $metalPrec < 0 )
            return ["state" => "error_1", "TryToHack" => TryToHack()];
        
        
        updateTable(
                "food_storage_ratio = :fc , wood_storage_ratio = :wc , metal_storage_ratio = :mc , stone_storage_ratio = :sc", 'city_storage',
                "id_city = :idc AND id_player = :idp",
                [   "idc" => $idCity, "fc"  =>$foodPrec, 
                    "wc"  => $woodPrec, "sc"  => $stonePrec, 
                    "mc"  => $metalPrec, "idp" => $idPlayer
            ]);
        
        LSaveState::storeRatio($idCity);
        return [
            "state"       => "ok" ,
            "cityStorage" => selectFromTable("*", "city_storage", "id_city = :idc", ["idc" => $idCity])[0],
            "City"        => selectFromTable("*", "city", "id_city = :idc AND id_player = :idp", ["idc" => $idCity, "idp" => $idPlayer])[0]
            ];
   
        
    
    }
    
}
