
class LBattel
{
    static addUnitGarrison(Battel, callBack) {
        Elkaisar.DB.SelectFrom(
                `world_unit_garrison.id_player, world_unit_garrison.id_hero, world_unit_garrison.x_coord AS x,
                world_unit_garrison.y_coord AS y, hero.id_city, 
                hero.point_b, hero.point_b_plus, hero.point_c, hero.point_c_plus  , 
                hero_medal.medal_den , hero_medal.medal_leo, city.x ,city.y `,
                `world_unit_garrison JOIN hero ON hero.id_hero = world_unit_garrison.id_hero
                JOIN hero_medal ON hero_medal.id_hero = world_unit_garrison.id_hero
                JOIN city ON city.id_city = hero.id_city`,
                `world_unit_garrison.x_coord = ? AND world_unit_garrison.y_coord = ? ORDER BY world_unit_garrison.ord ASC`, [Battel.x_coord, Battel.y_coord],
                function (Res) {
                    Res.forEach(function (Hero, index) {
                        var HeroBattel = {
                            idHero: Hero.id_hero,
                            isGarrison: true,
                            side: Elkaisar.Config.BATTEL_SIDE_DEF,
                            Hero: Hero,
                            AttaCkTask: Battel.task
                        };
                        LBattel.addPlayerToBattel(Hero.id_player, Elkaisar.Battel.BattelList[Battel.id_battel], Elkaisar.Config.BATTEL_SIDE_DEF);
                        Elkaisar.Battel.BattelList[Battel.id_battel].Heros.push(HeroBattel);
                        Elkaisar.Lib.LHero.getHeroEquip(HeroBattel);
                        Elkaisar.Lib.LHero.getHeroArmy(HeroBattel);
                    });

                    if (callBack)
                        callBack();
                });
    }

    static removeHeroFromBattel(idHero, callBack) {

        var Players = [];
        var Heros = [];
        var TimeBack = 0;
        var Battel = {};

        Elkaisar.DB.SelectFrom("*", "battel_member", `id_hero = ${idHero}`, [], function (HeroBattel) {
            if (!HeroBattel[0])
                return;
            if (!Elkaisar.Battel.BattelList[HeroBattel[0]["id_battel"]])
                return;

            var Hero;
            var PlayerFound = false;
            var Battel = Elkaisar.Battel.BattelList[HeroBattel[0]["id_battel"]];

            TimeBack = Math.floor(Date.now() / 1000) - Battel.Battel.time_start;


            for (var Index in Battel.Heros) {
                Hero = Battel.Heros[Index];
                if (Hero.idHero == idHero) {
                    Heros.push({
                        idHero: idHero,
                        idPlayer: HeroBattel[0]["id_player"],
                        Hero: Hero
                    });
                    Battel.Heros.splice(Index, 1);
                } else if (Hero.Hero.id_player == HeroBattel[0]["id_player"])
                    PlayerFound = true;

            }




            if (Battel.Battel.id_hero == idHero) {
                for (var H of Battel.Heros) {
                    if (H.idHero != idHero)
                        Heros.push({
                            idHero: H.idHero,
                            idPlayer: H.Hero.id_player,
                            Hero: H
                        });
                }
                Elkaisar.Lib.LBattel.removeBattel(Battel.Battel.id_battel);
                delete(Elkaisar.Battel.BattelList[Battel.Battel.id_battel]);
            }


            Players = Object.keys(Battel.Players);
            if (!PlayerFound)
                delete(Battel.Players[HeroBattel[0]["id_player"]]);





            Elkaisar.DB.Delete("battel_member", `id_hero = ${idHero}`);

            for (var idH  of Heros) {
                Elkaisar.DB.Insert(
                        `id_hero = ${idH.idHero}, x_from  = ${Battel.Battel.x_coord}, y_from = ${Battel.Battel.y_coord},
                     task = ${Battel.Battel.task}, x_to = ${idH.Hero.Hero.x} , y_to = ${idH.Hero.Hero.y},
                     time_back = ${TimeBack} , id_player = ${idH.idPlayer}`, "hero_back", []);
            }

            if (callBack)
                callBack(Players);


        });
    }

