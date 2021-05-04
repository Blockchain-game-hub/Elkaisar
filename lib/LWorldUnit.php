<?php

class LWorldUnit {

    static function isBarrary($t) {
        return ($t >= WUT_RIVER_LVL_1 && $t <= WUT_WOODS_LVL_10);
    }

    static function isRiver($t) {
        return ($t >= WUT_RIVER_LVL_1 && $t <= WUT_RIVER_LVL_10);
    }

    static function isEmpty($t) {
        return ($t == WUT_EMPTY);
    }

    static function isCity($t) {
        return ($t >= WUT_CITY_LVL_0 && $t <= WUT_CITY_LVL_3);
    }

    static function isMountain($t) {
        return ($t >= WUT_MOUNT_LVL_1 && $t <= WUT_MOUNT_LVL_10);
    }

    static function isDesert($t) {
        return ($t >= WUT_DESERT_LVL_1 && $t <= WUT_DESERT_LVL_10);
    }

    static function isWood($t) {
        return ($t >= WUT_WOODS_LVL_1 && $t <= WUT_WOODS_LVL_10);
    }

    static function isMonawrat($t) {
        return ($t == WUT_MONAWRAT);
    }

    static function isCamp($t) {
        return ($t >= WUT_CAMP_BRITONS && $t <= WUT_CAMP_EGYPT);
    }

    static function isAsianSquads($t) {
        return ($t >= WUT_FRONT_SQUAD && $t <= WUT_BRAVE_THUNDER);
    }

    static function isOneFRONT($t) {
        return ($t >= WUT_FRONT_SQUAD && $t <= WUT_FRONT_DIVISION);
    }

    static function isFrontSquad($t) {
        return ($t == WUT_FRONT_SQUAD);
    }

    static function isFrontBand($t) {
        return ($t == WUT_FRONT_BAND);
    }

    static function isFrontSquadron($t) {
        return ($t == WUT_FRONT_SQUADRON);
    }

    static function isFrontDivision($t) {
        return ($t == WUT_FRONT_DIVISION);
    }

    static function isOneLight($t) {
        return ($t >= WUT_ARMY_LIGHT_SQUAD && $t <= WUT_ARMY_LIGHT_DIVISION);
    }

    static function isLightSquad($t) {
        return ($t == WUT_ARMY_LIGHT_SQUAD);
    }

    static function isLightBand($t) {
        return ($t == WUT_ARMY_LIGHT_BAND);
    }

    static function isLightSquadron($t) {
        return ($t == WUT_ARMY_LIGHT_SQUADRON);
    }

    static function isLightDivision($t) {
        return ($t == WUT_ARMY_LIGHT_DIVISION);
    }

    static function isOneHeavy($t) {
        return ($t >= WUT_ARMY_HEAVY_SQUAD && $t <= WUT_ARMY_HEAVY_DIVISION);
    }

    static function isHeavySquad($t) {
        return ($t == WUT_ARMY_HEAVY_SQUAD);
    }

    static function isHeavyBand($t) {
        return ($t == WUT_ARMY_HEAVY_SQUAD);
    }

    static function isHeavySquadron($t) {
        return ($t == WUT_ARMY_HEAVY_SQUADRON);
    }

    static function isHeavyDivision($t) {
        return ($t == WUT_ARMY_HEAVY_DIVISION);
    }

    static function isOneGuard($t) {
        return ($t >= WUT_GUARD_SQUAD && $t <= WUT_GUARD_DIVISION);
    }

    static function isGuardSquad($t) {
        return ($t == WUT_GUARD_SQUAD);
    }

    static function isGuardBand($t) {
        return ($t == WUT_GUARD_BAND);
    }

    static function isGuardSquadron($t) {
        return ($t == WUT_GUARD_SQUADRON);
    }

    static function isGuardDivision($t) {
        return ($t == WUT_GUARD_DIVISION);
    }

    static function isBraveThunder($t) {
        return ($t == WUT_BRAVE_THUNDER);
    }

