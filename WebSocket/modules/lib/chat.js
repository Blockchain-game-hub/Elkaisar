
function quoatedWorldMsg(pram) {
    return JSON.stringify({
        "classPath": "Chat.WorldMsg",
        "chatMsg": pram.msg,
        "fromName": pram.sender.name,
        "idFrom": pram.sender.id_player,
        "playerAvatar": pram.sender.avatar,
        "userGroup": pram.sender.user_group,
        "idMsg": (Math.floor(Date.now())) + "-" + Math.floor(Math.random() * (1 - 10000) + 1),
        "playerTitle": pram.title,
        "quoted": true,
        "quote": pram.data.quote,
        "quoteFrom": pram.data.quoteFrom,
        "PlayerData": {
            Porm: pram.sender.porm,
            Rank: pram.sender.rank
        }

    });
}

function unQuoatedWorldMsg(pram) {
    return JSON.stringify({

        "classPath": "Chat.WorldMsg",
        "chatMsg": pram.msg,
        "fromName": pram.sender.name,
        "idFrom": pram.sender.id_player,
        "playerAvatar": pram.sender.avatar,
        "userGroup": pram.sender.user_group,
        "idMsg": (Math.floor(Date.now())) + "-" + Math.floor(Math.random() * (1 - 10000) + 1),
        "playerTitle": pram.title,
        "quoted": false,
        "PlayerData": {
            Porm: pram.sender.porm,
            Rank: pram.sender.rank
        }

    });
}

exports.publicMsgImage = function (connection, msgObj) {

    var player = Elkaisar.Base.getPlayer(connection.idPlayer);
    if (!player)
        return;

    if (player.playerData.user_group < 5)
        return;
    if (player.playerData.porm < 4)
        return;

    var msg = JSON.stringify({
        "classPath": "Chat.sendMsgImage",
        "task": "world",
        "image": msgObj.image,
        "from_name": msgObj.p_name,
        "id_from": msgObj.idPlayer,
        "user_group": msgObj.userGroup
    });

    Elkaisar.Base.broadcast(msg);

};


exports.sendWorldMsg = function (con, msgObj) {

    var player = Elkaisar.Base.getPlayer(con.idPlayer);
    if (!player)
        return;

    if (player.playerData.chat_panne > Date.now() / 1000)
        return;

    if (msgObj.quoted) {

        Elkaisar.Base.broadcast(quoatedWorldMsg({
            msg: Elkaisar.Base.escapeHtml(msgObj.chat_msg),
            sender: player.playerData,
            title: player.playerTitles,
            data: msgObj
        }));

    } else {
        Elkaisar.Base.broadcast(unQuoatedWorldMsg({
            msg: Elkaisar.Base.escapeHtml(msgObj.chat_msg),
            sender: player.playerData,
            title: player.playerTitles,
            data: msgObj
        }));

    }



};


exports.sendGuildMsg = function (con, msgObj) {

    var player = Elkaisar.Base.getPlayer(con.idPlayer);
    if (!player)
        return;
    Elkaisar.DB.SelectFrom("id_player", "player", "id_guild = ? AND online = 1", [player.playerData.id_guild], function (Mems) {
        var sender = Elkaisar.Base.getPlayer(con.idPlayer);
        var msg = JSON.stringify({
            "classPath": "Chat.GuildMsg",
            "task": "guild",
            "chatMsg": Elkaisar.Base.escapeHtml(msgObj.chat_msg),
            "fromName": sender.playerData.name,
            "idFrom": con.idPlayer,
            "playerAvatar": sender.playerData.avatar,
            "playerTitle": sender.playerTitle,
            "quoted": false,
            "PlayerData": {
                Porm: sender.playerData.porm,
                Rank: sender.playerData.rank
            }
        });
        var p = null;
        for (var iii in Mems) {

            p = Elkaisar.Base.getPlayer(Mems[iii].id_player);
            if (p)
                p.connection.sendUTF(msg);
        }
    });

};

exports.sendMsg = function (connection, msgObj) {

    var chatTo = msgObj.chat_to;

    if (chatTo === "world") {
        this.sendWorldMsg(connection, msgObj);

    } else if (chatTo === "guild") {
        this.sendGuildMsg(connection, msgObj);
    }

};


exports.sendPrivate = function (con, msgObj) {

    var playerTo = Elkaisar.Base.getPlayer(msgObj.idPlayerTo);
    var playerFrom = Elkaisar.Base.getPlayer(con.idPlayer);
    var msg = Elkaisar.Base.escapeHtml(msgObj.chat_msg);

    if (playerTo) {

        playerTo.connection.sendUTF(JSON.stringify({
            "classPath": "Chat.privateMsg",
            "chatMsg": msg,
            "fromName": playerFrom.playerData.name,
            "idFrom": con.idPlayer,
            "playerFromAvatar": playerFrom.playerData.avatar,
            "idTo": msgObj.idPlayerTo,
            "playerToAvatar": playerTo.playerData.avatar
        }));

        con.sendUTF(JSON.stringify({
            "classPath": "Chat.privateMsg",
            "chatMsg": msg,
            "fromName": playerFrom.playerData.name,
            "idFrom": msgObj.idPlayerTo,
            "playerFromAvatar": playerFrom.playerData.avatar,
            "idTo": msgObj.idPlayerTo,
            "playerToAvatar": playerTo.playerData.avatar
        }));

    } else
        con.sendUTF(
                JSON.stringify({
                    "classPath": "Chat.NotOnline",
                    "chatMsg": msg,
                    "idFrom": msgObj.idPlayerTo
                }));

};

exports.delete = function (con, msgObj) {

    var player = Elkaisar.Base.getPlayer(con.idPlayer);

    if (!player)
        return;

    if (player.playerData.user_group < 2)
        return;


    Elkaisar.Base.broadcast(JSON.stringify({
        "classPath": "Chat.deleteMsg",
        "DeletedFor": msgObj.p_name_delete_for,
        "DeletedBy": player.playerData.name,
        "idMsg": msgObj.id_msg
    }));

};