    static addHeroToBattel(Hero, Battel, callBack) {

        if (Hero.side == Elkaisar.Config.BATTEL_SIDE_ATT)
            Elkaisar.Battel.BattelList[Battel.id_battel].Battel.attackNum++;
        else
            Elkaisar.Battel.BattelList[Battel.id_battel].Battel.defenceNum++;

        Elkaisar.DB.SelectFrom(
                `hero.point_a, hero.point_a_plus,hero.point_b, hero.point_b_plus,
                 hero.point_c, hero.point_c_plus , hero.id_player, 
                 hero_medal.medal_den , hero_medal.medal_leo, 
                 hero.id_city, city.x, city.y `,
                `hero  JOIN hero_medal  ON hero_medal.id_hero = hero.id_hero JOIN city ON hero.id_city = city.id_city`,
                `hero.id_hero = ?`, [Hero.idHero],
                function (Res) {
                    var CuHero = Res[0];

                    if (!CuHero)
                        return;
                    CuHero.id_hero = Hero.idHero;
                    Hero.Hero = CuHero;
                    Hero.AttackTask = Battel.task;
                    Elkaisar.Lib.LHero.getHeroEquip(Hero);
                    Elkaisar.Lib.LHero.getHeroArmy(Hero);
                    if (callBack)
                        callBack(Hero);
                });

    }

    static addPlayerToBattel(idPlayer, Battel, side, callBack) {

        if (Battel.Players[idPlayer])
            return;
        Battel.Players[idPlayer] = Elkaisar.Config.CPlayer.BattelPlayerEmpty();
        Battel.Players[idPlayer].idPlayer = idPlayer;
        Battel.Players[idPlayer].side = side;

        Elkaisar.Lib.LPlayer.getPlayerdata(Battel.Players[idPlayer]);
        Elkaisar.Lib.LPlayer.getPlayerStudy(Battel.Players[idPlayer]);
        Elkaisar.Lib.LPlayer.getPlayerState(Battel.Players[idPlayer]);
        Elkaisar.Lib.LPlayer.getPlayerGodGate(Battel.Players[idPlayer], Battel.Battel);

        if (callBack)
            callBack();

    }
    static BattelInfluencedPlayers(Battel, callBack)
    {
        var Unit = Elkaisar.World.getUnit(Battel.Battel.x_coord, Battel.Battel.y_coord);

        if (Elkaisar.Lib.LWorldUnit.isBarrary(Unit.ut))
        {
            Elkaisar.DB.SelectFrom("id_player", "city_bar", "x_coord = ? AND y_coord = ?", [Unit.x, Unit.y], function (PlayerBarr) {
                if (!PlayerBarr[0])
                    return;
                LBattel.addPlayerToBattel(PlayerBarr[0].id_player, Elkaisar.Battel.BattelList[Battel.Battel.id_battel], Elkaisar.Config.BATTEL_SIDE_DEF);

                if (callBack)
                    callBack();
            });

        } else if (Elkaisar.Lib.LWorldUnit.isCity(Unit.ut)) {
            Elkaisar.DB.SelectFrom('id_city, id_player', "city", "x = ? AND y = ?", [Unit.x, Unit.y], function (PlayerCity) {
                if (!PlayerCity[0])
                    return;
                LBattel.addPlayerToBattel(PlayerCity[0].id_player, Elkaisar.Battel.BattelList[Battel.Battel.id_battel], Elkaisar.Config.BATTEL_SIDE_DEF);
                if (callBack)
                    callBack();
            });
        }

    }

    static newBattelStarted(Battel, callBack) {

        Elkaisar.Battel.BattelList[Battel.id_battel] = {
            Battel: Battel,
            Heros: [],
            Players: {},
            HeroReadyList: []
        };
        var HeroBattel = {
            idHero: Battel.id_hero,
            isGarrison: false,
            side: Elkaisar.Config.BATTEL_SIDE_ATT
        };
        LBattel.addPlayerToBattel(Battel.id_player, Elkaisar.Battel.BattelList[Battel.id_battel], Elkaisar.Config.BATTEL_SIDE_ATT);
        LBattel.BattelInfluencedPlayers(Elkaisar.Battel.BattelList[Battel.id_battel]);
        LBattel.addUnitGarrison(Battel/*, function () {
         LBattel.addHeroToBattel(HeroBattel, Battel, function (Hero) {
         Elkaisar.Battel.BattelList[Battel.id_battel].Heros.push(HeroBattel);
         if (callBack)
         callBack();
         });
         }*/);
        Elkaisar.Lib.LWorldUnit.fireOnWorldUnit(Battel.x_coord, Battel.y_coord);
        Elkaisar.WsLib.BattelWatchList.newBattelNotif(null, {
            xCoord: Battel.x_coord,
            yCoord: Battel.y_coord,
            Battel: Battel
        });
    }

