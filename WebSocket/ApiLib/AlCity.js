class ALCity{

    static async isResourceTaken(idPlayer, idCity, ReqRes){

        if(!ALCity.isResourceEnough(idPlayer, idCity, ReqRes))
            return false;
        var List = [];
        for(var res in ReqRes){
            List.push("`"+res+"` = `"+res+"` - " + ReqRes[res]);
        }
       
        Elkaisar.DB.Update( List.join(","), "city", "id_city = ? AND id_player = ?", [idCity,idPlayer]);
        return true;
    }

    static async isResourceEnough(idPlayer, idCity, ReqRes)
    {
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        const cityRes = await Elkaisar.DB.ASelectFrom(
            Object.keys(ReqRes).join(","),
            "city", "id_city = ? AND id_player = ?", [idCity,idPlayer] );
        if(!cityRes.length)
            return false;
        for(var res in ReqRes){
            if(cityRes[0][res] < ReqRes[res] || ReqRes[res] <0)
            return false;
        }
        
        return true;
        
    }
   

}

module.exports = ALCity;