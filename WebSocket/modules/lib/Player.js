
module.exports.online = function (con) {

    var idPlayer = con.idPlayer;
    Elkaisar.DB.Update("`online` = 1", "player", "id_player = ?", [idPlayer]);
    Elkaisar.DB.Insert("id_player = ?, ipv4 = ?", "player_logs", [idPlayer, con.ip]);
    Elkaisar.DB.SelectFrom("*", "player_title", "id_player = ?", [idPlayer], function (PlayerTitle) {
        Elkaisar.DB.SelectFrom("*",
                "(SELECT player.*, @row:=@row+1 as 'rank' FROM player,(SELECT @row:=0) r ORDER BY player.prestige DESC ) AS col ",
                "col.id_player = ?", [idPlayer], function (PlayerData) {
            Elkaisar.Arr.Players[idPlayer] = {
                connection: con,
                playerTitles: [
                    PlayerTitle[0].title_1, PlayerTitle[0].title_2, PlayerTitle[0].title_3,
                    PlayerTitle[0].title_4, PlayerTitle[0].title_5, PlayerTitle[0].title_6,
                    PlayerTitle[0].title_5, PlayerTitle[0].title_8, PlayerTitle[0].title_7,
                    PlayerTitle[0].title_2
                ],
                playerData: PlayerData[0]
            };
        });
    });

};

module.exports.offline = function (con) {
    
    Elkaisar.Base.Request.postReq(
            {
                "server": con.idGameServer,
                "token": con.token,
                idLog: con.idLog
            }, `${Elkaisar.CONST.BASE_URL}/api/APlayer/offline`,
            function (data) {

            }

    );



};



module.exports.addPlayer = function (con) {

    var idPlayer = con.idPlayer;
    var player = Elkaisar.Arr.Players[idPlayer];

    if (player && player.connection) {

        player.connection.sendUTF(JSON.stringify({"classPath": "Player.someOneOppend"}));
        player.connection.close();
        delete(Elkaisar.Arr.Players[idPlayer]);

    }
    /*  
     con.sendUTF(JSON.stringify({
     classPath: "City.WorldCity",
     WorldCity: Elkaisar.Base.WorldUnitCity
     }))*/


    setTimeout(function () {
        module.exports.online(con);
    }, 1000);





};


