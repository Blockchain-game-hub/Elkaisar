

exports.newMessageSent = function (con, msgObj) {


    Elkaisar.Base.Request.postReq(
            {
                "GUILD_ONLINE_PLAYER": true,
                "id_guild": msgObj.id_guild,
                "server": con.idGameServer,
                "token": con.token
            },
            `${Elkaisar.CONST.BASE_URL}/api/AGuildMember/getOnlineMember`,
            function (data) {
                var guildPlayers = Elkaisar.Base.isJson(data);

                if (!guildPlayers)
                    return;

                var msg = JSON.stringify({
                    "classPath": "Guild.msgSent"
                });
                var ii;
                var player;

                for (ii in guildPlayers) {

                    player = Elkaisar.Base.getPlayer(guildPlayers[ii].id_player);
                    if (!player)
                        continue;

                    player.connection.sendUTF(msg);

                }
            }
    );

};


exports.JoinInvSent = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinInvSent"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }

};

exports.JoinInvRejected = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinInvRejected"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }

};

exports.JoinInvAccepted = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinInvAccepted"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }
    Elkaisar.WsLib.World.refreshWorldCities();
};

exports.JoinReqCanceled = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinReqCanceled"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }

};

exports.JoinReqSent = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinReqSent"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }

};



exports.JoinReqAccepted = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinReqAccepted"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }
    Elkaisar.WsLib.World.refreshWorldCities();

};

exports.JoinReqRejected = function (con, msgObj)
{

    var index;
    var Player;
    var msg = JSON.stringify({
        "classPath": "Guild.JoinReqRejected"
    })
    for (index in msgObj.Players)
    {
        Player = Elkaisar.Base.getPlayer(msgObj.Players[index]);
        if (!Player)
            continue;
        Player.connection.sendUTF(msg);
    }

};


exports.announceRelation = function (con, msgObj) {

    Elkaisar.DB.SelectFrom("name", "guild", `id_guild = ${msgObj.idGuildOne}`, [], function (GuildOne) {
        Elkaisar.DB.SelectFrom("name", "guild", `id_guild = ${msgObj.idGuildTwo}`, [], function (GuildTwo) {
            Elkaisar.WsLib.World.refreshWorldCities(function () {
                Elkaisar.Base.broadcast(JSON.stringify({
                    "classPath": "Guild.announceRelation",
                    "PlayerName": msgObj.PlayerName,
                    "GuildNameOne": GuildOne[0].name,
                    "GuildNameTwo": GuildTwo[0].name,
                    "idGuildOne": msgObj.idGuildOne,
                    "idGuildTwo": msgObj.idGuildTwo,
                    "relation": msgObj.relation
                }));
            });

        });
    });




};






Elkaisar.Cron.schedule("1 * * * *", function () {

    Elkaisar.DB.SelectFrom("id_guild", "guild", "1", [], function (Guilds) {
        Guilds.forEach(function (Guild, Index) {
            Elkaisar.DB.Update(
                    `mem_num = (SELECT COUNT(*) FROM guild_member WHERE id_guild = ?) ,
            prestige = (SELECT SUM(player.prestige) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = ?) ,
            honor = (SELECT SUM(player.honor) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = ?)`
                    , "guild", "id_guild = ?", [Guild.id_guild, Guild.id_guild, Guild.id_guild, Guild.id_guild]);
        });
    });
});


Elkaisar.DB.SelectFrom("id_guild", "guild", "1", [], function (Guilds) {
    Guilds.forEach(function (Guild, Index) {
        Elkaisar.DB.Update(
            `mem_num = (SELECT COUNT(*) FROM guild_member WHERE id_guild = ?) ,
            prestige = (SELECT SUM(player.prestige) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = ?) ,
            honor = (SELECT SUM(player.honor) FROM player JOIN guild_member ON player.id_player = guild_member.id_player WHERE guild_member.id_guild = ?)`
            , "guild", "id_guild = ?", [Guild.id_guild, Guild.id_guild, Guild.id_guild, Guild.id_guild]);
    });
});