
Elkaisar.World.WorldUnits = [];
Elkaisar.World.WorldUnitData = {};
Elkaisar.World.WorldUnitPrize = {};
Elkaisar.World.WorldUnitLosePrize = {};
Elkaisar.World.OnFireUnits = {};

Elkaisar.World.refreshWorldUnit = function (callBack) {

    Elkaisar.DB.SelectFrom("x, y, l, t, ut, lo", "world", "1", [], function (Res) {
        console.log("====");
        console.log(Date.now());
        for(var iii in Res){
            var Unit = Res[iii];
            if(!Elkaisar.World.WorldUnits[Unit.x*500 + Unit.y])
                Elkaisar.World.WorldUnits[Unit.x*500 + Unit.y] = Unit;
            else{
                Elkaisar.World.WorldUnits[Unit.x*500 + Unit.y].ut = Unit.ut;
                Elkaisar.World.WorldUnits[Unit.x*500 + Unit.y].l  = Unit.l;
            }
        }
        console.log(Date.now());
        if (callBack)
            callBack();
    });
};



Elkaisar.World.getEquipPower = function (callBack) {
    Elkaisar.DB.SelectFrom("*", "equip_power", "1", [], function (EquipList) {
        for (var iii in EquipList) {
            Elkaisar.Equip.EquipPower[`${EquipList[iii].equip}.${EquipList[iii].part}.${EquipList[iii].lvl}`] = EquipList[iii];
        }
    });
};


Elkaisar.World.getUnit = function (xCoord, yCoord) {
    return Elkaisar.World.WorldUnits[xCoord * 500 + yCoord];
};

Elkaisar.World.getUnitPrize = function (Battel) {
    var xCoord = Battel.Battel.x_coord;
    var yCoord = Battel.Battel.y_coord;
    var Unit = Elkaisar.World.WorldUnits[xCoord * 500 + yCoord];
    var PrizeList = [];
    if(Battel.Fight.sideWin === Elkaisar.Config.BATTEL_SIDE_ATT)
        PrizeList = Elkaisar.World.WorldUnitPrize[`${Unit.ut}.${Battel.WinLvl}`];
    else
        PrizeList = Elkaisar.World.WorldUnitPrize[`${Unit.ut}.${Battel.WinLvl}.Lose`];
    
    if (!PrizeList)
        return [];
    for (var i = PrizeList.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = PrizeList[i];
        PrizeList[i] = PrizeList[j];
        PrizeList[j] = temp;
    }

    return PrizeList;
};