    static function isGangStar($t) {
        return ($t >= WUT_GANG && $t <= WUT_THIEF);
    }

    static function isGang($t) {
        return ($t == WUT_GANG);
    }

    static function isMugger($t) {
        return ($t == WUT_MUGGER);
    }

    static function isThiefs($t) {
        return ($t == WUT_THIEF);
    }

    static function isCarthagianArmies($t) {
        return ($t >= WUT_CARTHAGE_GANG && $t <= WUT_CARTHAGE_CAPITAL);
    }

    static function isCarthasianGang($t) {
        return ($t == WUT_CARTHAGE_GANG);
    }

    static function isCarthageTeams($t) {
        return ($t == WUT_CARTHAGE_TEAMS);
    }

    static function isCarthageRebals($t) {
        return ($t == WUT_CARTHAGE_REBELS);
    }

    static function isCarthageForces($t) {
        return ($t == WUT_CARTHAGE_FORCES);
    }

    static function isCarthageCapital($t) {
        return ($t == WUT_CARTHAGE_CAPITAL);
    }

    static function isArmyCapital($t) {
        return ($t >= WUT_ARMY_CAPITAL_A && $t <= WUT_ARMY_CAPITAL_F);
    }

    static function isArmyCapitalA($t) {
        return ($t == WUT_ARMY_CAPITAL_A);
    }

    static function isArmyCapitalB($t) {
        return ($t == WUT_ARMY_CAPITAL_B);
    }

    static function isArmyCapitalC($t) {
        return ($t == WUT_ARMY_CAPITAL_C);
    }

    static function isArmyCapitalD($t) {
        return ($t == WUT_ARMY_CAPITAL_D);
    }

    static function isArmyCapitalE($t) {
        return ($t == WUT_ARMY_CAPITAL_E);
    }

    static function isArmyCapitalF($t) {
        return ($t == WUT_ARMY_CAPITAL_F);
    }

    static function isQueenCity($t) {
        return ($t >= WUT_QUEEN_CITY_A && $t <= WUT_QUEEN_CITY_C);
    }

    static function isQueenCityS($t) {
        return ($t == WUT_QUEEN_CITY_A);
    }

    static function isQueenCityM($t) {
        return ($t == WUT_QUEEN_CITY_B);
    }

    static function isQueenCityH($t) {
        return ($t == WUT_QUEEN_CITY_C);
    }

    static function isRepelCastle($t) {
        return ($t >= WUT_REPLE_CASTLE_A && $t <= WUT_REPLE_CASTLE_C);
    }

    static function isRepelCastleS($t) {
        return ($t == WUT_REPLE_CASTLE_A);
    }

    static function isRepelCastleM($t) {
        return ($t == WUT_REPLE_CASTLE_B);
    }

    static function isRepelCastleH($t) {
        return ($t == WUT_REPLE_CASTLE_C);
    }

    static function isStatueWar($t) {
        return ($t >= WUT_WAR_STATUE_A && $t <= WUT_WAR_STATUE_C);
    }

    static function isStatueWarS($t) {
        return ($t == WUT_WAR_STATUE_A);
    }

    static function isStatueWarM($t) {
        return ($t == WUT_WAR_STATUE_B);
    }

    static function isStatueWarH($t) {
        return ($t == WUT_WAR_STATUE_C);
    }

    static function isStatueWalf($t) {
        return ($t >= WUT_WOLF_STATUE_A && $t <= WUT_WOLF_STATUE_C);
    }

    static function isStatueWalfS($t) {
        return ($t == WUT_WOLF_STATUE_A);
    }

    static function isStatueWalfM($t) {
        return ($t == WUT_WOLF_STATUE_B);
    }

    static function isStatueWalfH($t) {
        return ($t == WUT_WOLF_STATUE_C);
    }

    static function canHasGarrison($t) {
        return (static::isCity($t) || static::isBarrary($t));
    }

    static function isArena($t) {
        return ($t >= WUT_ARENA_CHALLANGE && $t <= WUT_ARENA_GUILD);
    }

