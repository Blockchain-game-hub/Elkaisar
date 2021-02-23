<?php

class AWorldMap
{
    
    function refreshWorldUnit()
    {
        
        $xStart = validateID($_GET["xStart"]);
        $xEnd   = validateID($_GET["xEnd"]);
        $yStart = validateID($_GET["yStart"]);
        $yEnd   = validateID($_GET["yEnd"]);
        
        if(abs($xStart - $xEnd) > 25 || abs($yStart - $yEnd) > 25)
            return ["state" => "error_0"];
        
        $cities = selectFromTable("player.id_guild, city.x, city.y, city.lvl, player.city_flag", "player RIGHT JOIN  city ON city.id_player = player.id_player ", "(x BETWEEN $xStart AND  $xEnd ) AND (y BETWEEN $yStart AND $yEnd)");
        $changableLvl = selectFromTable("x, y, l", "world", "(x BETWEEN $xStart AND  $xEnd ) AND (y BETWEEN $yStart AND  $yEnd) AND ut >= ".WUT_MONAWRAT);
    
        return([
            "City" =>$cities,
            "changableLvl"=>$changableLvl
                ]);
        
    }
    
}