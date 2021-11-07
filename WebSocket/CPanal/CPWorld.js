class CPWorld {
    Parm;
    constructor(ReqParm) {
        this.Parm = ReqParm;
    }

    async getUnit() {

        const xCoord = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Garrison = await Elkaisar.DB.ASelectFrom(
            "world_unit_garrison.*, player.name AS PlayerName, player.guild AS GuildName, hero.name AS HeroName",
            `world_unit_garrison  JOIN  player ON player.id_player = world_unit_garrison.id_player
              JOIN  hero ON hero.id_hero = world_unit_garrison.id_hero`,
            "world_unit_garrison.x_coord = ? AND world_unit_garrison.y_coord = ? ORDER BY ord ASC", [xCoord, yCoord]);

        const AttackQue = await Elkaisar.DB.ASelectFrom(
            "world_attack_queue.*, player.name AS PlayerName, guild.name AS GuildName, hero.name AS HeroName",
            `world_attack_queue LEFT JOIN  player ON player.id_player = world_attack_queue.id_player
             LEFT JOIN  hero ON hero.id_hero = world_attack_queue.id_hero LEFT JOIN  guild ON world_attack_queue.id_guild = guild.id_guild`,
            "world_attack_queue.x_coord = ? AND world_attack_queue.y_coord = ?", [xCoord, yCoord]);

        const Rank = await Elkaisar.DB.ASelectFrom(
            "world_unit_rank.*, player.name AS PlayerName, guild.name AS GuildName, hero.name AS HeroName",
            `world_unit_rank LEFT JOIN  player ON player.id_player = world_unit_rank.id_player
            LEFT JOIN  hero ON hero.id_hero = world_unit_rank.id_hero LEFT JOIN  guild ON world_unit_rank.id_guild = guild.id_guild`,
            "world_unit_rank.x = ? AND world_unit_rank.y = ? ORDER BY id_round DESC", [xCoord, yCoord]);

        const BarColonizer = await Elkaisar.DB.ASelectFrom(
            "city_bar.*, player.name AS PlayerName, city.name AS CityName",
            `city_bar LEFT JOIN  player ON player.id_player = city_bar.id_player
            LEFT JOIN  city ON city.id_city = city_bar.id_city`,
            "city_bar.x_coord = ? AND city_bar.y_coord = ? ", [xCoord, yCoord]);


        return {
            Unit: await Elkaisar.DB.ASelectFrom("*", "world", "x = ? AND y = ?", [xCoord, yCoord]),
            Heros: await Elkaisar.DB.ASelectFrom("*", "world_unit_hero", "x = ? AND y = ?", [xCoord, yCoord]),
            Equip: await Elkaisar.DB.ASelectFrom("*", "world_unit_equip", "x = ? AND y = ?", [xCoord, yCoord]),
            Garrison: Garrison,
            AtttackQue: AttackQue,
            Rank: Rank,
            BarColonizer: BarColonizer
        }
    }

    async ReAssignLvl() {
     
        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const lvlTo    = Elkaisar.Base.validateId(this.Parm.lvlTo);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);
        const UnitData = Elkaisar.World.WorldUnitData[Unit.ut];

        if (!UnitData)
            return { state: "error_0" }
        if(!UnitData.lvlChange)
            return { state: "error_1" }

        Elkaisar.DB.Update("l = ?", "world", "x = ? AND y = ?", [lvlTo, xCoord, yCoord]);
        Elkaisar.World.refreshWorldUnit();
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.LvlChangedByGM",
            xCoord: xCoord,
            yCoord: yCoord,
            lvlTo: lvlTo
        }));
        Unit.l = lvlTo;
        return { state: "ok" }
    }


    async LockWorldUnit() {
        
        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);
        Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [xCoord, yCoord]);
        Elkaisar.World.refreshWorldUnit();
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitLockedByGM",
            xCoord: xCoord,
            yCoord: yCoord
        }));
        Unit.lo = 1;
        return { state: "ok" }
    }



    async UnLockWorldUnit() {
        
        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);
        Elkaisar.DB.Update("lo = 0", "world", "x = ? AND y = ?", [xCoord, yCoord]);
        Elkaisar.World.refreshWorldUnit();
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitLockedByGM",
            xCoord: xCoord,
            yCoord: yCoord
        }));
        Unit.lo = 0;
        return { state: "ok" }
    }



    async FinishWorldUnitRound() {
        
        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);
        if(Elkaisar.Lib.LWorldUnit.isQueenCity(Unit.ut)){
            Elkaisar.Helper.CloseQueenCity(Unit.ut);
        }else if(Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit.ut)){
            Elkaisar.Helper.CloseRepleCastle(Unit.ut);
        }else if(Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut)){
            Elkaisar.Helper.CloseArmyCapital(Unit.ut);
        }else if(Unit.ut == Elkaisar.Config.WUT_CHALLAGE_FIELD_PLAYER){
            Elkaisar.Helper.CloseArenaChallange();
        }

        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitRoundFinishedByGM",
            xCoord: xCoord,
            yCoord: yCoord
        }));
        return { state: "ok" }
    }


    async StartWorldUnitRound(){
           
        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);

        if(Elkaisar.Lib.LWorldUnit.isQueenCity(Unit.ut)){
            Elkaisar.Helper.OpenQueenCity(Unit.ut);
        }else if(Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit.ut)){
            Elkaisar.Helper.OpenRepleCastle(Unit.ut);
        }else if(Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut)){
            Elkaisar.Helper.OpenArmyCapital(Unit.ut);
        }
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitRoundStartedByGM",
            xCoord: xCoord,
            yCoord: yCoord
        }));

        return { state: "ok" }
    }

    async DeleteWorldUnitRankById(){
           
        const idRound   = Elkaisar.Base.validateId(this.Parm.idRound);
        Elkaisar.DB.Delete("world_unit_rank", "id_round = ?", [idRound])
        return { state: "ok" }
    }


    async ClearWorldUnitRank(){

        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);

        Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [xCoord, yCoord]);

        return { state: "ok" }

    }


    async DeleteWorldUnitQueById(){

        const  idQue   = Elkaisar.Base.validateId(this.Parm.idQue);

        Elkaisar.DB.Delete("world_attack_queue", "id", [idQue]);

        return { state: "ok" }

    }


    async ClearWorldUnitQue(){

        const xCoord   = Elkaisar.Base.validateId(this.Parm.xCoord);
        const yCoord   = Elkaisar.Base.validateId(this.Parm.yCoord);
        const Unit     = Elkaisar.World.getUnit(xCoord, yCoord);

        Elkaisar.DB.Delete("world_attack_queue", "x_coord = ? AND y_coord = ?", [xCoord, yCoord]);

        return { state: "ok" }

    }
    ResetMnawratLvl(){
        const UnitTypeToChange = [Elkaisar.Config.WUT_MONAWRAT];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    ResetCampLvl(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_CAMP_ASIANA, Elkaisar.Config.WUT_CAMP_BRITONS, Elkaisar.Config.WUT_CAMP_CARTHAGE,
            Elkaisar.Config.WUT_CAMP_EGYPT, Elkaisar.Config.WUT_CAMP_GAULS, Elkaisar.Config.WUT_CAMP_HISPANIA,
            Elkaisar.Config.WUT_CAMP_ITALIA, Elkaisar.Config.WUT_CAMP_MACEDON, Elkaisar.Config.WUT_CAMP_PARTHIA,
             Elkaisar.Config.WUT_CAMP_REICH
             ];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    ResetMofedLvl(){
        const UnitTypeToChange = [Elkaisar.Config.WUT_CARTHAGE_REBELS];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    ResetGeneralLvl(){
        const UnitTypeToChange = [Elkaisar.Config.WUT_CARTHAGE_FORCES];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    
    ResetMarshelLvl(){
        const UnitTypeToChange = [Elkaisar.Config.WUT_CARTHAGE_CAPITAL];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    
    ResetAsiaOne(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_DIVISION,
            Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_SQUADRON];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }

    ResetAsiaTwo(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
            Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    ResetAsiaThree(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
            Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    ResetAsiaFour(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_DIVISION,
            Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_SQUADRON];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    ResetAsiaFive(){
        const UnitTypeToChange = [
            Elkaisar.Config.WUT_BRAVE_THUNDER];
        Elkaisar.DB.Update("l = ?", "world", `ut IN ( ${UnitTypeToChange.join(",")} )`, [this.Parm.LvlTo]);
        Elkaisar.World.refreshWorldUnit();
        const LvlTo = this.Parm.LvlTo;
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: UnitTypeToChange,
            lvlTo:LvlTo
        }));
    }
    
    changeLvlByType(){
        
        const lvlTo    = Elkaisar.Base.validateId(this.Parm.lvlTo);
        const UnitType = Elkaisar.Base.validateId(this.Parm.UnitType);
        const UnitData = Elkaisar.World.WorldUnitData[UnitType];

        if (!UnitData)
            return { state: "error_0" }
        if(!UnitData.lvlChange)
            return { state: "error_1" }
        
        Elkaisar.DB.Update("l = ?", "world", "ut = ?", [lvlTo, UnitType]);
        Elkaisar.World.refreshWorldUnit();
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "World.UnitTypeLvlChange",
            UnitType: [UnitTypeToChange],
            lvlTo: lvlTo
        }));
        Unit.l = lvlTo;
        return { state: "ok" }
    }


}



module.exports = CPWorld;