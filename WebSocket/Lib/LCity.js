class LCity {

    static refreshPopCap(idCity) {

        Elkaisar.DB.SelectFrom("*", "city_building", "id_city = ?", [idCity], function (CityBuilding) {
            Elkaisar.DB.SelectFrom("*", "city_building_lvl", "id_city = ?", [idCity], function (CityBuildingLvl) {
                var TotalCap   = 300;
                var TampleLvl  = 0;
                var TheaterLvl = 0;
                
                for (var iii in CityBuilding[0]) {
                    if (iii === "id_player")
                        continue;
                    if (iii === "id_city")
                        continue;
                    
                    if(CityBuilding[0][iii] === Elkaisar.Config.CITY_BUILDING_WORSHIP)
                        TampleLvl = CityBuildingLvl[0][iii];
                    if(CityBuilding[0][iii] === Elkaisar.Config.CITY_BUILDING_THEATER)
                        TheaterLvl = CityBuildingLvl[0][iii];
                    
                    if (CityBuilding[0][iii] != Elkaisar.Config.CITY_BUILDING_COTTAGE)
                        continue;
                    
                    TotalCap += Elkaisar.Config.CottagePopCap[Math.max(CityBuildingLvl[0][iii] - 1, 1)];
                }
                Elkaisar.DB.Update("lvl = ?", "city_theater", "id_city = ?", [TheaterLvl, idCity]);
                Elkaisar.DB.SelectFrom("pop, pop_cap, taxs, helper", "city", "id_city = ?", [idCity], function (City){
                    
                    if(City[0].helper === Elkaisar.Config.CITY_HELPER_POP)
                        Elkaisar.DB.Update("pop_cap = ?, pop_max = ?", "city" ,"id_city = ?", [TotalCap + (TotalCap*0.02*TampleLvl), Math.ceil((TotalCap + TotalCap*0.02*TampleLvl) - ((City[0]["taxs"]*TotalCap)/100 )),idCity]);
                    else 
                        Elkaisar.DB.Update("pop_cap = ?, pop_max = ?", "city" ,"id_city = ?", [TotalCap, Math.ceil(TotalCap - ((City[0]["taxs"]*TotalCap)/100 )),idCity]);
                    
                });
                
            });
        });

    }
    
    
    static refreshStoreCap(idCity, callBack){
        
        Elkaisar.DB.SelectFrom("*", "city_building", "id_city = ?", [idCity], function (CityBuilding){
            Elkaisar.DB.SelectFrom("*", "city_building_lvl", "id_city = ?", [idCity], function (CityBuildingLvl){
                var TotalCap = 0;
                for(var iii in CityBuilding[0]){
                    
                    if(CityBuilding[0][iii] !== Elkaisar.Config.CITY_BUILDING_STORE)
                        continue;
                    if(iii === "id_player")
                        continue;
                    if(iii === "id_city")
                        continue;
                    TotalCap = Elkaisar.Config.StorageCap[CityBuildingLvl[0][iii] - 1];
                }
                
                if(callBack)
                    callBack(TotalCap);
                Elkaisar.DB.Update("total_cap = ?", "city_storage" ,"id_city = ?", [TotalCap, idCity]);
            });
        });
    } 

}


module.exports = LCity;


