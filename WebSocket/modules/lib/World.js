

exports.refreshWorldCities = function (callBack) {
    Elkaisar.DB.SelectFrom(
            "player.id_guild AS ig, player.id_player AS ip, city.id_city AS ic, city.x, city.y, city.lvl AS l, player.city_flag AS f",
            "player JOIN  city ON city.id_player = player.id_player ",
            "1", [], function (Res) {

        Elkaisar.AllWorldCity = Res;
        if (callBack)
            callBack();
    });
};

exports.refreshWorldColonizedCities = function (callBack) {
    Elkaisar.DB.SelectFrom(
            "player.id_guild, player.id_player, city.x, city.y, city.lvl, player.city_flag",
            "city_colonize JOIN city ON city.id_city = city_colonize.id_city_colonized JOIN player ON city_colonize.id_colonizer = player.id_player",
            "1",
            [], function (Res) {
        Elkaisar.AllWorldCityColonized = Res;
        if (callBack)
            callBack();
    });
};

exports.refreshWorldCities();
exports.refreshWorldColonizedCities();




exports.updateWorldPrize = function (con, msgObject) {

    
    var unitLvl = msgObject.unitLvl;
    var unitType = msgObject.unitType;
    var amountMin = msgObject.amountMin;
    var amountMax = msgObject.amountMax;
    var winRate = msgObject.winRate;
    var Item = msgObject.Item;
    var isSpecilPrize = msgObject.isSp;

    var MoreTypes = msgObject.MoreTypes.split(",");
    var MoreLvls = msgObject.MoreLvls.split(",");

console.log(MoreTypes.some(i => i > 0))

    if (msgObject.isWin == true) {
        console.log(msgObject)
        
        if (MoreTypes.length && MoreLvls.length && MoreTypes.some(i => i > 0)) {
            for (var ii of MoreTypes) {
                for (var i of MoreLvls) {
                    Elkaisar.DB.Insert(
                            "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                            "world_unit_prize",
                            [ii, i, Item, amountMin, amountMax, winRate], function () {
                        Elkaisar.World.refreshWorldUnitPrize();
                        console.log("das", MoreTypes, MoreLvls)
                    });
                }
            }
        } else {
            Elkaisar.DB.Insert(
                    "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                    "world_unit_prize",
                    [unitType, unitLvl, Item, amountMin, amountMax, winRate], function () {
                Elkaisar.World.refreshWorldUnitPrize();
                console.log("sadsad")
            });
        }
    }


    if (msgObject.isLose == true) {
        if (MoreTypes.length && MoreLvls.length && MoreTypes.some(i => i > 0)) {
            for (var ii of MoreTypes) {
                for (var i of MoreLvls) {
                    Elkaisar.DB.Insert(
                            "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                            "world_unit_prize_lose",
                            [ii, i, Item, amountMin, amountMax, winRate], function () {
                        Elkaisar.World.refreshWorldUnitPrize();
                    });
                }
            }
        } else {
            Elkaisar.DB.Insert(
                    "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                    "world_unit_prize_lose",
                    [unitType, unitLvl, Item, amountMin, amountMax, winRate], function () {
                Elkaisar.World.refreshWorldUnitPrize();
            });
        }
    }


    if (msgObject.isSp == true) {
        if (MoreTypes.length && MoreLvls.length && MoreTypes.some(i => i > 0)) {
            for (var ii of MoreTypes) {
                for (var i of MoreLvls) {
                    Elkaisar.DB.Insert(
                            "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                            "world_unit_prize_sp",
                            [ii, i, Item, amountMin, amountMax, winRate], function () {
                        Elkaisar.World.refreshWorldUnitPrize();
                    });
                }
            }
        } else {
            Elkaisar.DB.Insert(
                    "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                    "world_unit_prize_sp",
                    [unitType, unitLvl, Item, amountMin, amountMax, winRate], function () {
                Elkaisar.World.refreshWorldUnitPrize();
            });
        }
    }


    if (msgObject.isPlunder == true) {
        if (MoreTypes.length && MoreLvls.length && MoreTypes.some(i => i > 0)) {
            for (var ii of MoreTypes) {
                for (var i of MoreLvls) {
                    Elkaisar.DB.Insert(
                            "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                            "world_unit_prize_plunder",
                            [ii, i, Item, amountMin, amountMax, winRate], function () {
                        Elkaisar.World.refreshWorldUnitPrize();
                    });
                }
            }
        } else {
            Elkaisar.DB.Insert(
                    "unitType = ?, lvl = ?, prize = ?, amount_min = ?, amount_max = ?, win_rate = ?",
                    "world_unit_prize_plunder",
                    [unitType, unitLvl, Item, amountMin, amountMax, winRate], function () {
                Elkaisar.World.refreshWorldUnitPrize();
            });
        }
    }






    con.sendUTF(JSON.stringify({
        classPath: "WorldUnitPrize.prizeChanged"
    }));

};


