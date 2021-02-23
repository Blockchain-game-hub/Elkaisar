
exports.worldChat = function (connection, dataObj){

    var msg = JSON.stringify({
                            "classPath"         : "Chat.banPlayer",
                            "p_name_panned"     : dataObj.PannedName,
                            "p_name_panner"     : dataObj.PannerName,
                            "duration"          : dataObj.duration,
                            "idPlayerToPan"     : dataObj.idPlayerToPan
               });
               
    Elkaisar.Base.broadcast(msg);
    
    
    var Player = Elkaisar.Arr.Players[dataObj.idPlayerToPan];
    
    if(Player)
    {
        Elkaisar.DB.SelectFrom("*", "player", "id_player = ?", [dataObj.idPlayerToPan], function (Res){
            if(Res[0])
                Player.playerData =  Res[0];
            else 
                Player.connection.close();
        });
    }
    
};

