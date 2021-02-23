
exports.SchadualedAnnounce = [];

exports.SendMailToServer = function (con, data) {


    var Now = Math.floor(Date.now() / 1000);
    Elkaisar.DB.QueryExc(
            "INSERT INTO msg_income(`id_from`,`id_to`,`head`,`body`,`from_` , time_stamp) SELECT 0 , id_player , '" + data.Title + "', '" + data.MsgBody + "', 1 , " + Now + " FROM player WHERE 1", [], function () {
        Elkaisar.Base.broadcast(JSON.stringify({
            classPath: "ServerAnnounce.newMailSent"
        }));
    });
};


exports.SendServerAnnounce = function (con, data) {
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.NewServerAnnounce",
        Announce: data.Announce,
        AnnounceRank: data.AnnounceRank
    }));
};


exports.MakeSchadularAnnounce = function (con, data) {

    exports.SchadualedAnnounce.push({
        Every: data.Every,
        Announce: data.Announce,
        AnnounceRank: data.AnnounceRank,
        Timer: setInterval(function () {
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.NewServerAnnounce",
                Announce: data.Announce,
                AnnounceRank: data.AnnounceRank
            }));
        }, data.Every * 60 * 1000),
        TimeStamp: Date.now()
    });
    exports.getSchaduler(con, data);
};



exports.getSchaduler = function (con, data) {

    var List = [];
    
    for (var iii in exports.SchadualedAnnounce)
        List.push({
            Every: exports.SchadualedAnnounce[iii].Every,
            Announce: exports.SchadualedAnnounce[iii].Announce,
            AnnounceRank: exports.SchadualedAnnounce[iii].AnnounceRank,
            TimeStamp: exports.SchadualedAnnounce[iii].TimeStamp
        });
    con.sendUTF(JSON.stringify({
        classPath: "ServerAnnounce.showServerAnnounceScaduler",
        Schadule: List
    }));
};


exports.removeFromSchaduler = function (con, data){
    
    clearInterval(exports.SchadualedAnnounce[data.Index].Timer);
    exports.SchadualedAnnounce.splice(data.Index, 1);
    exports.getSchaduler(con, data);
    
};
