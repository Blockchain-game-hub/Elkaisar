

Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 7 * * *`, function () {
    Elkaisar.DB.QueryExc(`SET @r=0; UPDATE arena_player_challange SET rank= @r:= (@r+1) ORDER BY rank ASC;`);
    Elkaisar.DB.QueryExc(`SET @r=0; UPDATE arena_team_challange SET rank= @r:= (@r+1) ORDER BY rank ASC;`);
    Elkaisar.DB.QueryExc(`SET @r=0; UPDATE arena_guild_challange SET rank= @r:= (@r+1) ORDER BY rank ASC;`);
});



/*

Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 7 * * *`, function () {

    Elkaisar.DB.SelectFrom("x, y, lvl, id_city", "city", "1", [], function (Cities) {
        console.log(`City Counts Is ${Cities.length}`);
        Elkaisar.DB.Update("ut = 0, l = 0, s = 0", "world", `ut IN(${Elkaisar.Config.WUT_CITY_LVL_0}, ${Elkaisar.Config.WUT_CITY_LVL_1}, ${Elkaisar.Config.WUT_CITY_LVL_2}, ${Elkaisar.Config.WUT_CITY_LVL_3})`, [], function () {
            Cities.forEach(function (City, Index) {
                Elkaisar.DB.Update("ut = ?, t = ?, l = ?", "world", `x = ${City.x} AND y = ${City.y}`, [City.lvl + Elkaisar.Config.WUT_CITY_LVL_0, 17 + City.lvl, City.lvl + 1]);
                Elkaisar.Lib.LCity.refreshPopCap(City.id_city);
                Elkaisar.Lib.LCity.refreshStoreCap(City.id_city);
                
                Elkaisar.Lib.LSaveState.saveCityState(City.id_city);
                Elkaisar.Lib.LSaveState.foodOutState(City.id_player, City.id_city);
                Elkaisar.Lib.LSaveState.coinOutState(City.id_player, City.id_city);
                Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "wood");
                Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "stone");
                Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "metal");
                
                Elkaisar.Lib.LSaveState.coinInState(City.id_player, City.id_city);
                Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "food" );
                Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "wood" );
                Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "stone");
                Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "metal");
                
            });
        });

    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});

Elkaisar.DB.SelectFrom("x, y, lvl, id_city, id_player", "city", "1", [], function (Cities) {
    console.log(`City Counts Is ${Cities.length}`);
    Elkaisar.DB.Update("ut = 0, l = 0, s = 0", "world", `ut IN(${Elkaisar.Config.WUT_CITY_LVL_0}, ${Elkaisar.Config.WUT_CITY_LVL_1}, ${Elkaisar.Config.WUT_CITY_LVL_2}, ${Elkaisar.Config.WUT_CITY_LVL_3})`, [], function () {
        Cities.forEach(function (City, Index) {
            Elkaisar.DB.Update("ut = ?, t = ?, l = ?", "world", `x = ${City.x} AND y = ${City.y}`, [City.lvl + Elkaisar.Config.WUT_CITY_LVL_0, 17 + City.lvl, City.lvl + 1]);
            
            Elkaisar.Lib.LCity.refreshPopCap(City.id_city);
            Elkaisar.Lib.LCity.refreshStoreCap(City.id_city);
            
            Elkaisar.Lib.LSaveState.saveCityState(City.id_city);
            
            
            Elkaisar.Lib.LSaveState.foodOutState(City.id_player, City.id_city);
            Elkaisar.Lib.LSaveState.coinOutState(City.id_player, City.id_city);
            Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "wood");
            Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "stone");
            Elkaisar.Lib.LSaveState.resOutState(City.id_player, City.id_city, "metal");
            
            Elkaisar.Lib.LSaveState.coinInState(City.id_player, City.id_city);
            Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "food" );
            Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "wood" );
            Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "stone");
            Elkaisar.Lib.LSaveState.resInState(City.id_player, City.id_city, "metal");
            
        });
    });

});

*/

Elkaisar.Cron.schedule(`${Elkaisar.Base.rand(20, 40)} 6 * * *`, function () {

    Elkaisar.DB.QueryExc("INSERT IGNORE INTO city_storage(id_player, id_city) SELECT id_player, id_city FROM city", []);
    Elkaisar.DB.QueryExc("UPDATE server_data SET guild_num = (SELECT COUNT(*) FROM guild), city_num = (SELECT count(*) from city )", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO hero_army(id_player, id_hero) SELECT id_player, id_hero FROM hero", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO hero_medal (id_hero) SELECT id_hero FROM hero", []);
    Elkaisar.DB.QueryExc("UPDATE equip SET lvl = 1 WHERE lvl = 0", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO player_auth(id_player) SELECT id_player FROM player", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO player_edu(id_player) SELECT id_player FROM player", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO arena_player_challange_buy(id_player) SELECT id_player FROM player", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO arena_player_challange(id_player) SELECT id_player FROM player", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO arena_team_challange(id_team) SELECT id_team FROM team", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO arena_guild_challange(id_guild) SELECT id_guild FROM guild", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO city_theater(id_city, id_player) SELECT id_city, id_player FROM city", []);
    Elkaisar.DB.QueryExc("INSERT IGNORE INTO player_item(id_item, id_player, amount) SELECT item.id_item, player.id_player, item.startingAmount FROM item JOIN player WHERE 1", []);
    Elkaisar.DB.QueryExc("TRUNCATE `battel`");
    Elkaisar.DB.QueryExc("TRUNCATE `battel_member`");
    Elkaisar.DB.QueryExc("TRUNCATE `hero_back`");
    Elkaisar.DB.QueryExc(`UPDATE hero SET in_city = ${Elkaisar.Config.HERO_IN_CITY} WHERE in_city = 0`);
    
}, {
    scheduled: true,
    timezone: "Etc/UTC"
});









