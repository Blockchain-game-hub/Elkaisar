


exports.onCity  = function (city, msgObj){
    
    if(!city.city_owner)
        return ;
    
    var player = Elkaisar.Base.getPlayer(city.city_owner.id_player);
    if(!player)
        return ;
    
    player.connection.sendUTF(JSON.stringify({
        "classPath"  : "Battel.battel",
        "type"       : "battel",
        "task"       : "YOUR_CITY_FIRE",
        "ok"         : true,
        "x_from"     : msgObj.x_from,
        "y_from"     : msgObj.y_from,
        "x_to"       : msgObj.x_to,
        "y_to"       : msgObj.y_to,
        "city_name"  : city.city_owner.name
    }));
};


exports.OnGarrison  = function (garrison, msgObj){
    
    if(!Array.isArray(garrison.garrison))
        return ;
    
    var iii ;
    var player;
    var msg = JSON.stringify({
        "classPath" : "Battel.garrisonFire",
        "ok"        : true,
        "x_from"    : msgObj.x_from,
        "y_from"    : msgObj.y_from,
        "x_to"      : msgObj.x_to,
        "y_to"      : msgObj.y_to
    });
    for( iii in garrison.garrison){
        var player = Elkaisar.Base.getPlayer(garrison.garrison[iii].id_player);
        if(!player)
            continue ;
        
        if(garrison.city_owner && parseInt(garrison.city_owner.id_player) === parseInt(garrison.garrison[iii].id_player))
            continue;
        
        player.connection.sendUTF(msg);
        
        
    }
    
    
    
    
};



exports.OnEffected = function (battel, msgObj){
  
    if(!battel.effected)
        return ;
    if(!battel.effected.attacker)
        return ;
    if(!Array.isArray(battel.effected.players))
        return ;
    
    var playersId = battel.effected.players;
    var ii;
    var player;
    var msg = JSON.stringify({
        "classPath"        : "Battel.startAnnounce",
        "type"             : "BATTEL_START_ANNOUNCE",
        "ok"               : true,
        "attackerName"     : msgObj.p_name,
        "attackerIdGuild"  : msgObj.id_guild,
        "attackerGuildName": msgObj.guild_name,
        "x_to"             : msgObj.x_to,
        "y_to"             : msgObj.y_to
    });
    
    
    
    for(var ii in playersId){
       
        player = Elkaisar.Base.getPlayer(playersId[ii]);
        if(!player)
            continue;
        
        player.connection.sendUTF(msg);
        
    }
    
};


exports.started = function (battel){
  
    
    var $res  = {};
    if(battel.state === "ok"){
        $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "started",
            "ok"            : battel.id_battel,
            "id_battel"     : battel.id_battel,
            "time_end"      : battel.time_end,
            "attack_time"   : battel.attack_time,
            "power_need"    : battel.power_need,
            "matrial"       : battel.matrial
       };

    }else if(battel.state === "not_in_city"){
        $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "not_in_city"
        };
    }else if(battel.state === "no_more_lvls"){
        $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "no_more_lvls"
        };
    }else if(battel.state === "locked_unit"){
        $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "locked_unit"
        };
    }else if(battel.state === "hero_cant_used"){
        $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "hero_cant_used"
        };
    }else if(battel.state === "no_enough_mat"){
         $res = {
            "classPath"     : "Battel.started",
            "type"          : "battel",
            "task"          : "no_enough_mat"
        };
    }

    return $res;
    
};