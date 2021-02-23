
setInterval(function () {
    
    
    Elkaisar.DB.SelectFrom("id_hero, id_player, power, power_max", "hero", "power < power_max", [], function (Heros){
        
        var Players = {};
        
        Heros.forEach(function(Hero, Index){
            
            var powerToAdd = Math.min(1 + Hero["power"], Hero["power_max"]);
            var idPlayer = Hero.id_player;
            Elkaisar.DB.Update("power = ?", "hero", "id_hero = ?", [powerToAdd, Hero["id_hero"]]);
            
            if(!Players[idPlayer])
                Players[idPlayer] = [];
            Players[idPlayer].push({
                "idHero" : Hero["id_hero"],
                "power"  : powerToAdd
            });
        });
        for(var idPlayer in Players){
            var player = Elkaisar.Base.getPlayer(idPlayer);
            if(player)
                player.connection.sendUTF(JSON.stringify({
                    "classPath" : "Hero.Power.Added",
                    "Heros"     : Players[idPlayer]
                }));
        }
    });

    /*Elkaisar.Base.Request.postReq(
            {
                server: Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/ws/api/AHero/addPower`,
            function (data) {
                
                var Players = Elkaisar.Base.isJson(data);
                if(!Players)
                    return console.log("Add power Error", data);
                
                var spy;
                var ii;
                var player;
               
                
                for(ii in Players)
                {
                    player = Elkaisar.Base.getPlayer(ii);
                    if(player)
                        player.connection.sendUTF(JSON.stringify({
                            "classPath" : "Hero.Power.Added",
                            "Heros"     : Players[ii]
                        }));
                    
                }
                

            }
    );*/


}, 6*60*1000);
