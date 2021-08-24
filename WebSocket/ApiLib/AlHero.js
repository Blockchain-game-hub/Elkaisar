class ALHero{

    static async addNew(idPlayer, idCity, lvl, avatar, name, ultraPoint = 0) {
        
        const points = ALHero.pointsForLvl(lvl);
        const lastOrder = await Elkaisar.DB.ASelectFrom("ord", "hero", "id_city = ? ORDER BY ord DESC LIMIT 1", [idCity]);
        var power = 50 + lvl;
        if (lvl >= 100) 
            power = 150;
        
        const ord = lastOrder.length > 0 ? lastOrder[0]["ord"] + 1 : 0;
        
        const idHero = await ALHero.getNewHeroId(idPlayer);
        if (!idHero) return false;
        
        await Elkaisar.DB.AInsert(`id_hero = ?, name = '${name}' , lvl = ? ,  avatar = ? , power = ? , id_city = ? , id_player = ? ,
                point_a = ? , point_b = ? , point_c = ?, cap = ? , ultra_p = ? , ord = ? , p_b_a = ?, p_b_b = ?,
                p_b_c = ?, b_lvl = ?, power_max = ?`, "hero",
                [idHero, lvl, avatar, power, idCity, idPlayer, points["pointA"],  
                points["pointB"], points["pointC"], points["cap"], ultraPoint, ord,
                points["pba"], points["pbb"], points["pbc"], lvl, 50 + lvl
        ]);
        Elkaisar.DB.Insert("id_hero = ?, id_player = ?", "hero_army", [idHero, idPlayer]);
        Elkaisar.DB.Insert("id_hero = ?", "hero_medal", [idHero]);
        Elkaisar.DB.Update("hero_num  = hero_num  + 1", "server_data", "id = 1");
        return true;
    }

    static  pointsForLvl(lvl) {
        var points = [];
        points["pba"] = Elkaisar.Base.rand(0, 25);
        points["pbb"] = Elkaisar.Base.rand(0, 25);
        const pointsSum     = Elkaisar.Base.rand(50, 60);
        points["pbc"]    = pointsSum - (points["pba"] + points["pbb"]);
        points["pointA"] = points["pba"] + (lvl * 1);
        points["pointB"] = points["pbb"] + (lvl * 1);
        points["pointC"] = points["pbc"] + (lvl * 1);
        points["cap"] = 30000 + points["pointA"] * 500;
        return points;
    }

    static async getNewHeroId(idPlayer)
    {
        
        const heroCount = (await Elkaisar.DB.ASelectFrom("COUNT(*) AS c", "hero", "id_player = ?", [idPlayer]))[0]["c"];
        
        const idHero = (idPlayer - 1 ) *1000 + heroCount + 1;
        
        if(heroCount == 0)
            return idHero;
        
        const LastHeroId = (await Elkaisar.DB.ASelectFrom("id_hero", "hero", "id_player = ? ORDER BY id_hero DESC LIMIT 1", [idPlayer]))[0]["id_hero"];

        if(LastHeroId >= idHero)
        {
            const idHeroGap = (await Elkaisar.DB.ASelectFrom(`id_hero + 1 AS idHero`,`hero mo`,
                                    `NOT EXISTS ( SELECT NULL FROM hero mi WHERE mi.id_hero = mo.id_hero + 1 ) 
                                        AND id_player = ?
                                        ORDER BY id_hero  LIMIT 1`, [idPlayer]))[0]["idHero"];
           
            if(idHeroGap > (idPlayer - 1 ) *1000  && idHeroGap < (idPlayer) *1000 )
                return idHeroGap;
        }
        
        
        return Math.max(idHero, LastHeroId + 1);
    
    }
   

}

module.exports = ALHero;