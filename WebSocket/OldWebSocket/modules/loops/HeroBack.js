

Elkaisar.Helper.HeroSupport = function (Hero) {
    Elkaisar.DB.Update("in_city = ?", "hero", "id_hero = ?", [Elkaisar.Config.HERO_IN_GARISON, Hero.id_hero]);
    Elkaisar.DB.SelectFrom("ord", "world_unit_garrison", "x_coord = ? AND y_coord = ? ORDER BY ord DESC LIMIT 1",
            [Hero["y_to"], Hero["x_to"]], function (LastHero) {
        var Ord = 0;
        if (LastHero && LastHero.length > 0)
            Ord = LastHero[0].ord + 1;
        Elkaisar.DB.Insert("x_coord = ?, y_coord = ?, id_hero = ?, id_player = ?", "world_unit_garrison", [Hero.x_to, Hero.y_to, Hero.id_hero, Hero.id_player]);
        var Player = Elkaisar.Base.getPlayer(Hero.id_player);
        if (Player)
            Player.connection.sendUTF(JSON.stringify({
                classPath: "Hero.Back",
                "idPlayer": Hero["id_player"],
                "inCity": Elkaisar.Config.HERO_IN_GARISON,
                "idHero": Hero["id_hero"],
                "xTo": Hero["x_to"],
                "yTo": Hero["y_to"],
                "xFrom": Hero["x_from"],
                "yFrom": Hero["y_from"],
                "Task": Hero["task"]
            }));
    });
};


Elkaisar.Helper.HeroTrans = function (Hero) {
    Elkaisar.DB.SelectFrom("id_city, id_player", "city", " x = ? AND y = ? AND id_player = ?", [Hero.x_to, Hero.y_to, Hero.id_player], function (CityTo) {

        if (!CityTo || !CityTo.length) {
            Elkaisar.DB.Update("in_city = ?", "hero", "id_hero = ?", [Elkaisar.Config.HERO_IN_CITY, Hero.id_hero]);
            
            var Player = Elkaisar.Base.getPlayer(Hero.id_player);
            if (Player)
                Player.connection.sendUTF(JSON.stringify({
                    classPath: "Hero.Back",
                    "idPlayer": Hero["id_player"],
                    "inCity": Elkaisar.Config.HERO_IN_CITY,
                    "idHero": Hero["id_hero"],
                    "xTo": Hero["x_to"],
                    "yTo": Hero["y_to"],
                    "xFrom": Hero["x_from"],
                    "yFrom": Hero["y_from"],
                    "Task": Hero["task"]
                }));
            return;
        }


        Elkaisar.DB.SelectFrom("ord", "hero", "id_city = ? ORDER BY ord DESC", [CityTo[0].id_city], function (LastOreder) {

            var LastOrd = 0;
            
            if (LastOreder && LastOreder[0] && LastOreder[0].ord)
                LastOrd = LastOreder[0].ord + 1;
            
            Elkaisar.DB.Update(
                    "id_city = ?, in_city = ?, ord = ?", "hero", "id_hero = ?",
                    [CityTo[0].id_city, Elkaisar.Config.HERO_IN_CITY, LastOrd, Hero["id_hero"]], function () {
                        
                Elkaisar.Lib.LHero.reOrderHero(Hero.id_city);
                Elkaisar.Lib.LHero.reOrderHero(CityTo[0].id_city);

                Elkaisar.Lib.LSaveState.coinOutState(CityTo[0].id_player, CityTo[0].id_city);
                Elkaisar.Lib.LSaveState.foodOutState(CityTo[0].id_player, CityTo[0].id_city);
                       
                Elkaisar.Lib.LSaveState.coinOutState(Hero.id_player, Hero.id_city);
                Elkaisar.Lib.LSaveState.foodOutState(Hero.id_player, Hero.id_city);
              


            });

            var Player = Elkaisar.Base.getPlayer(Hero.id_player);
            if (Player)
                Player.connection.sendUTF(JSON.stringify({
                    classPath: "Hero.Back",
                    "idPlayer": Hero["id_player"],
                    "inCity": Elkaisar.Config.HERO_IN_CITY,
                    "idHero": Hero["id_hero"],
                    "xTo": Hero["x_to"],
                    "yTo": Hero["y_to"],
                    "xFrom": Hero["x_from"],
                    "yFrom": Hero["y_from"],
                    "Task": Hero["task"]
                }));
        });
    });
};


Elkaisar.Helper.HeroBackHome = function (Hero) {

    Elkaisar.DB.Update("in_city = ?", "hero", "id_hero = ?", [Elkaisar.Config.HERO_IN_CITY, Hero["id_hero"]]);

    var Player = Elkaisar.Base.getPlayer(Hero.id_player);
    if (Player)
        Player.connection.sendUTF(JSON.stringify({
            classPath: "Hero.Back",
            "idPlayer": Hero["id_player"],
            "inCity": Elkaisar.Config.HERO_IN_CITY,
            "idHero": Hero["id_hero"],
            "xTo": Hero["x_to"],
            "yTo": Hero["y_to"],
            "xFrom": Hero["x_from"],
            "yFrom": Hero["y_from"],
            "Task": Hero["task"]
        }));

}

setInterval(function () {


    Elkaisar.DB.SelectFrom("*", "hero_back", "time_back <= ?", [Date.now() / 1000], function (Heros) {
        Heros.forEach(function (Hero, Index) {

            if (Hero.task === Elkaisar.Config.BATTEL_TASK_SUPPORT) {
                Elkaisar.Helper.HeroSupport(Hero);
            } else if (Hero.task === Elkaisar.Config.BATTEL_TASK_HERO_TRANS) {
                Elkaisar.Helper.HeroTrans(Hero);
            } else {
                Elkaisar.Helper.HeroBackHome(Hero);
            }
            Elkaisar.DB.Delete("hero_back", "id_hero = ?", [Hero.id_hero]);
        });
    });

    /* Elkaisar.Base.Request.postReq(
     {
     server: Elkaisar.CONST.SERVER_ID
     },
     `${Elkaisar.CONST.BASE_URL}/ws/api/AHeroBack/Back`,
     function (data) {
     
     var Heros = Elkaisar.Base.isJson(data);
     if(!Heros)
     return console.log("heroBackLoop: ", data);
     
     var player;
     var ii;
     var hero;
     
     for(ii in Heros){
     hero = Heros[ii];
     Heros[ii].classPath = "Hero.Back";
     player = Elkaisar.Base.getPlayer(hero.idPlayer);
     
     if(player)
     player.connection.sendUTF(JSON.stringify(Heros[ii]));
     
     
     }
     
     }
     );*/

}, 1000);
