class LSaveState {

    static ResourceInEffct = {
        "food" : { "ResIn": "food_in", "ResOut": "food_out", "Study": "farming", "PlayerState": "wheat", "LaborCap": 10, "ResBarr": Elkaisar.Config.WUT_RIVER_LVL_1 + " AND " + Elkaisar.Config.WUT_RIVER_LVL_10 },
        "wood" : { "ResIn": "wood_in", "ResOut": "wood_out", "Study": "wooding", "PlayerState": "wood", "LaborCap": 10, "ResBarr": Elkaisar.Config.WUT_MOUNT_LVL_1 + " AND " + Elkaisar.Config.WUT_RIVER_LVL_10 },
        "stone": { "ResIn": "stone_in", "ResOut": "stone_out", "Study": "stoning", "PlayerState": "stone", "LaborCap": 10, "ResBarr": Elkaisar.Config.WUT_MOUNT_LVL_1 + " AND " + Elkaisar.Config.WUT_RIVER_LVL_10 },
        "metal": { "ResIn": "metal_in", "ResOut": "metal_out", "Study": "mining", "PlayerState": "metal", "LaborCap": 10, "ResBarr": Elkaisar.Config.WUT_MOUNT_LVL_1 + " AND " + Elkaisar.Config.WUT_RIVER_LVL_10 }
    };

    static async saveCityState(idCity, callBack) {
        
        var now = Math.floor(Date.now() / 1000);
        await Elkaisar.DB.AUpdate(`coin = GREATEST( LEAST( coin   + (CAST(coin_in   AS SIGNED) - CAST(coin_out   AS SIGNED))*(${now} - LS)/3600 , GREATEST( coin_cap , coin )  ) , 0), 
                 food  = GREATEST( LEAST( food   + (CAST(food_in   AS SIGNED) - CAST(food_out   AS SIGNED))*(${now} - LS)/3600 , GREATEST( food_cap  , food  )  ) , 0) , 
                 wood  = GREATEST( LEAST( wood   + (CAST(wood_in   AS SIGNED) - CAST(wood_out   AS SIGNED))*(${now} - LS)/3600 , GREATEST( wood_cap  , wood  )  ) , 0) , 
                 stone = GREATEST( LEAST( stone  + (CAST(stone_in  AS SIGNED) - CAST(stone_out  AS SIGNED))*(${now} - LS)/3600 , GREATEST( stone_cap , stone )  ) , 0) , 
                 metal = GREATEST( LEAST( metal  + (CAST(metal_in  AS SIGNED) - CAST(metal_out  AS SIGNED))*(${now} - LS)/3600 , GREATEST( metal_cap , metal )  ) , 0) , 
                 LS = ${now} `, "city", "id_city = ?", [idCity]);
        if (callBack)
            callBack(Res);

    }

