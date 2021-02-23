<?php

class AWorldUnitPrize{
    
    function getWorldUnitPrize()
    {
        
        $unitLvl  = validateID($_GET["unitLvl"]);
        $unitType = validateID($_GET["unitType"]);
        
        return [
            "W" => selectFromTable("*", "world_unit_prize", "unitType = :ut AND lvl = :l", ["ut" => $unitType, "l" => $unitLvl]),
            "S" => selectFromTable("*", "world_unit_prize_sp", "unitType = :ut AND lvl = :l", ["ut" => $unitType, "l" => $unitLvl]),
            "L" => selectFromTable("*", "world_unit_prize_lose", "unitType = :ut AND lvl = :l", ["ut" => $unitType, "l" => $unitLvl]),
            "P" => selectFromTable("*", "world_unit_prize_plunder", "unitType = :ut AND lvl = :l", ["ut" => $unitType, "l" => $unitLvl])
        ];
    }
    
    
    function addPrize()
    {
         
        $unitLvl       = validateID($_POST["unitLvl"]);
        $unitType      = validateID($_POST["unitType"]);
        $amountMin     = validateID($_POST["amountMin"]);
        $amountMax     = validateID($_POST["amountMax"]);
        $winRate       = validateID($_POST["winRate"]);
        $Item          = validateID($_POST["Item"]);
        $isSpecilPrize = validateGameNames($_POST['isSpeicial']);
        
        if($isSpecilPrize == "true"){
            insertIntoTable(
                "unitType = :ut, lvl = :l, prize = :i, amount_min = :amin, amount_max = :amax, win_rate = :wr",
                "world_unit_prize_sp",
                ["ut" => $unitType, "l" => $unitLvl, "i" => $Item, "amin" => $amountMin, "amax" => $amountMax, "wr" => $winRate]);
        }else {
            insertIntoTable(
                "type = 0, unitType = :ut, lvl = :l, prize = :i, amount_min = :amin, amount_max = :amax, win_rate = :wr",
                "world_unit_prize",
                ["ut" => $unitType, "l" => $unitLvl, "i" => $Item, "amin" => $amountMin, "amax" => $amountMax, "wr" => $winRate]);
        }
        
        
    }
    
    function removePrize()
    {
        
        $idPrize = validateID($_POST["idPrize"]);
        $isSpeicial = validateGameNames($_POST["isSpeicial"]);
        
        if($isSpeicial == "true"){
            deleteTable("world_unit_prize_sp", "id_prize = :idp", ["idp" => $idPrize]);
        }else 
             deleteTable("world_unit_prize", "id_prize = :idp", ["idp" => $idPrize]);
        
    }
}