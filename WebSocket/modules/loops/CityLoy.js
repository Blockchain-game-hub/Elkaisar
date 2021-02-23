
setInterval(function () {
    
    
    Elkaisar.DB.SelectFrom("loy, loy_max, id_city, id_player, pop_cap, taxs", "city", "loy_max != loy", [], function (Cities){
        
        
        if(!Cities)
            return ;
        
        Cities.forEach(function (City, Index){
            
            var Loyal = City.loy;
            
            if(City.loy < City.loy_max)
                Loyal = Math.min(City.loy + 3 , 100, City.loy_max);
            else if(City.loy > City.loy_max)
                Loyal = Math.min(Math.max(City.loy - 3 , City.loy_max), 100);
            
            Elkaisar.DB.Update("loy = ?, pop_max = ?", "city", "id_city = ?", [Loyal, City.pop_cap - ((100 - Loyal)/100)*City.pop_cap, City.id_city]);
            
            var player = Elkaisar.Base.getPlayer(City.id_player);
            
            if (player)
                player.connection.sendUTF(JSON.stringify({
                    "classPath": "City.Pop.UpdateLoy",
                    "idCity": City.id_city
                }));
        });
    });
    
}, 3*6*60*1000);


