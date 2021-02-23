<?php

class LEquip {

    static function addEquip($Equip, $part, $lvl = 1) {
        global $idPlayer;
        insertIntoTable(
                "id_player = :idp, type = :t, part = :p, lvl = :l",
                "equip",
                ["idp" => $idPlayer, "t" => $Equip, "p" => $part, "l" => $lvl]
        );
    }

    static function prepareHeroBattel(&$Heros, $Unit) {
        
        foreach ($Heros as &$oneHero) {
            if (($oneHero["id_hero"] < 1 && !LWorldUnit::heroSysHasEquip($Unit["t"]))) 
                continue;
            

            if (!LWorldUnit::isEquipEfeective($Unit["t"])) 
                continue; 
            
            if ($oneHero["id_hero"] < 1) 
                $equip = selectFromTable("part, equip AS type, lvl ", "world_unit_equip", "x = :xc AND y = :yc AND l = :l", ["xc" => $Unit["x"], "yc" => $Unit["y"], "l" => $Unit["l"]]);
            else 
                $equip = selectFromTable("equip.part , equip.type ,equip.lvl", "equip", "equip.id_hero = :idh AND id_player = :idp", ["idh" => $oneHero["id_hero"], "idp" => $oneHero["id_player"]]);
            

            foreach ($equip as $one) {

                $equipEffect = selectFromTable("*", "equip_power", "equip = :t AND part = :p AND lvl = :l", ["t" => $one["type"], "p" => $one["part"], "l" => $one["lvl"]])[0];
                
                foreach ($oneHero["real_eff"] as &$oneCell){
                    
                    $oneCell["attack"]     +=  $equipEffect["attack"];
                    $oneCell["def"]        +=  $equipEffect["defence"];
                    $oneCell["vit"]        +=  $equipEffect["vitality"]; 
                    $oneCell["dam"]        +=  $equipEffect["damage"]; 
                    $oneCell["break"]      +=  $equipEffect["break"]; 
                    $oneCell["anti_break"] +=  $equipEffect["anti_break"];
                    $oneCell["strike"]     +=  $equipEffect["strike"]; 
                    $oneCell["immunity"]   +=  $equipEffect["immunity"]; 
                }
            }

        }
        
    }

}
