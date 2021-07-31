class LWorldUnit {

    static isBarrary($t) {
        return ($t >= Elkaisar.Config.WUT_RIVER_LVL_1 && $t <= Elkaisar.Config.WUT_WOODS_LVL_10);
    }

    static isRiver($t) {
        return ($t >= Elkaisar.Config.WUT_RIVER_LVL_1 && $t <= Elkaisar.Config.WUT_RIVER_LVL_10);
    }

    static isEmpty($t) {
        return ($t == Elkaisar.Config.WUT_EMPTY);
    }

    static isCity($t) {
        return ($t >= Elkaisar.Config.WUT_CITY_LVL_0 && $t <= Elkaisar.Config.WUT_CITY_LVL_3);
    }

    static isMountain($t) {
        return ($t >= Elkaisar.Config.WUT_MOUNT_LVL_1 && $t <= Elkaisar.Config.WUT_MOUNT_LVL_10);
    }

    static isDesert($t) {
        return ($t >= Elkaisar.Config.WUT_DESERT_LVL_1 && $t <= Elkaisar.Config.WUT_DESERT_LVL_10);
    }

    static isWood($t) {
        return ($t >= Elkaisar.Config.WUT_WOODS_LVL_1 && $t <= Elkaisar.Config.WUT_WOODS_LVL_10);
    }

    static isMonawrat($t) {
        return ($t == Elkaisar.Config.WUT_MONAWRAT);
    }

    static isCamp($t) {
        return ($t >= Elkaisar.Config.WUT_CAMP_BRITONS && $t <= Elkaisar.Config.WUT_CAMP_EGYPT);
    }

    static isAsianSquads($t) {
        return ($t >= Elkaisar.Config.WUT_FRONT_SQUAD && $t <= Elkaisar.Config.WUT_BRAVE_THUNDER);
    }

    static isOneFRONT($t) {
        return ($t >= Elkaisar.Config.WUT_FRONT_SQUAD && $t <= Elkaisar.Config.WUT_FRONT_DIVISION);
    }

    static isFrontSquad($t) {
        return ($t == Elkaisar.Config.WUT_FRONT_SQUAD);
    }

    static isFrontBand($t) {
        return ($t == Elkaisar.Config.WUT_FRONT_BAND);
    }

    static isFrontSquadron($t) {
        return ($t == Elkaisar.Config.WUT_FRONT_SQUADRON);
    }

    static isFrontDivision($t) {
        return ($t == Elkaisar.Config.WUT_FRONT_DIVISION);
    }

    static isOneLight($t) {
        return ($t >= Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD && $t <= Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION);
    }

    static isLightSquad($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD);
    }

    static isLightBand($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_LIGHT_BAND);
    }

    static isLightSquadron($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON);
    }

    static isLightDivision($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION);
    }

    static isOneHeavy($t) {
        return ($t >= Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD && $t <= Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION);
    }

    static isHeavySquad($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD);
    }

    static isHeavyBand($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD);
    }

    static isHeavySquadron($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON);
    }

    static isHeavyDivision($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION);
    }

    static isOneGuard($t) {
        return ($t >= Elkaisar.Config.WUT_GUARD_SQUAD && $t <= Elkaisar.Config.WUT_GUARD_DIVISION);
    }

    static isGuardSquad($t) {
        return ($t == Elkaisar.Config.WUT_GUARD_SQUAD);
    }

    static isGuardBand($t) {
        return ($t == Elkaisar.Config.WUT_GUARD_BAND);
    }

    static isGuardSquadron($t) {
        return ($t == Elkaisar.Config.WUT_GUARD_SQUADRON);
    }

    static isGuardDivision($t) {
        return ($t == Elkaisar.Config.WUT_GUARD_DIVISION);
    }

    static isBraveThunder($t) {
        return ($t == Elkaisar.Config.WUT_BRAVE_THUNDER);
    }

    static isGangStar($t) {
        return ($t >= Elkaisar.Config.WUT_GANG && $t <= Elkaisar.Config.WUT_THIEF);
    }

    static isGang($t) {
        return ($t == Elkaisar.Config.WUT_GANG);
    }

    static isMugger($t) {
        return ($t == Elkaisar.Config.WUT_MUGGER);
    }

    static isThiefs($t) {
        return ($t == Elkaisar.Config.WUT_THIEF);
    }

    static isCarthagianArmies($t) {
        return ($t >= Elkaisar.Config.WUT_CARTHAGE_GANG && $t <= Elkaisar.Config.WUT_CARTHAGE_CAPITAL);
    }

    static isCarthasianGang($t) {
        return ($t == Elkaisar.Config.WUT_CARTHAGE_GANG);
    }

    static isCarthageTeams($t) {
        return ($t == Elkaisar.Config.WUT_CARTHAGE_TEAMS);
    }

    static isCarthageRebals($t) {
        return ($t == Elkaisar.Config.WUT_CARTHAGE_REBELS);
    }

    static isCarthageForces($t) {
        return ($t == Elkaisar.Config.WUT_CARTHAGE_FORCES);
    }

    static isCarthageCapital($t) {
        return ($t == Elkaisar.Config.WUT_CARTHAGE_CAPITAL);
    }

    static isArmyCapital($t) {
        return ($t >= Elkaisar.Config.WUT_ARMY_CAPITAL_A && $t <= Elkaisar.Config.WUT_ARMY_CAPITAL_F);
    }

    static isArmyCapitalA($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_A);
    }

    static isArmyCapitalB($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_B);
    }

    static isArmyCapitalC($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_C);
    }

    static isArmyCapitalD($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_D);
    }

    static isArmyCapitalE($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_E);
    }

    static isArmyCapitalF($t) {
        return ($t == Elkaisar.Config.WUT_ARMY_CAPITAL_F);
    }

    static isQueenCity($t) {
        return ($t >= Elkaisar.Config.WUT_QUEEN_CITY_A && $t <= Elkaisar.Config.WUT_QUEEN_CITY_C);
    }

    static isQueenCityS($t) {
        return ($t == Elkaisar.Config.WUT_QUEEN_CITY_A);
    }

    static isQueenCityM($t) {
        return ($t == Elkaisar.Config.WUT_QUEEN_CITY_B);
    }

    static isQueenCityH($t) {
        return ($t == Elkaisar.Config.WUT_QUEEN_CITY_C);
    }

    static isRepelCastle($t) {
        return ($t >= Elkaisar.Config.WUT_REPLE_CASTLE_A && $t <= Elkaisar.Config.WUT_REPLE_CASTLE_C);
    }

    static isRepelCastleS($t) {
        return ($t == Elkaisar.Config.WUT_REPLE_CASTLE_A);
    }

    static isRepelCastleM($t) {
        return ($t == Elkaisar.Config.WUT_REPLE_CASTLE_B);
    }

    static isRepelCastleH($t) {
        return ($t == Elkaisar.Config.WUT_REPLE_CASTLE_C);
    }

    static isStatueWar($t) {
        return ($t >= Elkaisar.Config.WUT_WAR_STATUE_A && $t <= Elkaisar.Config.WUT_WAR_STATUE_C);
    }

    static isStatueWarS($t) {
        return ($t == Elkaisar.Config.WUT_WAR_STATUE_A);
    }

    static isStatueWarM($t) {
        return ($t == Elkaisar.Config.WUT_WAR_STATUE_B);
    }

    static isStatueWarH($t) {
        return ($t == Elkaisar.Config.WUT_WAR_STATUE_C);
    }

    static isStatueWalf($t) {
        return ($t >= Elkaisar.Config.WUT_WOLF_STATUE_A && $t <= Elkaisar.Config.WUT_WOLF_STATUE_C);
    }

    static isStatueWalfS($t) {
        return ($t == Elkaisar.Config.WUT_WOLF_STATUE_A);
    }

    static isStatueWalfM($t) {
        return ($t == Elkaisar.Config.WUT_WOLF_STATUE_B);
    }

    static isStatueWalfH($t) {
        return ($t == Elkaisar.Config.WUT_WOLF_STATUE_C);
    }

    static canHasGarrison($t) {
        return (isCity($t) || isBarrary($t));
    }

    static isArena($t) {
        return ($t >= Elkaisar.Config.WUT_ARENA_CHALLANGE && $t <= Elkaisar.Config.WUT_ARENA_GUILD);
    }

    static isArenaChallange($t) {
        return ($t == Elkaisar.Config.WUT_ARENA_CHALLANGE);
    }

    static isArenaDeath($t) {
        return ($t == Elkaisar.Config.WUT_ARENA_DEATH);
    }

    static isArenaGuild($t) {
        return ($t == Elkaisar.Config.WUT_ARENA_GUILD);
    }

    static isChallangeField($t) {
        return ($t >= Elkaisar.Config.WUT_CHALLAGE_FIELD_PLAYER && $t <= Elkaisar.Config.WUT_CHALLAGE_FIELD_SERVER);
    }

    static isChallangeFieldPlayer($t) {
        return ($t == Elkaisar.Config.WUT_CHALLAGE_FIELD_PLAYER);
    }

    static isChallangeFieldTeam($t) {
        return ($t == Elkaisar.Config.WUT_CHALLAGE_FIELD_TEAM);
    }

    static isChallangeFieldGuild($t) {
        return ($t == Elkaisar.Config.WUT_CHALLAGE_FIELD_GUILD);
    }
    static isChallangeFieldServer($t) {
        return ($t == Elkaisar.Config.WUT_CHALLAGE_FIELD_SERVER);
    }

    static isFightField($t) {
        return ($t >= Elkaisar.Config.WUT_FIEGHT_FIELD_PLAYER && $t <= Elkaisar.Config.WUT_FIEGHT_FIELD_SERVER);
    }

    static isFightFieldPlayer($t) {
        return ($t == Elkaisar.Config.WUT_FIEGHT_FIELD_PLAYER);
    }

    static isFightFieldTeam($t) {
        return ($t == Elkaisar.Config.WUT_FIEGHT_FIELD_TEAM);
    }

    static isFightFieldGuild($t) {
        return ($t == Elkaisar.Config.WUT_FIEGHT_FIELD_PLAYER);
    }

    static isFightFieldServer($t) {
        return ($t == Elkaisar.Config.WUT_FIEGHT_FIELD_SERVER);
    }

    static isSeaCity($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_1 && $t <= Elkaisar.Config.WUT_SEA_CITY_6);
    }

    static isSeaCity_1($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_1);
    }
    static isSeaCity_2($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_2);
    }
    static isSeaCity_3($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_3);
    }
    static isSeaCity_4($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_4);
    }
    static isSeaCity_5($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_5);
    }
    static isSeaCity_6($t) {
        return ($t >= Elkaisar.Config.WUT_SEA_CITY_6);
    }

    static isSharablePrize($t) {
        return LWorldUnit.isCarthagianArmies($t) || LWorldUnit.isArenaDeath($t) || LWorldUnit.isCity($t) || LWorldUnit.isSeaCity($t);
    }

    static isEquipEfeective($t) {
        return !LWorldUnit.isArenaDeath($t);
    }

    static heroSysHasEquip($t) {
        return (LWorldUnit.isArenaDeath($t) || LWorldUnit.isArenaDeath($t));
    }

    static isGodGateEffective($t) {
        return !(LWorldUnit.isArenaDeath($t) || LWorldUnit.isArenaDeath($t));
    }

    static limitedHero($t) {
        return (LWorldUnit.isArmyCapital($t) || LWorldUnit.isCarthagianArmies($t) || LWorldUnit.isStatueWalf($t), LWorldUnit.isStatueWar($t) || LWorldUnit.isSeaCity($t));
    }
    
    static  isDefencable($t){
        return (!LWorldUnit.isCarthagianArmies($t) && !LWorldUnit.isStatueWalf($t) && !LWorldUnit.isStatueWar($t));
    }
    
    static  isGuildWar($t) {
        return LWorldUnit.isRepelCastle($t) ||
                LWorldUnit.isRepelCastle($t) ||
                LWorldUnit.isRepelCastle($t) ||
                LWorldUnit.isRepelCastle($t);
    }
    

    static afterWinAnnounceable(Unit, WinLvl) {
        if (LWorldUnit.isArena(Unit.ut))
            return true;
        if (LWorldUnit.isArmyCapital(Unit.ut))
            return true;
        if (LWorldUnit.isStatueWalf(Unit.ut))
            return true;
        if (LWorldUnit.isStatueWar(Unit.ut))
            return true;
        if (LWorldUnit.isQueenCity(Unit.ut) || LWorldUnit.isRepelCastle(Unit.ut))
            return true;

        if (LWorldUnit.isMonawrat(Unit["ut"]) || LWorldUnit.isCamp(Unit["ut"]))
            return (WinLvl % 10 === 0);

        return false;
    }

    static calDist(xFrom, xTo, yFrom, yTo) {
        var difX = Math.abs(xFrom - xTo);
        var difY = Math.abs(yFrom - yTo);
        if (difX > 249)
            difX -= 500;
        if (difY > 249)
            difY -= 500;
        return Math.floor(Math.sqrt(Math.pow((difX), 2) + Math.pow((difY), 2)) * 6000);
    }

    static calReturningTime(Hero, Unit) {
        if (parseInt(Hero.id_hero) <= 0)
            return 0;
        var now = Date.now() / 1000;
        var distance = LWorldUnit.calDist(Hero["x_coord"], Unit["x"], Hero["y_coord"], Unit["y"]);
        var slowestSpeed = Elkaisar.Lib.LHero.getReturningSlowestSpeed(Hero);
        var OrgRetTim = Math.ceil(distance / slowestSpeed);
        var returningTime = 15 * 60;

        if (
                LWorldUnit.isMonawrat(Unit.ut) || LWorldUnit.isAsianSquads(Unit.ut) || LWorldUnit.isGangStar(Unit.ut)
                || LWorldUnit.isCarthagianArmies(Unit.ut) || LWorldUnit.isCamp(Unit.ut) || LWorldUnit.isStatueWalf(Unit.ut)
                || LWorldUnit.isStatueWar(Unit.ut) || LWorldUnit.isQueenCity(Unit.ut))
            returningTime = now + Math.min(OrgRetTim, 15 * 60);
        else if (LWorldUnit.isSeaCity(Unit.ut))
            returningTime = now + 60 * 60;
        else if (LWorldUnit.isArena(Unit.ut) || LWorldUnit.isArmyCapital(Unit.ut))
            returningTime = now + 60;
        else
            returningTime = now + Math.min(OrgRetTim, 2 * 60 * 60);

        return returningTime;
    }

    static fireOffWorldUnit(x, y) {

        if (!Elkaisar.World.OnFireUnits[x * 500 + y])
            return;

        var CBattel = {};
        var BattelCount = 0;
        for (var iii in Elkaisar.Battel.BattelList) {
            CBattel = Elkaisar.Battel.BattelList[iii];
            if (!CBattel.Battel)
                continue;
            if (CBattel.Battel.x_coord !== x)
                continue;
            if (CBattel.Battel.y_coord !== y)
                continue;
            BattelCount++;
            if (BattelCount > 1)
                return;
        }

        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.Fire.Off",
            xCoord: x,
            yCoord: y
        }));
        delete(Elkaisar.World.OnFireUnits[x * 500 + y]);
        Elkaisar.DB.Update("s = 0", "world", `x = ? AND y = ?`, [x, y]);
    }

    static worldBattelEnded(Battel) {

        const x = Battel.x_coord;
        const y = Battel.y_coord;
        
        const WorldBattelKey = `${Battel.x_city}.${Battel.y_city}-${Battel.x_coord}.${Battel.x_coord}`;
    
        if (!Elkaisar.World.WorldBattels[WorldBattelKey])
            return;

        var CBattel = {};
        var BattelCount = 0;
        for (var iii in Elkaisar.Battel.BattelList) {
            CBattel = Elkaisar.Battel.BattelList[iii];
            if (!CBattel.Battel)
                continue;
            if (CBattel.Battel.x_coord !== x)
                continue;
            if (CBattel.Battel.y_coord !== y)
                continue;
            if (CBattel.Battel.x_city !== Battel.x_city)
                continue;
            if (CBattel.Battel.x_city !== Battel.x_city)
                continue;
            BattelCount++;
            if (BattelCount > 1)
                return;
        }
       
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.Battel.Ended",
            xCoord: Battel.x_coord,
            yCoord: Battel.y_coord,
            xCity: Battel.x_city,
            yCity: Battel.y_city
        }));
        delete(Elkaisar.World.WorldBattels[WorldBattelKey]);
    }

    

}

module.exports = LWorldUnit;