exports.removeWorldPrize = function (con, msgObject) {


    var idPrize = msgObject.idPrize;
    var PrizeFor = msgObject.PrizeFor;


    if (PrizeFor == "Win")
        Elkaisar.DB.Delete("world_unit_prize", "id_prize = ?", [idPrize], function () {
            Elkaisar.World.refreshWorldUnitPrize();
        });

    if (PrizeFor == "Lose")
        Elkaisar.DB.Delete("world_unit_prize_lose", "id_prize = ?", [idPrize], function () {
            Elkaisar.World.refreshWorldUnitPrize();
        });

    if (PrizeFor == "Sp")
        Elkaisar.DB.Delete("world_unit_prize_sp", "id_prize = ?", [idPrize], function () {
            Elkaisar.World.refreshWorldUnitPrize();
        });

    if (PrizeFor == "Plunder")
        Elkaisar.DB.Delete("world_unit_prize_plunder", "id_prize = ?", [idPrize], function () {
            Elkaisar.World.refreshWorldUnitPrize();
        });





    con.sendUTF(JSON.stringify({
        classPath: "WorldUnitPrize.prizeChanged"
    }));

};


exports.getWorldCity = function (con, msgObject) {

    var partNum = Math.ceil(Elkaisar.AllWorldCity.length / 300);

    for (var iii = 0; iii < partNum; iii++)
        con.sendUTF(JSON.stringify({
            classPath: "Base.worldCity",
            Part: iii,
            City: Elkaisar.AllWorldCity.slice(iii * 300, (iii + 1) * 300)
        }));

};

exports.getWorldFireUnit = function (con, msgObject) {

    con.sendUTF(JSON.stringify({
        classPath: "Base.WorldFireUnit",
        Units: Elkaisar.World.OnFireUnits
    }));

};

exports.getWorldCityColonized = function (con, msgObject) {

    var partNum = Math.ceil(Elkaisar.AllWorldCityColonized.length / 300);

    for (var iii = 0; iii < partNum; iii++)
        con.sendUTF(JSON.stringify({
            classPath: "Base.worldCityColonized",
            Part: iii,
            City: Elkaisar.AllWorldCityColonized.slice(iii * 300, (iii + 1) * 300)
        }));

};


exports.getWorldCityFlag = function (con, msgObject)
{


};

exports.refreshWorldCitiesForPlayers = function (con, msgObject)
{
    var msg = JSON.stringify({
        classPath: "Base.refreshWorldCitiesForPlayers",
        idGuild: msgObject.idGuild,
        CityLvl: 0,
        idCity: msgObject.idCity,
        idPlayer: msgObject.idPlayer,
        CityFlag: msgObject.CityFlag,
        xCoord: msgObject.xCoord,
        yCoord: msgObject.yCoord
    });
    Elkaisar.Base.broadcast(msg);

};



exports.refreshWorldUnit = function (con, msgObject) {
    exports.refreshWorldCities(function () {
        exports.refreshWorldColonizedCities(function () {
            var WorldNewUnits = [];
            msgObject.Units.forEach(function (Unit, Index) {
                Elkaisar.DB.SelectFrom("*", "world", "x = ? AND y = ?", [Unit.x, Unit.y], function (WorldUnit) {
                    Elkaisar.World.WorldUnits[Number(Unit.x * 500) + Number(Unit.y)] = WorldUnit[0];
                    WorldNewUnits.push(WorldUnit[0]);

                    if (WorldNewUnits.length === msgObject.Units.length) {
                        var msg = JSON.stringify({
                            classPath: "World.RefereshWorldUnit",
                            WorldUnits: WorldNewUnits
                        });
                        Elkaisar.Base.broadcast(msg);
                    }
                });
            });
        });
    });
};