    static foodOutState(idPlayer, idCity, callBack) {
        Elkaisar.DB.SelectFrom("hero_army.*", "hero_army JOIN hero ON hero.id_hero = hero_army.id_hero", "hero.id_city = ?", [idCity], function (Heros) {
            var heroFood = 0;
            for (var iii in Heros) {
                var one = Heros[iii];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["f_1_type"]] * one["f_1_num"];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["f_2_type"]] * one["f_2_num"];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["f_3_type"]] * one["f_3_num"];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["b_1_type"]] * one["b_1_num"];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["b_2_type"]] * one["b_2_num"];
                heroFood += Elkaisar.Config.CArmy.FoodEat[one["b_3_type"]] * one["b_3_num"];
            }

            Elkaisar.DB.Exist("city_colonize", "id_city_colonized = ?", [idCity], function (Found) {

                /*if (Found)
                    Elkaisar.DB.Update(`food_out = LEAST( (army_a*4 + army_b*18 + army_c*36 + army_d*5 + army_e*20 + army_f*150 + ? + 0.03*food_in) * ( 1 - (SELECT  supplying FROM  player_edu WHERE id_player = ?)*3/100 ) , food_in)`,
                            "city", "id_city = ?", [heroFood, idPlayer, idCity], function (Res) {
                        if (callBack)
                            callBack();
                    });
                else*/
                Elkaisar.DB.Update(`food_out = LEAST( (army_a*4 + army_b*18 + army_c*36 + army_d*5 + army_e*20 + army_f*150 + ?) * ( 1 - (SELECT  supplying FROM  player_edu WHERE id_player = ?)*3/100 ),  food_in)`,
                    "city", "id_city = ?", [heroFood, idPlayer, idCity], function (Res) {
                        if (callBack)
                            callBack();
                    });

            });
        });

    }

    static getConsoleEffect(idCity, callBack) {
        var consoleEffect = 0;

        Elkaisar.DB.SelectFrom(
            "point_a, point_a_plus, medal_ceasro",
            "hero JOIN city ON city.console = hero.id_hero JOIN hero_medal ON hero_medal.id_hero = city.console",
            "city.id_city = ?", [idCity], function (Res) {
                if (!Res)
                    return callBack(0);
                if (!Res[0])
                    return callBack(0);

                if (Res[0]["medal_ceasro"] > Date.now() / 1000) {
                    consoleEffect = (Res[0]["point_a"] + Res[0]["point_a_plus"]) * 1.25 * 0.5;
                } else {
                    consoleEffect = (Res[0]["point_a"] + Res[0]["point_a_plus"]) * 0.5;
                }


                callBack(consoleEffect / 100);
            });
    }

    static getResFromColonize(idCity, callBack) {
        Elkaisar.DB.SelectFrom(
            "city_colonize.id_city_colonized, city.food_in, city.wood_in, city.stone_in, city.metal_in, city.coin_in",
            "city_colonize JOIN city ON city.id_city = city_colonize.id_city_colonized", "city_colonize.id_city_colonizer = ?", [idCity], function (Cities) {
                var Res = {
                    "food": 0,
                    "wood": 0,
                    "stone": 0,
                    "metal": 0,
                    "coin": 0
                };
                Cities.forEach(function (City, Index) {
                    Res["food"] += City["food_in"];
                    Res["wood"] += City["wood_in"];
                    Res["stone"] += City["stone_in"];
                    Res["metal"] += City["metal_in"];
                    Res["coin"] += City["coin_in"];
                    if (Index >= Cities.length && callBack)
                        callBack(Res);
                });

            });
    }

    static afterCityColonizer(idPlayer, idColonizer) {
        LSaveState.getResFromColonize(idColonizer, function (Res) {

            LSaveState.saveCityState(idColonizer);
            LSaveState.coinInState(idPlayer, idColonizer, Res["coin"] * 0.03);
            LSaveState.resInState(idPlayer, idColonizer, "food", Res["food"] * 0.03);
            LSaveState.resInState(idPlayer, idColonizer, "wood", Res["wood"] * 0.03);
            LSaveState.resInState(idPlayer, idColonizer, "stone", Res["stone"] * 0.03);
            LSaveState.resInState(idPlayer, idColonizer, "metal", Res["metal"] * 0.03);

        });
    }

    static afterCityColonized(idPlayer, idColonized) {

        LSaveState.saveCityState(idColonized);
        LSaveState.foodOutState(idPlayer, idColonized);
        LSaveState.coinOutState(idPlayer, idColonized);
        LSaveState.resOutState(idPlayer, idColonized, "wood");
        LSaveState.resOutState(idPlayer, idColonized, "stone");
        LSaveState.resOutState(idPlayer, idColonized, "metal");
    }

    static resInState(idPlayer, idCity, Reso, ColoEff = 0) {

        Elkaisar.DB.SelectFrom("*", "city_jop", "id_city = ?", [idCity], function (Res) {

            var realJopNum = {};
            var takenPop = 0;

            realJopNum["food"] = Res[0]["food"] * Res[0]["food_rate"] / 100;
            realJopNum["wood"] = Res[0]["wood"] * Res[0]["wood_rate"] / 100;
            realJopNum["stone"] = Res[0]["stone"] * Res[0]["stone_rate"] / 100;
            realJopNum["metal"] = Res[0]["metal"] * Res[0]["metal_rate"] / 100;


            if (Reso === "food")
                takenPop = 0;
            else if (Reso === "wood")
                takenPop = realJopNum["food"];
            else if (Reso === "stone")
                takenPop = realJopNum["food"] + realJopNum["wood"];
            else if (Reso === "metal")
                takenPop = realJopNum["food"] + realJopNum["wood"] + realJopNum["stone"];

            Elkaisar.DB.SelectFrom(LSaveState.ResourceInEffct[Reso]["PlayerState"], "player_stat", "id_player = ?", [idPlayer], function (PlayerState) {

                var ratProMat = PlayerState[0][LSaveState.ResourceInEffct[Reso]["PlayerState"]] > Date.now() ? 0.25 : 0;


                Elkaisar.DB.SelectFrom(
                    "IFNULL(SUM(world.l),0 ) as lvlsum",
                    "world JOIN city_bar ON world.x = city_bar.x_coord AND  world.y = city_bar.y_coord",
                    `city_bar.id_city = ? AND world.ut BETWEEN  ${LSaveState.ResourceInEffct[Reso]["ResBarr"]}`, [idCity], function (BarrLvl) {
                        var BarrEff = BarrLvl[0].lvlsum * 0.03;


                        Elkaisar.DB.SelectFrom(LSaveState.ResourceInEffct[Reso]["Study"], "player_edu", "id_player = ?", [idPlayer], function (PlayerEdu) {
                            var PlayerStudy = PlayerEdu[0][LSaveState.ResourceInEffct[Reso]["Study"]] * 0.07;

                            LSaveState.getConsoleEffect(idCity, function (ConEff) {
                                var Quary = LSaveState.ResourceInEffct[Reso]["ResIn"] + ` = (LEAST( GREATEST(pop - ${takenPop} , 0) , ${realJopNum[Reso]} ) + 15)  *?* ( 1 + ${ConEff} + ${ratProMat} + ${BarrEff} + ${PlayerStudy} ) `;
                                Elkaisar.DB.Update(Quary, "city", "id_city = ?", [LSaveState.ResourceInEffct[Reso]["LaborCap"], idCity], function () {

                                });
                            });


                        });
                    });

            });

        });

    }


    static resOutState(idPlayer, idCity, $res) {
        /*Elkaisar.DB.Exist("city_colonize", "id_city_colonized = ?", [idCity], function(Found){
            Elkaisar.DB.Update(LSaveState.ResourceInEffct[$res]["ResOut"]+" = "+LSaveState.ResourceInEffct[$res]["ResIn"]+"*0.03", "city", "id_city = ?", [idCity]);
        });*/
    }


    static coinInState(idPlayer, idCity, ColoEff = 0) {

        Elkaisar.DB.SelectFrom("coin", "player_stat", "id_player = ?", [idPlayer], function (Res) {
            var matrialEffect = Res[0];
            var ratio_pro_mat = matrialEffect["coin"] > Date.now() / 1000 ? 0.25 : 0;
            LSaveState.getConsoleEffect(idCity, function (ConEff) {
                var Quary = ` coin_in = (
                             (SELECT accounting FROM player_edu WHERE id_player = ${idPlayer})*10/100) * (taxs/100)*pop 
                             + (taxs/100)*pop + ${ConEff} *(taxs/100)*pop 
                             + (taxs/100)*pop*${ratio_pro_mat} + ${ColoEff}`;
                Elkaisar.DB.Update(Quary, "city", "id_city = ?", [idCity], function () { });
            });
        });

    }

    static coinOutState(idPlayer, idCity) {
        Elkaisar.DB.SelectFrom(" SUM(lvl) as c", "hero", "id_city = ?", [idCity], function (HeroLvl) {

            var LvlSum = HeroLvl[0]["c"];
            Elkaisar.DB.Exist("city_colonize", "id_city_colonized = ?", [idCity], function (Found) {
                /*if(Found)
                    Elkaisar.DB.Update("coin_out = LEAST(coin_in + coin_in*0.03, ?)", "city", "id_city = ?", [LvlSum*10, idCity]);
                else */
                Elkaisar.DB.Update("coin_out = LEAST(coin_in , ?)", "city", "id_city = ?", [LvlSum * 10, idCity]);
            });

        });
    }


    static storeRatio(idCity) {

        LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LCity.refreshStoreCap(idCity, function (Storage) {
            Elkaisar.DB.SelectFrom("*", "city_storage", "id_city = ?", [idCity], function (CityStorage) {
                var food_cap = CityStorage[0]["total_cap"] * CityStorage[0]["food_storage_ratio"] / 100;
                var wood_cap = CityStorage[0]["total_cap"] * CityStorage[0]["wood_storage_ratio"] / 100;
                var stone_cap = CityStorage[0]["total_cap"] * CityStorage[0]["stone_storage_ratio"] / 100;
                var metal_cap = CityStorage[0]["total_cap"] * CityStorage[0]["metal_storage_ratio"] / 100;
                var Quary = `food_cap = ${food_cap} , wood_cap = ${wood_cap} , metal_cap = ${metal_cap} , stone_cap = ${stone_cap}`;
                Elkaisar.DB.Update(Quary, "city", "id_city = ?", [idCity]);
            });
        });
    }

}



module.exports = LSaveState;


