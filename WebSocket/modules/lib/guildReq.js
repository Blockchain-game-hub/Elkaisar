
exports.joinRequestAccepted = function (con , msgObj){
  
    var player = Elkaisar.Base.getPlayer(msgObj.id_player_from);
    if(!player)
        return ;
    
    var msg = JSON.stringify({
        "classPath":"Guild.joinReqAccepted",
        "guild_name": msgObj.guild_name,
        "accepter_name": msgObj.accepter_name
    });
    
    player.connection.sendUTF(msg);
    
};


