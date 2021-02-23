
Elkaisar.Lib.LBase.ResetWorldLvl = function (UnitList){
    Elkaisar.DB.Update(`l = 1`, "world", `ut IN(${UnitList.join(", ")})`, function () {
        Elkaisar.World.refreshWorldUnit();
    });
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: 'World.ResetLvl',
        UnitList : UnitList
    }));
};


Elkaisar.Lib.LBase.DailyReset = function (){
    Elkaisar.DB.Delete("world_prize_taken", "1");
    Elkaisar.DB.Update("take_times = 0", "exchange_player", "1");
    Elkaisar.DB.Update("points = points + 25", "god_gate", "points < 4000");
    Elkaisar.DB.Update("done = 0", "quest_player", "id_quest IN(SELECT id_quest FROM quest WHERE refresh = 1)");
    Elkaisar.DB.Update("attempt = 10", "arena_player_challange", "1");
    Elkaisar.DB.Update("buy_times = 0", "arena_player_challange_buy", "1");
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: 'Base.DailyRest'
    }));
    
};

Elkaisar.Cron.schedule("0 0 * * *", function () {

    Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);
    Elkaisar.Lib.LBase.DailyReset();
    
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});

Elkaisar.Cron.schedule("0 2 * * *", function () {
    var UnitList = [Elkaisar.Config.WUT_CARTHAGE_FORCES];
    Elkaisar.DB.Update(`l = 1`, "world", `ut IN(${UnitList.join(", ")})`, function () {
        Elkaisar.World.refreshWorldUnit();
    });
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 4 * * *", function () {
    Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Cron.schedule("0 8 * * *", function () {
   Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_MONAWRAT, Elkaisar.Config.WUT_CAMP_BRITONS, Elkaisar.Config.WUT_CAMP_REICH,
        Elkaisar.Config.WUT_CAMP_ASIANA, Elkaisar.Config.WUT_CAMP_GAULS, Elkaisar.Config.WUT_CAMP_MACEDON,
        Elkaisar.Config.WUT_CAMP_HISPANIA, Elkaisar.Config.WUT_CAMP_ITALIA, Elkaisar.Config.WUT_CAMP_PARTHIA,
        Elkaisar.Config.WUT_CAMP_CARTHAGE, Elkaisar.Config.WUT_CAMP_EGYPT,
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS, Elkaisar.Config.WUT_CARTHAGE_FORCES,
        Elkaisar.Config.WUT_CARTHAGE_CAPITAL, Elkaisar.Config.WUT_WAR_STATUE_A, Elkaisar.Config.WUT_WAR_STATUE_B, Elkaisar.Config.WUT_WAR_STATUE_C,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 14 * * *", function () {
    var UnitList = [Elkaisar.Config.WUT_CARTHAGE_FORCES];
    Elkaisar.DB.Update(`l = 1`, "world", `ut IN(${UnitList.join(", ")})`, function () {
        Elkaisar.World.refreshWorldUnit();
    });
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 12 * * *", function () {
    Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 16 * * *", function () {
    Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 20 * * *", function () {
    Elkaisar.Lib.LBase.ResetWorldLvl([
        Elkaisar.Config.WUT_MONAWRAT, Elkaisar.Config.WUT_CAMP_BRITONS, Elkaisar.Config.WUT_CAMP_REICH,
        Elkaisar.Config.WUT_CAMP_ASIANA, Elkaisar.Config.WUT_CAMP_GAULS, Elkaisar.Config.WUT_CAMP_MACEDON,
        Elkaisar.Config.WUT_CAMP_HISPANIA, Elkaisar.Config.WUT_CAMP_ITALIA, Elkaisar.Config.WUT_CAMP_PARTHIA,
        Elkaisar.Config.WUT_CAMP_CARTHAGE, Elkaisar.Config.WUT_CAMP_EGYPT,
        Elkaisar.Config.WUT_FRONT_SQUAD, Elkaisar.Config.WUT_FRONT_BAND, Elkaisar.Config.WUT_FRONT_SQUADRON, Elkaisar.Config.WUT_FRONT_DIVISION,
        Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD, Elkaisar.Config.WUT_ARMY_LIGHT_BAND, Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON, Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION,
        Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD, Elkaisar.Config.WUT_ARMY_HEAVY_BAND, Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON, Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION,
        Elkaisar.Config.WUT_GUARD_SQUAD, Elkaisar.Config.WUT_GUARD_BAND, Elkaisar.Config.WUT_GUARD_SQUADRON, Elkaisar.Config.WUT_GUARD_DIVISION,
        Elkaisar.Config.WUT_BRAVE_THUNDER, Elkaisar.Config.WUT_GANG, Elkaisar.Config.WUT_MUGGER, Elkaisar.Config.WUT_THIEF,
        Elkaisar.Config.WUT_CARTHAGE_GANG, Elkaisar.Config.WUT_CARTHAGE_TEAMS, Elkaisar.Config.WUT_CARTHAGE_REBELS, Elkaisar.Config.WUT_CARTHAGE_FORCES,
        Elkaisar.Config.WUT_CARTHAGE_CAPITAL, Elkaisar.Config.WUT_WAR_STATUE_A, Elkaisar.Config.WUT_WAR_STATUE_B, Elkaisar.Config.WUT_WAR_STATUE_C,
        Elkaisar.Config.WUT_WOLF_STATUE_A, Elkaisar.Config.WUT_WOLF_STATUE_B, Elkaisar.Config.WUT_WOLF_STATUE_C
    ]);
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Cron.schedule("51 * * * *", function (){
    Elkaisar.DB.QueryExc(`UPDATE guild t2
                            JOIN player t1 ON t1.id_guild = t2.id_guild
                            SET t2.prestige = (SELECT SUM(player.prestige) FROM player WHERE player.id_guild = t2.id_guild),
                                 t2.honor   = (SELECT SUM(player.honor)    FROM player WHERE player.id_guild = t2.id_guild)`);
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Cron.schedule("47 * * * *", function (){
    Elkaisar.DB.QueryExc(
            `UPDATE hero SET in_city = ${Elkaisar.Config.HERO_IN_CITY} WHERE id_hero NOT IN( SELECT id_hero FROM battel_member) AND id_hero NOT IN(SELECT id_hero FROM hero_back)`, [], function (){
                Elkaisar.DB.QueryExc(`UPDATE hero JOIN world_unit_garrison ON world_unit_garrison.id_hero = hero.id_hero SET hero.in_city = ${Elkaisar.Config.HERO_IN_GARISON}`, [], function (){
                   
                   Elkaisar.DB.QueryExc(`UPDATE hero SET in_city = ${Elkaisar.Config.HERO_IN_CITY}  WHERE id_hero IN
                                            (   
                                                SELECT world_unit_garrison.id_hero FROM world_unit_garrison JOIN 
                                                city on city.x = world_unit_garrison.x_coord AND city.y = world_unit_garrison.y_coord
                                                WHERE world_unit_garrison.id_player = city.id_player
                                            )`);
                    
                });
            });
});