    static function isArenaChallange($t) {
        return ($t == WUT_ARENA_CHALLANGE);
    }

    static function isArenaDeath($t) {
        return ($t == WUT_ARENA_DEATH);
    }

    static function isArenaGuild($t) {
        return ($t == WUT_ARENA_GUILD);
    }
    
    static function isChallangeField($t) {
        return ($t >= WUT_CHALLAGE_FIELD_PLAYER && $t <= WUT_CHALLAGE_FIELD_SERVER);
    }

    static function isChallangeFieldPlayer($t) {
        return ($t == WUT_CHALLAGE_FIELD_PLAYER);
    }

    static function isChallangeFieldTeam($t) {
        return ($t == WUT_CHALLAGE_FIELD_TEAM);
    }

    static function isChallangeFieldGuild($t) {
        return ($t == WUT_CHALLAGE_FIELD_GUILD);
    }
    static function isChallangeFieldServer($t) {
        return ($t == WUT_CHALLAGE_FIELD_SERVER);
    }
    
    static function isFightField($t) {
        return ($t >= WUT_FIEGHT_FIELD_PLAYER && $t <= WUT_FIEGHT_FIELD_SERVER);
    }

    static function isFightFieldPlayer($t) {
        return ($t == WUT_FIEGHT_FIELD_PLAYER);
    }

    static function isFightFieldTeam($t) {
        return ($t == WUT_FIEGHT_FIELD_TEAM);
    }

    static function isFightFieldGuild($t) {
        return ($t == WUT_FIEGHT_FIELD_PLAYER);
    }

    static function isFightFieldServer($t) {
        return ($t == WUT_FIEGHT_FIELD_SERVER);
    }
    
    static function isSeaCity($t) {
        return ($t >= WUT_SEA_CITY_1 && $t <= WUT_SEA_CITY_6);
    }
    
    static function isSeaCity_1($t) {
        return ($t >= WUT_SEA_CITY_1);
    }
    static function isSeaCity_2($t) {
        return ($t >= WUT_SEA_CITY_2);
    }
    static function isSeaCity_3($t) {
        return ($t >= WUT_SEA_CITY_3);
    }
    static function isSeaCity_4($t) {
        return ($t >= WUT_SEA_CITY_4);
    }
    static function isSeaCity_5($t) {
        return ($t >= WUT_SEA_CITY_5);
    }
    static function isSeaCity_6($t) {
        return ($t >= WUT_SEA_CITY_6);
    }

    
    
    
    
    
    static function isSharablePrize($t) {
        return static::isCarthagianArmies($t) || static::isCity($t);
    }

    static function isEquipEfeective($t) {
        return !static::isArmyCapital($t);
    }

    static function heroSysHasEquip($t) {
        return (static::isStatueWalf($t) || static::isStatueWar($t));
    }

    static function isGodGateEffective($t) {
        return !(static::isArmyCapital($t) || static::isArenaDeath($t));
    }

    static function limitedHero($t) {
        return (static::isArmyCapital($t) || static::isCarthagianArmies($t) || static::isStatueWalf($t) || static::isStatueWar($t) || static::isSeaCity($t));
    }
    
    static function isDefencable($t){
        return (!static::isCarthagianArmies($t) && !static::isStatueWalf($t) && !static::isStatueWar($t));
    }

    static function afterWinAnnounceable($t) {
        return (
                static::isMonawrat($t) ||
                static::isCamp($t) ||
                static::isArena($t) ||
                static::isArmyCapital($t) ||
                static::isStatueWalf($t) ||
                static::isStatueWar($t)
                );
    }

    static function isGuildWar($t) {
        return static::isCarthagianArmies($t) ||
                static::isArenaGuild($t) ||
                static::isQueenCity($t) ||
                static::isRepelCastle($t);
    }

    static function hasGarrison($x, $y) {
        return selectFromTable("id_player", "world_unit_garrison", "x_coord = $x AND y_coord = $y", ["x" => $x, "y" => $y]);
    }

