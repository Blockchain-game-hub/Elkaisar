<?php

class CHero
{
    
    static public $HeroNames = ["ماكسيموس","اشرف", "مصطفى", "اليكس", "اليسا", "بطليموس",
            "كليوباترا","هكس","ماجد", "يويليوس","مارس","ماكس","صلاح الدين","سيورس",
             "سيزار", "اغسطس","جلادياتور","سما", "زين","شادو","الملك", "القاهر",
            "الاسد", "اليس","حورس","يورك"
        ];
    
    static public $EmptyBattelHero = [
        "pre"=>[
            "f_1" => 0,
            "f_2" => 0,
            "f_3" => 0,
            "b_1" => 0,
            "b_2" => 0,
            "b_3" => 0
        ],
        "type"=>[
            "f_1" => 0,
            "f_2" => 0,
            "f_3" => 0,
            "b_1" => 0,
            "b_2" => 0,
            "b_3" => 0
        ]
    ];
    
    static public $HeroPacksPoints = [
        "tagned_3p"=>3,
        "tagned_4p"=>4,
        "tagned_5p"=>5,
        "tagned_6p"=>6,
        "tagned_7p"=>7,
        "tagned_8p"=>8
    ];
    static public $EmptyBattelHeroEff = [
        "attack"         => 0,    // attack of unite in this cell
        "def"            => 0,    // defence of unite in this cell
        "vit"            => 0,    // vitality of unite in this cell
        "dam"            => 0,    // damage of unite in this cell
        "break"          => 0,    // damage of unite in this cell
        "anti_break"     => 0,    // damage of unite in this cell
        "strike"         => 0,    // damage of unite in this cell
        "immunity"       => 0,    // damage of unite in this cell

        "sp_attack"      => 0,    // attack of unite in this cell
        "sp_defence"     => 0,    // defence of unite in this cell
        "sp_vit"         => 0,    // vitality of unite in this cell
        "sp_damage"      => 0,    // damage of unite in this cell
        "sp_break"       => 0,    // damage of unite in this cell
        "sp_anti_break"  => 0,    // damage of unite in this cell
        "sp_strike"      => 0,    // damage of unite in this cell
        "sp_immunity"    => 0,    // damage of unite in this cell

        "unit"           => 0,    // the number of untie in this cell
        "fight"          => FALSE,
        "defenced"       => FALSE,
        "dead_unit"      =>0,
        "honor"          =>0,
        "points"         =>0,
        "armyType"      =>0,
        "troopsKills"    =>0,
        "troopsKilled"   =>0
    ];
    
    static public $EmptyHeroEquipEff = [
        
            "attack"=>0,
            "def"=>0,
            "vit"=>0,
            "dam"=>0,
            "break"=>0,
            "anti_break"=>0,
            "strike"=>0,
            "immunity"=>0
        
    ];
}