    static heroJoinedBattel(Hero, Battel)
    {
        var HeroBattel = {
            idHero: Hero.id_hero,
            side: Number(Hero.side),
            isGarrison: false
        };
        LBattel.addPlayerToBattel(Hero.id_player, Elkaisar.Battel.BattelList[Battel.id_battel], Hero.side);

        LBattel.addHeroToBattel(HeroBattel, Battel, function (HeroB) {
            if (Elkaisar.Battel.BattelList[Battel.id_battel])
                Elkaisar.Battel.BattelList[Battel.id_battel].Heros.push(HeroBattel);
        });

        Elkaisar.WsLib.BattelWatchList.heroJoinNotif(null, {
            xCoord: Elkaisar.Battel.BattelList[Battel.id_battel].Battel.x_coord,
            yCoord: Elkaisar.Battel.BattelList[Battel.id_battel].Battel.y_coord,
            attackNum: Elkaisar.Battel.BattelList[Battel.id_battel].Battel.attackNum,
            defenceNum: Elkaisar.Battel.BattelList[Battel.id_battel].Battel.defenceNum
        });

    }

    static  getHeros(Battel, HeroList)
    {

        var Unit = Elkaisar.World.getUnit(Battel.Battel.x_coord, Battel.Battel.y_coord);
        var UnitHeros = Elkaisar.Lib.LWorld.unitHeros(Unit);
        var UnitEquip = Elkaisar.Lib.LWorld.unitEquip(Unit);
        for (var ii in UnitHeros)
        {
            var OneHero = UnitHeros[ii];
            Battel.Heros.unshift({
                idHero: `0-${ii}`,
                isGarrison: false,
                side: Elkaisar.Config.BATTEL_SIDE_DEF,
                Hero: OneHero.Hero,
                Army: OneHero.Army,
                Equip: UnitEquip
            });

        }
        for (var iii in Battel.Heros) {

            var Player = Battel.Players[Battel.Heros[iii].Hero.id_player];
            Elkaisar.Lib.LHero.prepareForBattel(Battel.Heros[iii], Battel);

            if (!Player)
                continue;



            Player["side"] = Battel.Heros[iii]["side"];

            Player["Troops"][Battel.Heros[iii].Army["f_1_type"]] += Battel.Heros[iii].Army["f_1_num"];
            Player["Troops"][Battel.Heros[iii].Army["f_2_type"]] += Battel.Heros[iii].Army["f_2_num"];
            Player["Troops"][Battel.Heros[iii].Army["f_3_type"]] += Battel.Heros[iii].Army["f_3_num"];
            Player["Troops"][Battel.Heros[iii].Army["b_1_type"]] += Battel.Heros[iii].Army["b_1_num"];
            Player["Troops"][Battel.Heros[iii].Army["b_2_type"]] += Battel.Heros[iii].Army["b_2_num"];
            Player["Troops"][Battel.Heros[iii].Army["b_3_type"]] += Battel.Heros[iii].Army["b_3_num"];

            Player["RemainTroops"][Battel.Heros[iii].Army["f_1_type"]] += Battel.Heros[iii].Army["f_1_num"];
            Player["RemainTroops"][Battel.Heros[iii].Army["f_2_type"]] += Battel.Heros[iii].Army["f_2_num"];
            Player["RemainTroops"][Battel.Heros[iii].Army["f_3_type"]] += Battel.Heros[iii].Army["f_3_num"];
            Player["RemainTroops"][Battel.Heros[iii].Army["b_1_type"]] += Battel.Heros[iii].Army["b_1_num"];
            Player["RemainTroops"][Battel.Heros[iii].Army["b_2_type"]] += Battel.Heros[iii].Army["b_2_num"];
            Player["RemainTroops"][Battel.Heros[iii].Army["b_3_type"]] += Battel.Heros[iii].Army["b_3_num"];
        }



    }

    static removeBattel(idBattel) {

        Elkaisar.DB.Delete("battel", "id_battel = ?", [idBattel]);
        Elkaisar.DB.Delete("battel_member", "id_battel = ?", [idBattel]);
        Elkaisar.Lib.LWorldUnit.fireOffWorldUnit(Elkaisar.Battel.BattelList[idBattel].Battel.x_coord, Elkaisar.Battel.BattelList[idBattel].Battel.y_coord);

    }

}


Elkaisar.OnEvent.on('OnServerReady', function () {
    Elkaisar.DB.SelectFrom("*", "battel", "1", [], function (Res) {
        var BattelIndex = 0;
        Res.forEach(function (Battel, index) {
            LBattel.newBattelStarted(Battel, function () {
                Elkaisar.DB.SelectFrom("*", "battel_member", `id_battel = ? AND id_hero != ${Battel.id_hero} ORDER BY ord ASC `, [Battel.id_battel], function (BattelMem) {
                    BattelMem.forEach(function (Mem, Index) {
                        LBattel.heroJoinedBattel({
                            id_hero: Mem.id_hero,
                            id_player: Mem.id_player,
                            side: Mem.side
                        }, Battel);
                    });
                });
            });
        });
    });
});



module.exports = LBattel;

