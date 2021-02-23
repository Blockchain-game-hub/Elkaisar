exports.armyCapitalUnLock = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.capitalUnLock",
        capital: msgObj
    });

    Elkaisar.Base.broadcast(msg);

};

exports.armyCapitalLock = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.capitalLock",
        capital: msgObj
    });

    Elkaisar.Base.broadcast(msg);

};

exports.BattelWin = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.Battel.Win",
        "Attacker": msgObj.Attacker,
        "Joiners": msgObj.Joiners,
        "Defender": msgObj.Defender,
        "EnemyName": msgObj.EnemyName,
        "WinPrize": msgObj.WinPrize,
        "honor": msgObj.honor,
        "WorldUnit": msgObj.WorldUnit
    });

    Elkaisar.Base.broadcast(msg);

};

exports.BattelStarted = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.Battel.Started",
        "GuildName": msgObj.GuildName,
        "PlayerName": msgObj.PlayerName,
        "slog_top": msgObj.slog_top,
        "slog_cnt": msgObj.slog_cnt,
        "slog_btm": msgObj.slog_btm,
        "id_guild": msgObj.id_guild,
        "id_player": msgObj.id_player,
        "xCoord": msgObj.x_coord,
        "yCoord": msgObj.y_coord
    });

    Elkaisar.Base.broadcast(msg);
};

exports.BattelGuildWin = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.Battel.GuildWin",
        "GuildName": msgObj.Guild.GuildName,
        "PlayerName": msgObj.Guild.PlayerName,
        "slog_top": msgObj.Guild.slog_top,
        "slog_cnt": msgObj.Guild.slog_cnt,
        "slog_btm": msgObj.Guild.slog_btm,
        "id_guild": msgObj.Guild.id_guild,
        "id_player": msgObj.Guild.id_player,
        "xCoord": msgObj.Battel.x_coord,
        "yCoord": msgObj.Battel.y_coord
    });

    Elkaisar.Base.broadcast(msg);

};

exports.BattelPlayerWin = function (con, msgObj) {

    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.Battel.Player.Win",
        "GuildName": msgObj.Player.GuildName,
        "PlayerName": msgObj.Player.PlayerName,
        "id_player": msgObj.Player.id_player,
        "xCoord": msgObj.Battel.x_coord,
        "yCoord": msgObj.Battel.y_coord
    });

    Elkaisar.Base.broadcast(msg);

};



exports.CityColonized = function (con, msgObj) {

    Elkaisar.WsLib.World.refreshWorldColonizedCities();
    var msg = JSON.stringify({
        "classPath": "ServerAnnounce.CityColonized",
        "ColonizerName": msgObj.ColonizerName,
        "ColonizedName": msgObj.ColonizedName,
        "CityColonizedName": msgObj.CityColonizedName,
        "xCoord": msgObj.xCoord,
        "yCoord": msgObj.yCoord,
        "ColonizerIdGuild": msgObj.ColonizerIdGuild,
        "ColonizerIdPlayer": msgObj.ColonizerIdPlayer,
        "CityColonizerFlag": msgObj.CityColonizerFlag
    });

    Elkaisar.Base.broadcast(msg);

};





