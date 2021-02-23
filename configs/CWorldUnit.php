<?php




class CWorldUnit
{
    
    public static  $MAX_UNIT_LVL=[
        WUT_MONAWRAT      => 50, WUT_CAMP_BRITONS  => 50,
        WUT_CAMP_REICH    => 50, WUT_CAMP_ASIANA   => 50,
        WUT_CAMP_GAULS    => 50, WUT_CAMP_MACEDON  => 50,
        WUT_CAMP_HISPANIA => 50, WUT_CAMP_ITALIA   => 50,
        WUT_CAMP_PARTHIA  => 50, WUT_CAMP_CARTHAGE => 50,
        WUT_CAMP_EGYPT    => 50,
        
        WUT_FRONT_SQUAD        =>40, WUT_FRONT_BAND         =>40,
        WUT_FRONT_SQUADRON     =>40, WUT_FRONT_DIVISION     =>40,
        WUT_ARMY_LIGHT_SQUAD   =>30, WUT_ARMY_LIGHT_BAND    =>30,
        WUT_ARMY_LIGHT_SQUADRON=>30, WUT_ARMY_LIGHT_DIVISION=>30,
        
        WUT_ARMY_HEAVY_SQUAD    =>20, WUT_ARMY_HEAVY_BAND     =>20,
        WUT_ARMY_HEAVY_SQUADRON =>20, WUT_ARMY_HEAVY_DIVISION =>20,
        WUT_GUARD_SQUAD         =>20, WUT_GUARD_BAND          =>20,
        WUT_GUARD_SQUADRON      =>20, WUT_GUARD_DIVISION       =>20,
        WUT_BRAVE_THUNDER       =>10,
        
        WUT_GANG               =>2, WUT_MUGGER           =>2, WUT_THIEF=>2,
        WUT_CARTHAGE_GANG      =>10, WUT_CARTHAGE_TEAMS  =>10,
        WUT_CARTHAGE_REBELS    =>10, WUT_CARTHAGE_FORCES =>10,
        WUT_CARTHAGE_CAPITAL=>10,
        
        WUT_WAR_STATUE_A => 75, WUT_WAR_STATUE_B=>50,  WUT_WAR_STATUE_C => 25,
        WUT_WOLF_STATUE_A=> 10, WUT_WOLF_STATUE_B=>10, WUT_WOLF_STATUE_C=> 10
        
    ];
    
    static function isLastLvl($Unit)
    {
        if(!isset(static::$MAX_UNIT_LVL[$Unit["ut"]]))
            return false;
        
        return $Unit["l"] >= static::$MAX_UNIT_LVL[$Unit["ut"]];
    }
    
    static function isOverLastLvl($Unit)
    {
        if(!isset(static::$MAX_UNIT_LVL[$Unit["ut"]]))
            return false;
        
        return $Unit["l"] > static::$MAX_UNIT_LVL[$Unit["ut"]];
    }
}