Elkaisar.World.getUnitData = function (callBack)
{
    Elkaisar.Base.Request.getReq(
            {
                server: Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/js/json/worldUnitData.json`,
            function (data) {
                Elkaisar.World.WorldUnitData = JSON.parse(data);
                if (callBack)
                    callBack();
            }
    );
};

Elkaisar.World.refreshWorldUnitHeros = function (callBack) {
    Elkaisar.DB.SelectFrom("*", "world_unit_hero", "1", [], function (Res) {
        console.log(`HerosLen is ${Res.length}`);
        for (var ii in Res) {

            if (!Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitHero)
                Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitHero = {};
            if (!Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitHero[Res[ii].lvl])
                Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitHero[Res[ii].lvl] = [];
            Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitHero[Res[ii].lvl].push({
                Hero: {
                    point_a: 0,
                    point_a_plus: 0,
                    point_b: 0,
                    point_b_plus: 0,
                    point_c: 0,
                    point_c_plus: 0,
                    id_player: 0,
                    medal_den: 0,
                    medal_leo: 0,
                    x: 0, y: 0,
                    id_hero: Res[ii].id_hero * -1,
                    id_city: 0
                },
                Army: {
                    id_hero: 0,
                    id_player: 0,
                    f_1_type: Res[ii].f_1_type,
                    f_2_type: Res[ii].f_2_type,
                    f_3_type: Res[ii].f_3_type,
                    f_1_num: Res[ii].f_1_num,
                    f_2_num: Res[ii].f_2_num,
                    f_3_num: Res[ii].f_3_num,
                    b_1_type: Res[ii].b_1_type,
                    b_2_type: Res[ii].b_2_type,
                    b_3_type: Res[ii].b_3_type,
                    b_1_num: Res[ii].b_1_num,
                    b_2_num: Res[ii].b_2_num,
                    b_3_num: Res[ii].b_3_num
                }
            });
        }
        if (callBack)
            callBack();
    });
};


Elkaisar.World.refreshWorldUnitEquip = function (callBack) {
    Elkaisar.DB.SelectFrom("*, world_unit_equip.equip AS type", "world_unit_equip", "1", [], function (Res) {
        for (var ii in Res) {
            if (!Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitEquip)
                Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitEquip = {};
            if (!Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitEquip[Res[ii].l])
                Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitEquip[Res[ii].l] = [];

            Elkaisar.World.WorldUnits[Res[ii].x * 500 + Res[ii].y].UnitEquip[Res[ii].l].push(Res[ii]);
        }
        if (callBack)
            callBack();
    });
};

Elkaisar.World.refreshWorldUnitPrize = function (callBack){
    
    Elkaisar.DB.SelectFrom("*", "world_unit_prize", "1", [], function (Res) {
        Elkaisar.DB.SelectFrom("*", "world_unit_prize_lose", "1", [], function (LP){
            Elkaisar.World.WorldUnitPrize = {};
            for (var iii in Res){
            
                var UnitPrize = Res[iii];
                if (!Elkaisar.World.WorldUnitPrize[`${UnitPrize.unitType}.${UnitPrize.lvl}`])
                    Elkaisar.World.WorldUnitPrize[`${UnitPrize.unitType}.${UnitPrize.lvl}`] = [];
                Elkaisar.World.WorldUnitPrize[`${UnitPrize.unitType}.${UnitPrize.lvl}`].push(Res[iii]);

            }
            
            for (var ii in LP){
            
                var UnitLPrize = LP[ii];
                if (!Elkaisar.World.WorldUnitPrize[`${UnitLPrize.unitType}.${UnitLPrize.lvl}.Lose`])
                    Elkaisar.World.WorldUnitPrize[`${UnitLPrize.unitType}.${UnitLPrize.lvl}.Lose`] = [];
                Elkaisar.World.WorldUnitPrize[`${UnitLPrize.unitType}.${UnitLPrize.lvl}.Lose`].push(LP[ii]);

            }
            if (callBack)
            callBack();
        });
    });
    
};


Elkaisar.World.refreshWorldUnit(function () {
    Elkaisar.World.refreshWorldUnitHeros(function () {
        Elkaisar.World.refreshWorldUnitEquip(function () {
            Elkaisar.World.refreshWorldUnitEquip(function () {
                Elkaisar.World.getUnitData(function () {
                    Elkaisar.World.refreshWorldUnitPrize(function () {
                        Elkaisar.OnEvent.emit("OnServerReady");
                        Elkaisar.DB.Update("s = 0", "world", "1", []);
                        Elkaisar.World.getEquipPower();
                    });
                });
            });
        });
    });
});





class LWorld {

    static unitHeros(Unit, callBack) {
        var herosToSend = [];
        var WorldUnit = Elkaisar.World.getUnit(Unit.x, Unit.y);
        if (!WorldUnit)
            return herosToSend;
        if (!WorldUnit.UnitHero)
            return herosToSend;
        if (!WorldUnit.UnitHero[Unit.l])
            return herosToSend;

        var ii;
        var HeroUnitList = WorldUnit.UnitHero[Unit.l];


        var UnitHero;
        /*for (ii in HeroUnitList){
         UnitHero = HeroUnitList[ii];
         herosToSend.push({
         "name": UnitHero["name"],
         "ord": UnitHero["ord"],
         "pre": {
         "f_1": UnitHero["f_1_num"],
         "f_2": UnitHero["f_2_num"],
         "f_3": UnitHero["f_3_num"],
         "b_1": UnitHero["b_1_num"],
         "b_2": UnitHero["b_2_num"],
         "b_3": UnitHero["b_3_num"]
         },
         "type": {
         "f_1": UnitHero["f_1_type"],
         "f_2": UnitHero["f_2_type"],
         "f_3": UnitHero["f_3_type"],
         "b_1": UnitHero["b_1_type"],
         "b_2": UnitHero["b_2_type"],
         "b_3": UnitHero["b_3_type"]
         }
         });
         }*/
        if (callBack)
            callBack(HeroUnitList);

        return HeroUnitList;
    }

    static unitEquip(Unit, callBack)
    {
        var WorldUnit = Elkaisar.World.getUnit(Unit.x, Unit.y);
        if (!WorldUnit.UnitEquip)
            return [];
        if (!WorldUnit.UnitEquip[WorldUnit.l])
            return [];
        return WorldUnit.UnitEquip[WorldUnit.l];
    }

    static  unitGarrisonHero(Unit, callBack) {
        Elkaisar.DB.SelectFrom(
                `world_unit_garrison.* , hero.id_city, 
                hero.point_b, hero.point_b_plus, hero.point_c, hero.point_c_plus  , 
                hero_medal.medal_den , hero_medal.medal_leo, city.x ,city.y `,
                `world_unit_garrison JOIN hero ON hero.id_hero = world_unit_garrison.id_hero
                JOIN hero_medal ON hero_medal.id_hero = world_unit_garrison.id_hero
                JOIN city ON city.id_city = hero.id_city`,
                `world_unit_garrison.x_coord = ? AND world_unit_garrison.y_coord = ? ORDER BY ord ASC`, [Unit.x, Unit.y],
                function (Res) {
                    callBack(Res);
                });

    }

    static battelHeros(Battel, callBack)
    {
        Elkaisar.DB.SelectFrom(
                `battel_member.id_hero , battel_member.id_player ,
                hero.point_b, hero.point_b_plus, hero.point_c, hero.point_c_plus  , 
                hero_medal.medal_den , hero_medal.medal_leo , battel_member.ord , 
                hero.id_city  , battel_member.side , city.x, city.y `,
                `battel_member JOIN  hero ON hero.id_hero = battel_member.id_hero JOIN hero_medal  ON hero_medal.id_hero = battel_member.id_hero JOIN city ON hero.id_city = city.id_city`,
                `battel_member.id_battel = ? ORDER BY battel_member.ord ASC`, [Battel.id_battel],
                function (Res) {
                    callBack(Res);
                });
    }

}

module.exports = LWorld;