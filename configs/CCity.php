<?php

class CCity
{
    static public $CityBlocks = [
        "0" => [
            "light_house_1",   "light_house_2",  "light_house_3",  "light_house_4",  "light_house_5",
            "light_house_6",   "light_house_7",  "light_house_8",  "light_house_9",  "light_house_10",
            "under_palace_1",  "under_palace_2", "under_palace_3", "under_palace_4", "under_palace_5",
            "under_palace_6",  "under_palace_7", "under_palace_8", "under_palace_9", "under_palace_10", 
            "under_palace_11", "under_palace_12",
        ],
        "1" => [
            "hill_1", "hill_2", "hill_3", "hill_4", "hill_5", "hill_6", "hill_7",
            "hill_8", "hill_9", "hill_10", "hill_11", "hill_12"
        ],
        "2" => [
            "under_wall_1", "under_wall_2", "under_wall_3", "under_wall_4", "under_wall_5", 
            "under_wall_6", "under_wall_7", "under_wall_8", "under_wall_9", "under_wall_10", 
            "under_wall_11", "under_wall_12", 
        ],
        "3" => [
            "above_palace_1", "above_palace_2", "above_palace_3",
            "above_palace_4", "above_palace_5", "above_palace_6", 
            "around_wood_1",  "around_wood_2",  "around_wood_3"
        ]
    ];

    static public $CottagePopCap = [
        100,  250,  500,  750,  1000, 1500, 2000, 2750, 3500, 4500,
        5400, 5625, 5850, 6075, 6300, 6525, 6750, 6975, 7200, 7425,
        7650, 7875, 8100, 8325, 8550, 8775, 9000, 9225, 9450, 9675
    ];
    
    static public $StorageCap = [
        24e4, 48e4, 96e4, 192e4, 384e4, 768e4, 1536e4, 3072e4, 6144e4, 12288e4,
        147456e3, 1536e5, 160e6, 165e6, 172e6, 178e6, 180e6, 182e6, 185e6, 188e6,
        200e6, 202e6, 210e6, 215e6, 220e6, 225e6, 230e6, 240e6, 245e6, 250e6
    ];
    
    public static $PalaceCoinCap = [
        20000   , 50000   , 100000  , 300000  , 700000  ,
        1500000 , 3500000 , 8000000 , 18000000, 37000000,
        37800000, 38600000, 39400000, 40200000, 41000000,
        41800000, 42600000, 43400000, 44200000, 45000000,
        45800000, 46600000, 47400000, 48200000, 49000000,
        49800000, 50600000, 51400000, 52200000, 53000000, 
        53000000
    ]; 
}

