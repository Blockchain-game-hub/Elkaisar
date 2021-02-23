

Elkaisar.Helper.BattelStartAnnounce = function (Battel) {

    var Unit = Elkaisar.World.getUnit(Battel.Battel.x_coord, Battel.Battel.y_coord);


    if (Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut)) {

        Elkaisar.DB.SelectFrom("player.name AS PlayerName, city.name AS CityName, city.x, city.y",
                "city JOIN player ON player.id_player = city.id_player",
                "city.x = ? AND city.y = ?", [Battel.Battel.x_city, Battel.Battel.y_city], function (Attack) {
            Elkaisar.DB.SelectFrom("id_dominant", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Unit["x"], Unit["y"]],
                    function (Defence) {
                        
                        if(!Defence.length)
                            return ;

                        P = Elkaisar.Base.getPlayer(Defence[0]["id_dominant"]);
                        if (P)
                            P.connection.sendUTF(JSON.stringify({
                                "classPath": "Battel.startAnnounce",
                                "Battel": Battel.Battel,
                                "Attacker": Attack[0],
                                "Defender": Defence[0]
                            }));

                    });
        });

    } else if (Elkaisar.Lib.LWorldUnit.isCity(Unit.ut)) {

        Elkaisar.DB.SelectFrom("player.name AS PlayerName, city.name AS CityName, city.x, city.y",
                "city JOIN player ON player.id_player = city.id_player",
                "city.x = ? AND city.y = ?", [Battel.Battel.x_city, Battel.Battel.y_city], function (Attack) {
            Elkaisar.DB.SelectFrom("player.name AS PlayerName, city.name AS CityName, city.x, city.y, player.id_guild, player.id_player",
                    "city JOIN player ON player.id_player = city.id_player",
                    "city.x = ? AND city.y = ?", [Battel.Battel.x_coord, Battel.Battel.y_coord], function (Defence) {
                Elkaisar.DB.SelectFrom("id_player", "player", "online = 1 AND id_guild = ?", [Defence[0]["id_guild"]], function (Players) {

                    var Msg = JSON.stringify({
                        "classPath": "Battel.startAnnounce",
                        "Battel": Battel.Battel,
                        "Attacker": Attack[0],
                        "Defender": Defence[0]
                    });
                    var P;
                    if (Players.length && Defence[0]["id_guild"] != null)
                        Players.forEach(function (Player, Index) {
                            P = Elkaisar.Base.getPlayer(Player["id_player"]);
                            if (P)
                                P.connection.sendUTF(Msg);
                        });
                    else {
                        P = Elkaisar.Base.getPlayer(Defence[0]["id_player"]);
                        if (P)
                            P.connection.sendUTF(Msg);
                    }
                });
            });
        });
    }

};

exports.abort = function (con, msgObj) {

    var idHero = msgObj.idHero;
    var Player = {};
    Elkaisar.Lib.LBattel.removeHeroFromBattel(idHero, function (Players) {
        Players.forEach(function (idPlayer, Index) {
            player = Elkaisar.Base.getPlayer(idPlayer);
            if (player) {
                player.connection.sendUTF(JSON.stringify({
                    "classPath": "Battel.Aborted"
                }));
            }
        });
    });

    Elkaisar.Base.Request.postReq(
            {
                "idHero": msgObj.idHero,
                "server": con.idGameServer,
                "token": con.token
            },
            `${Elkaisar.CONST.BASE_URL}/api/ABattel/abort`,
            function (data) {
                var arrayData = Elkaisar.Base.isJson(data);
                if (!arrayData)
                    return;
                var ii;
                var player;



                if (arrayData.state === "ok")
                    for (ii in arrayData.idPlayers) {
                        player = Elkaisar.Base.getPlayer(arrayData.idPlayers[ii]);
                        if (player) {
                            player.connection.sendUTF(JSON.stringify({
                                "classPath": "Battel.Aborted",
                                "Battel": arrayData.Battel,
                                "idBattel": arrayData.idBattel,
                                "state": arrayData.state
                            }));
                        }
                    }
                else if (arrayData.state === "error_2")
                    con.sendUTF(JSON.stringify({
                        "classPath": "Battel.Aborted.Failed",
                        "state": arrayData.state
                    }));


            }
    );

};


exports.Join = function (con, msgObj) {

    Elkaisar.Lib.LBattel.heroJoinedBattel(msgObj.Hero, msgObj.Battel);

};

exports.newBattelStarted = function (con, msgObject) {
    Elkaisar.Lib.LBattel.newBattelStarted(msgObject.Battel);
}

exports.start = function (con, msgObj) {
    var Parm = {
        "xCoord": msgObj.xCoord,
        "yCoord": msgObj.yCoord,
        "idHero": msgObj.idHero,
        "task": msgObj.attackTask,
        "server": con.idGameServer,
        "token": con.token,
        idPlayerV: con.idPlayer

    };

    Elkaisar.Base.Request.postReq(
            Parm,
            `${Elkaisar.CONST.BASE_URL}/api/ABattel/start`,
            function (data) {

                var battel = Elkaisar.Base.isJson(data);
                if (!battel)
                    return console.log(data);

                if (battel.state === "ok") {


                    Elkaisar.Helper.BattelStartAnnounce(battel);

                    var Player;
                    for (var ii in battel.InvolvedPlayer) {

                        Player = Elkaisar.Base.getPlayer(battel.InvolvedPlayer[ii]);
                        if (Player) {

                            Player.connection.sendUTF(JSON.stringify({
                                "classPath": "Battel.Started",
                                "Battel": battel.Battel,
                                "StartingPrice": battel.StartingPrice,
                                "state": "ok"
                            }));


                        } else {
                            console.log(battel.InvolvedPlayer);
                        }

                    }




                } else {

                    con.sendUTF(JSON.stringify({
                        "classPath": "Battel.StartFailed",
                        "state": battel.state
                    }));
                }

                if (battel.newFire)
                    Elkaisar.Base.broadcast(JSON.stringify({
                        classPath: "World.Fire.On",
                        xCoord: battel.Battel.x_coord,
                        yCoord: battel.Battel.y_coord
                    }));

                exports.watchListNewBattelNotif(con, msgObj);
                Elkaisar.WsLib.BattelWatchList.addPlayer(con, msgObj);


            }
    );

};

exports.watchListNewBattelNotif = function (con, msgObj) {

    var key = `${msgObj.x_to}-${msgObj.y_to}`;
    var WList = Elkaisar.Arr.BattelWatchList[key];
    if (!WList)
        return;

    var ii;
    var player;
    var msg = JSON.stringify({
        "classPath": "BattelWatchList.newBattel",
        "task": "YOUR_CITY_FIRE",
        "battel": {
            "x_coord": msgObj.x_to,
            "y_coord": msgObj.y_to
        }
    });
    for (ii in WList) {

        player = Elkaisar.Base.getPlayer(WList[ii]);
        if (!player)
            continue;

        player.connection.sendUTF(msg);


    }

};


exports.BattelCanceled = function (con, msgObj)
{
    var ii;
    var Players = msgObj.Players;
    var msg = JSON.stringify({
        "classPath": "Battel.Canceled"
    });

    for (ii in Players) {

        player = Elkaisar.Base.getPlayer(Players[ii]);
        if (!player)
            continue;

        player.connection.sendUTF(msg);

    }


};


