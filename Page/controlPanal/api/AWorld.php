<?php

class AWorld
{
    
    function changeLvlByType()
    {
        
        $unitType = validateID($_POST["unitType"]);
        $lvl      = validateID($_POST["lvl"]);
        
        if(!isset($_SESSION["UserGroup"]))
            return  ["state" => "error_0"];
        if($_SESSION["UserGroup"] < USER_G_SUB_ADMIN)
            return  ["state" => "error_1"];
        if($unitType <= WUT_WOODS_LVL_9)
            return ["state" => "error_2"];
        
        
        updateTable("l = :l", "world", "ut = :ut", ["l" => $lvl, "ut" => $unitType]);
        
        return [
            "state" => "ok"
        ];
    }
    
}
