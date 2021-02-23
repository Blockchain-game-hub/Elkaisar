
exports.notifyReciever = function (con, msgObj){
   
    var player = Elkaisar.Base.getPlayer(msgObj.id_player_to);
    if(!player)
        return ;
    
    
    player.connection.sendUTF(JSON.stringify({
        "classPath": "Mail.sentTo"
    }));
    
};


