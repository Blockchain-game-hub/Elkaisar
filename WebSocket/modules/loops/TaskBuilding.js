

Elkaisar.Helper.AfterBuildingUpGraded = function (Task){

        var GaindePres = 0;
        if(Task["state"] === "up"){
            GaindePres = Elkaisar.Config.BUILDING_PRESTIGE[Task["type"]][Task["lvl_to"] -1];
            Elkaisar.DB.Update("prestige = prestige + ?", "player", "id_player = ?", [GaindePres, Task.id_player]);
            Elkaisar.DB.Update("exp = exp + ?", "hero", "id_hero = (SELECT console FROM city WHERE id_city = ?)", [GaindePres*2, Task["id_city"]]);
        }
        
        
        
       
        if(Task["type"] === Elkaisar.Config.CITY_BUILDING_COTTAGE)
            Elkaisar.Lib.LCity.refreshPopCap(Task["id_city"]);
        
        else if(Task["type"] === Elkaisar.Config.CITY_BUILDING_STORE)
            Elkaisar.Lib.LSaveState.storeRatio(Task["id_city"]);
            
        else if(Task["type"] === Elkaisar.Config.CITY_BUILDING_THEATER)
            Elkaisar.DB.Update("lvl = ?", "city_theater", "id_city = ?", [Task["lvl_to"], Task["id_city"]]);
        
        else if(Task["type"] === Elkaisar.Config.CITY_BUILDING_PALACE){
            Elkaisar.Lib.LSaveState.saveCityState(Task["id_city"]);
            Elkaisar.DB.Update("coin_cap = ?", "city", "id_city = ?", [Elkaisar.Config.PalaceCoinCap[Task["lvl_to"] - 1], Task["id_city"]]);
            
        }else if(Task["type"] === Elkaisar.Config.CITY_BUILDING_WALL){
            Elkaisar.DB.Update("wall_cap = ?", "city", "id_city = ?",[10000*Task["lvl_to"], Task["id_city"]]);
            
        }
        
        return  GaindePres;
        
}

setInterval(function () {
    
    
    
    var Now = Date.now()/1000;
    
    Elkaisar.DB.SelectFrom("*", "city_worker", "time_end <= ?", [Now], function (Tasks){
        
        Elkaisar.DB.Delete("city_worker", "time_end <= ?", [Now]);
        
        Tasks.forEach(function (Task, Index){
            if(Task.lvl_to > 30)
                return;
            
            Elkaisar.DB.Update(Task.place + " = ?", "city_building_lvl", "id_city = ? AND id_player = ?",[Task["lvl_to"], Task["id_city"], Task["id_player"]], function (){
                if(Task["lvl_to"] === 0)
                    Elkaisar.DB.Update(Task.place +" = 0", "city_building", "id_city = ? AND id_player = ?", [Task["id_city"], Task["id_player"]]);
                
                Task.prestige = Elkaisar.Helper.AfterBuildingUpGraded(Task);
                var Player = Elkaisar.Base.getPlayer(Task.id_player);
                    
                    if(!Player)
                        return;
                    Player.connection.sendUTF(JSON.stringify({
                        "classPath": "TimedTask.Building",
                        Task       : Task
                    }));
                
            });
            
        });
    });
    
    
    /*Elkaisar.Base.Request.postReq(
            {
                "server": Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/ws/api/ATaskBuilding/Finish`,
            function (data) {
                
                var Tasks = Elkaisar.Base.isJson(data);
                if(!Tasks)
                    return console.log("endBattel: ",data);
                
                var ii;
                var Player;
                
               
                for(ii in Tasks){
                   
                    Player = Elkaisar.Base.getPlayer(Tasks[ii].id_player);
                    
                    if(!Player)
                        continue;
                    Player.connection.sendUTF(JSON.stringify({
                        "classPath": "TimedTask.Building",
                        Task       : Tasks[ii]
                    }));

                }

            }

    );*/

}, 1000);