
exports.removePlayer = function (con ,msgObj){
    
    var xCoord = msgObj.xCoord;
    var yCoord = msgObj.yCoord;
    var key = `${xCoord}-${yCoord}`;
    var watchArr = Elkaisar.Arr.BattelWatchList[key];
    
    if(!Array.isArray(watchArr))
        return ;
    var index = watchArr.indexOf(con.idPlayer);
    
    if(index < 0)
        return ;
    
    
    watchArr.splice(index, 1);
    
};

exports.addPlayer = function (con, msgObj){
  
    var idPlayer = con.idPlayer;
    var xCoord   = msgObj.xCoord;
    var yCoord   = msgObj.yCoord;
    
    var key = `${xCoord}-${yCoord}`;
    var watchArr = Elkaisar.Arr.BattelWatchList[key];
    
    if(!Array.isArray(watchArr))
        watchArr = [];
    
    var index = watchArr.indexOf(idPlayer);
    
    if(index >= 0)
        return ;
    
    
    watchArr.push(idPlayer);
    
};

exports.heroJoinNotif = function(con, msgObj){
    
    var msg = JSON.stringify({
        "classPath"    : "BattelWatchList.heroJoin",
        "id_battel"    : msgObj.id_battel,
        "attack_num"   : msgObj.attackNum,
        "defence_num"  : msgObj.defenceNum
    });
    
    var xCoord = msgObj.xCoord;
    var yCoord = msgObj.yCoord;
    var key = `${xCoord}-${yCoord}`;
    var watchArr = Elkaisar.Arr.BattelWatchList[key];
    
    if(!Array.isArray(watchArr))
        return ;
    
    var ii;
    var player;
    
    for(ii in watchArr){
        
        player = Elkaisar.Base.getPlayer(watchArr[ii]);
        if(!player)
            continue;
        
        player.connection.sendUTF(msg);
        
    }
};






exports.newBattelNotif = function (con , msgObj){
    
    var xCoord   = msgObj.xCoord;
    var yCoord   = msgObj.yCoord;
    var key = `${xCoord}-${yCoord}`;
    var watchArr = Elkaisar.Arr.BattelWatchList[key];
    
    if(!Array.isArray(watchArr))
        return ;
    
    var ii;
    var player;
    var msg = JSON.stringify({
        "classPath" : "BattelWatchList.newBattel",
        "Battel"    : msgObj.Battel
    });
    
    for(ii in watchArr){
        
        player = Elkaisar.Base.getPlayer(watchArr[ii]);
        if(!player)
            continue;
        
        player.connection.sendUTF(msg);
        
    }
    
};


exports.endBattelNotif = function (battel){
    
    var xCoord = battel.x_coord;
    var yCoord = battel.y_coord;
    var msg = JSON.stringify({
        "classPath" : "BattelWatchList.endBattel",
        "battel"    : battel
    });
    
    var key = `${xCoord}-${yCoord}`;
    var watchArr = Elkaisar.Arr.BattelWatchList[key];
    var ii;
    var player;
    
    if(!Array.isArray(watchArr))
        return ;
    
    for(ii in watchArr){
        
        player = Elkaisar.Base.getPlayer(watchArr[ii]);
        if(!player)
            continue;
        
        player.connection.sendUTF(msg);
        
    }
    
};