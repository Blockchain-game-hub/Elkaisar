
setInterval(function () {


    Elkaisar.DB.SelectFrom("pop, pop_cap, pop_max, id_city, id_player", "city", "pop != pop_max", [], function (Cities) {
        if (!Cities)
            return;

        Cities.forEach(function (City, Index) {
            
            var popToAdd = 0;
            
            if (City["pop"] > City["pop_max"])
                popToAdd = Math.max(City["pop_max"], City["pop"] - Math.max(0.05 * City["pop"], 1));
            else 
                popToAdd = Math.min(City["pop_max"], 0.05 * City["pop_cap"] + City["pop"]);


            Elkaisar.DB.Update("pop = ?", "city", "id_city = ?", [popToAdd, City["id_city"]], function () {
                Elkaisar.Lib.LSaveState.afterCityColonizer(City.id_player, City.id_city);
            });
            var player = Elkaisar.Base.getPlayer(City.id_player);
            if (player)
                player.connection.sendUTF(JSON.stringify({
                    "classPath": "City.Pop.Update",
                    "idCity": City.id_city
                }));
        });
    });
    
}, 6 * 60 * 1000);

