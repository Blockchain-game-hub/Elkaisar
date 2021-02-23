<?php

class CJop {

    public static $JopReq = [
        "farm" => ["food" => 10, "wood" => 20, "stone" => 30, "metal" => 15, "time" => 31, "pop" => 1, "condetion" => [], "produce" => "food"],
        "wood" => ["food" => 15, "wood" => 10, "stone" => 20, "metal" => 30, "time" => 31, "pop" => 1, "condetion" => [], "produce" => "wood"],
        "stone" => ["food" => 30, "wood" => 15, "stone" => 10, "metal" => 20, "time" => 31, "pop" => 1, "condetion" => [], "produce" => "stone"],
        "mine" => ["food" => 20, "wood" => 30, "stone" => 15, "metal" => 10, "time" => 31, "pop" => 1, "condetion" => [], "produce" => "metal"]
    ];
    
    public static $JOP_AVAIL_PLACE = [
        10, 30, 60, 100, 150, 200,
        300, 500, 750, 1000, 1200, 1250,
        1300, 1350, 1400, 1450, 1500, 1550,
        1600, 1650, 1700, 1750, 1800, 1850,
        1900, 1950, 2000, 2050, 2100, 2150, 2150
    ];

}
