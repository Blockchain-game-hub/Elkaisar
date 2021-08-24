//01033488458
class LCityBuilding {

    static async  buildingWithHeighestLvl(idCity, BuildingType){
        
        var buildingLvl = {
                "Place": "",
                "Type":"",
                "Lvl": 0
            };
        let cityBuildingType = (await Elkaisar.DB.ASelectFrom("*", "city_building", "id_city = ? ", [idCity]))[0]; 
        let cityBuildingLvl  = (await Elkaisar.DB.ASelectFrom("*", "city_building_lvl", "id_city = ?", [idCity]))[0]; 
        
       
        delete(cityBuildingType["id_city"]);
        delete(cityBuildingType["id_playe"]);
        
        for(var onePlace in cityBuildingType){
            let oneType = cityBuildingType[onePlace];
            if(oneType != BuildingType)
            continue;
            if(buildingLvl["Lvl"] < cityBuildingLvl[onePlace])
               buildingLvl = {
                        "Place" : onePlace,
                        "Type": oneType,
                        "Lvl" : cityBuildingLvl[onePlace]
                    };
        }
        
        return buildingLvl;
        
    }

}


module.exports = LCityBuilding;


