<?php

class CPlayer
{
    static public $PrizeEmpty = [
        "ItemPrize" => [],
        "ResourcePrize" =>[
            "food"  =>0,
            "wood"  =>0,
            "stone" =>0,
            "metal" =>0,
            "coin"  =>0
        ]
    ];

    static public $PlayerCityPrizeEmpty = [
        "idCity"   => 0,
        "Kills"    => 0,
        "Killed"   => 0,
        "armySize" =>0,
        "armyRemainSize" =>0,
        "ResourceCap" =>0,
        "Troops" => [
            0 => 0,
            ARMY_A => 0,
            ARMY_B => 0,
            ARMY_C => 0,
            ARMY_D => 0,
            ARMY_E => 0,
            ARMY_F => 0,
            ARMY_WALL_A => 0,
            ARMY_WALL_B => 0,
            ARMY_WALL_C => 0
        ],
        "RemainTroops" => [
            0 => 0,
            ARMY_A => 0,
            ARMY_B => 0,
            ARMY_C => 0,
            ARMY_D => 0,
            ARMY_E => 0,
            ARMY_F => 0,
            ARMY_WALL_A => 0,
            ARMY_WALL_B => 0,
            ARMY_WALL_C => 0
        ],
    ];
    
    static public $BattelPlayerEmpty = [
        "idPlayer" =>0,
        "Name"     =>"",
        "Honor"    =>0,
        "Kills"    =>0,
        "Killed"   =>0,
        "heroNum"  =>1,
        "Troops"   => [
            0 => 0,
            ARMY_A => 0,
            ARMY_B => 0,
            ARMY_C => 0,
            ARMY_D => 0,
            ARMY_E => 0,
            ARMY_F => 0,
            ARMY_WALL_A => 0,
            ARMY_WALL_B => 0,
            ARMY_WALL_C => 0
        ],
        "RemainTroops" => [
            0 => 0,
            ARMY_A => 0,
            ARMY_B => 0,
            ARMY_C => 0,
            ARMY_D => 0,
            ARMY_E => 0,
            ARMY_F => 0,
            ARMY_WALL_A => 0,
            ARMY_WALL_B => 0,
            ARMY_WALL_C => 0
        ],
        "ItemPrize" => [],
        "ResourcePrize" =>[
            "food"  =>0,
            "wood"  =>0,
            "stone" =>0,
            "metal" =>0,
            "coin"  =>0
        ]
        
    ];
    
}