    static function heroCanAttack($idHero, $unitType) {

        if (static::isArmyCapital($unitType)) {

            $capitalArmy = [
                WUT_ARMY_CAPITAL_A => ARMY_A,
                WUT_ARMY_CAPITAL_B => ARMY_B,
                WUT_ARMY_CAPITAL_C => ARMY_C,
                WUT_ARMY_CAPITAL_D => ARMY_D,
                WUT_ARMY_CAPITAL_E => ARMY_E,
                WUT_ARMY_CAPITAL_F => ARMY_F
            ];
            return LHeroArmy::isTheSame($idHero, $capitalArmy[$unitType]);
        }

        return TRUE;
    }

    static function isAttackable($idHero, $unit) {

        global $idPlayer;
        if(static::isFightField($unit["ut"]) || static::isChallangeField($unit["ut"]))
            return false;
        if (static::isArenaGuild($unit["ut"]) || static::isStatueWar($unit["ut"])) {

            return (existInTable("guild_member", "id_player = $idPlayer"));
        } else if (static::isRepelCastle($unit["ut"]) || static::isQueenCity($unit["ut"])) {

            if (existInTable("guild_member", "id_player = $idPlayer") == FALSE)
                return false;
            if (existInTable("battel", "x_coord = {$unit["x"]} AND y_coord = {$unit["y"]}") == TRUE)
                return false;
        } else if (static::isEmpty($unit["ut"])) {
            return FALSE;
        }
        
        return TRUE;
    }

    static function calDist($xFrom, $xTo, $yFrom, $yTo) {
        $difX = abs($xFrom - $xTo);
        $difY = abs($yFrom - $yTo);
        if($difX > 249)
            $difX -= 500;
        if($difY > 249)
            $difY -= 500;
        return floor(sqrt(pow(($difX), 2) + pow(($difY), 2)) * 6000);
    }

    static function calAttackTime($idHero, $City, $Unit) {
        
        $unitType = $Unit["ut"];
        
       
        if (
           static::isAsianSquads($unitType) || static::isGangStar($unitType) || static::isCarthagianArmies($unitType) || static::isArenaChallange($unitType) || static::isArenaDeath($unitType) || static::isArmyCapital($unitType) || static::isMonawrat($unitType) || static::isStatueWar($unitType) || static::isStatueWalf($unitType)
        )
            return 120;
        else if (static::isArenaGuild($unitType)) 
            return 5 * 60;
        else if(static::isQueenCity($unitType) || static::isSeaCity($unitType))
            return  15*60;
        else if(static::isRepelCastle($unitType))
            return 60*60;


        $distance = static::calDist($City["x"], $Unit["x"], $City["y"], $Unit["y"]);
        $slowestSpeed = LHeroArmy::getSlowestSpeed($idHero);


        if (static::isCity($unitType)) {


            return max(ceil($distance / $slowestSpeed), 15 * 60);
        } else if (static::isCamp($unitType)) {

            return max(ceil($distance / $slowestSpeed), 120);
        }

        return ceil($distance / $slowestSpeed);
    }

    static function calReturningTime($idHero, $City, $Unit) {
        
        $unitType = $Unit["ut"];
        $now = time();
        $distance = static::calDist($City["x"], $Unit["x"], $City["y"], $Unit["y"]);
        $slowestSpeed = LHeroArmy::getSlowestSpeed($idHero);
        $OrgRetTim = ceil($distance / $slowestSpeed);
        
        if (
                static::isMonawrat($unitType)     || static::isAsianSquads($unitType)  || static::isGangStar($unitType)  
                || static::isCarthagianArmies($unitType)  || static::isCamp($unitType)  || static::isStatueWalf($unitType) 
                || static::isStatueWar($unitType) || static::isQueenCity($unitType))
                $returningTime = $now + min($OrgRetTim, 15 * 60);

        else if (static::isArena($unitType) || static::isArmyCapital($unitType))
                $returningTime = $now + 60;
        else $returningTime = $now + min($OrgRetTim, 2 * 60 * 60);

        return $returningTime;
    }

}
