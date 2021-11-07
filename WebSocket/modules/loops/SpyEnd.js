

Elkaisar.Helper.cityVictimLose = async function (Spy, Victim, Player) {

    const xCoord = Spy["x_to"];
    const yCoord = Spy["y_to"];
    const now = Date.now() / 1000;
    const idReport = await Elkaisar.DB.AInsert("id_player = ? , x_coord = ?, y_coord = ?, spy_city = ?, time_stamp = ?, state = 1", "spy_report", [Spy["id_player"], xCoord, yCoord, Spy["id_city"], now]);
    const idRV = await Elkaisar.DB.AInsert(`id_player = ? , x_coord = ?,  y_coord = ?, spy_city = ${Spy["id_city"]} , time_stamp = ${now} , state = 1`, "spy_report", [Victim["id_player"], xCoord, yCoord]);
    const content = `قام العديد من اللاعبين بحملات تجسس على  المدينة ${Victim["name"]} [${xCoord} , ${yCoord}]  و خسرت ${Victim["spies"]} جاسوس خلال هذة العملية`;


    await Elkaisar.DB.AInsert(`id_report = ${idRV.insertId}, id_player = ${Victim["id_player"]}, x_coord = ${xCoord}, y_coord = ${yCoord}, city_name = '${Victim["name"]}' , content = '${content}' , time_stamp = ${now}`, "spy_victim");
    await Elkaisar.Lib.LSaveState.saveCityState(Victim["id_city"]);
    var victimCity = await Elkaisar.DB.ASelectFrom("food , wood , stone , metal, coin , army_a, army_b, army_c, army_d, army_e, army_f, lvl", "city", `id_city = ${Victim["id_city"]}`)[0];
    var victimBuilding = await Elkaisar.DB.ASelectFrom("*", "city_building", `id_city = ${Victim["id_city"]}`)[0];
    var victimBuildingLvl = await Elkaisar.DB.ASelectFrom("*", "city_building_lvl", `id_city = ${Victim["id_city"]}`)[0];
    var studyEffect = Math.max(25 - Player["riding"], 0);

    delete(victimBuilding["id_city"]);
    delete(victimBuilding["id_player"]);
    delete(victimBuildingLvl["id_city"]);
    delete(victimBuildingLvl["id_player"]);

    var Places = [], iii, ii;
    for (iii in victimBuilding) {
        const Builing = victimBuilding[iii];
        victimBuilding[iii] = Math.floor(Builing == 0 ? 0 : Builing - Builing * Elkaisar.Base.rand(-1 * studyEffect * 3, studyEffect * 3) / 100);
    }

    for (ii in victimBuildingLvl) {
        const Builing2 = victimBuildingLvl[ii];
        victimBuildingLvl[ii] = Math.floor(victimBuilding[ii] == 0 ? 0 : Math.max(Builing2 + Elkaisar.Base.rand(-1 * studyEffect * 2, studyEffect * 2), 1));
        Places.push(`${ii} = '${victimBuilding[ii] - Builing2}'`);
    }

    const quary = `id_report = ${idReport.insertId}, id_city = '${Victim["id_city"]}', id_player = '${Victim["id_player"]}', ${Places.join(", ")}, food_res = ${victimCity["food"]}, city_lvl = ${victimCity["lvl"]},
                        wood_res = ${victimCity["wood"]}, stone_res = ${victimCity["stone"]}, metal_res = ${victimCity["metal"]}, coin_res = ${victimCity["coin"]}, army_a = ${victimCity["army_a"]}, 
                        army_b = ${victimCity["army_b"]}, army_c = ${victimCity["army_c"]}, army_d = ${victimCity["army_d"]}, army_e = ${victimCity["army_e"]}, army_f = ${victimCity["army_f"]}`;

    await Elkaisar.DB.AInsert(quary, "spy_city");


};


Elkaisar.Helper.cityVictimWin = async function (Spy, Victim)
{

    const player_spy = Elkaisar.DB.ASelectFrom("spies, x, y, name, id_city, id_player", "city", "id_city = ?", [Spy["id_city"]]);
    const now = Date.now() / 1000;

    const id_report = await Elkaisar.DB.AInsert(`id_player = ${Victim["id_player"]}, x_coord = ${Spy["x_to"]}, y_coord = ${Spy["y_to"]}, spy_city = ${Spy["id_city"]} , time_stamp = ${now} , state = 0`, "spy_report");

    const content = `قامت المدينة ${player_spy[0]["name"]} [${player_spy[0]["x"]} , ${player_spy[0]["y"]}] بحملة تجسس على المدينة ${Victim["name"]} [${Spy["x_to"]} , ${Spy["y_to"]}]  باستخدام ${Spy["spy_num"]}  جاسوس وخسرت مدينتك ${Spy["spy_num"]} جاسوس خلال هذة الحملة`;
    const victim_report = `id_report = ${id_report.insertId}, id_player = ${Victim["id_player"]} , x_coord = ${Spy["x_to"]}, y_coord = ${Spy["y_to"]}, city_name = '${Victim["name"]}' , content = '${content}' , time_stamp = ${now}`;

    const id_report_player = await Elkaisar.DB.AInsert(`id_player = ${Spy["id_player"]} , x_coord = ${Spy["x_to"]}, y_coord = ${Spy["y_to"]}, spy_city = ${Spy["id_city"]} , time_stamp = ${now} , state = 0`, "spy_report");
    const content_player = `فشل محاولة التجسس على المدينة ${Victim["name"]} [${Spy["x_to"]} , ${Spy["y_to"]}] وخسرت ${Spy["spy_num"]}جاسوس فى هذة المحاولة `;
    const player_report = `id_report = ${id_report_player.insertId} , id_player = ${Spy["id_player"]} ,
                x_coord = ${Spy["x_to"]}, y_coord = ${Spy["y_to"]}, city_name = '${Victim["name"]}' ,
                content = '${content_player}' , time_stamp = ${now}`;
    await Elkaisar.DB.AInsert(victim_report, "spy_victim");
    await Elkaisar.DB.AInsert(player_report, "spy_victim");

};

Elkaisar.Helper.spyOnBarray = async function (Spy)
{

    const now = Date.now() / 1000;
    var Army = {0: 0, "army_a": 0, "army_b": 0, "army_c": 0, "army_d": 0, "army_e": 0, "army_f": 0};
    const barryHeros = await Elkaisar.DB.ASelectFrom("*", "world_unit_hero", `x = ${Spy["x_to"]} AND y = ${Spy["y_to"]}`);
    barryHeros.forEach(function (OneHero) {
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["f_1_type"]]] += OneHero["f_1_num"];
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["f_2_type"]]] += OneHero["f_2_num"];
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["f_3_type"]]] += OneHero["f_3_num"];
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["b_1_type"]]] += OneHero["b_1_num"];
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["b_2_type"]]] += OneHero["b_2_num"];
        Army[Elkaisar.Config.CArmy.ArmyCityPlace[OneHero["b_3_type"]]] += OneHero["b_3_num"];
    });
    const id_report = await Elkaisar.DB.AInsert(`id_player = ${Spy["id_player"]} , x_coord = ${Spy["x_to"]},
                    y_coord = ${Spy["y_to"]}, spy_city = ${Spy["id_city"]}, time_stamp = ${now} , spy_for = 'barrary'  , state = 1`, "spy_report");

    await Elkaisar.DB.AInsert(`id_report = ${id_report.insertId}, army_a = ${Army["army_a"]}, army_b = ${Army["army_b"]},
                army_c = ${Army["army_c"]}, army_d = ${Army["army_d"]}, army_e = ${Army["army_e"]},  army_f = ${Army["army_f"]}`, "spy_barray");

    return [Spy["id_player"]];

}


Elkaisar.Helper.SPyOnCity = async function (Spy) {


    const vectimSpy = await ELkaisar.DB.ASelectFrom("spies , id_player , id_city, name", 'city', "x = ?   AND y = ?", [Spy["x_to"], Spy["y_to"]]);
    const study_victim = await ELkaisar.DB.ASelectFrom("riding, spying", "edu_acad", `id_player = ${vectimSpy[0]["id_player"]}`)[0];
    const studyPlayer = await ELkaisar.DB.ASelectFrom("riding, spying", "edu_acad", `id_player = ${Spy["id_player"]}`)[0];

    const victim_point = Number(vectimSpy[0]["spies"]) + vectimSpy[0]["spies"] * study_victim["riding"];
    const player_point = Number(Spy["spy_num"]) + Spy["spy_num"] * studyPlayer["riding"];

    if (victim_point < player_point) {
        await Elkaisar.DB.AUpdate("spies = GREATEST(spies - ?, 0)", "city", "id_city = ?", [Math.max(vectimSpy[0]["spies"], 0), Spy["id_city"], ]);
        await Elkaisar.DB.AUpdate("spies = 0 ", "city", "x = ?   AND y = ?", [Spy["x_to"], Spy["y_to"]]);
        Elkaisar.Helper.cityVictimLose(Spy, vectimSpy[0], studyPlayer);


    } else {
        await Elkaisar.DB.AUpdate("spies = 0", "city", "id_city = :idc", [Spy["id_city"]]);
        await Elkaisar.DB.AUpdate(`spies = GREATEST(${vectimSpy[0]["spies"]} - ${Spy["spy_num"]}, 0)`, "city", "x = ? AND y = ?", [Spy["x_to"], Spy["y_to"]]);
        Elkaisar.Helper.cityVictimWin(Spy, vectimSpy[0]);
    }

    return [
        vectimSpy[0]["id_player"],
        Spy["id_player"]
    ];

};
setInterval(async function () {

    const AllSpies = await Elkaisar.DB.ASelectFrom("*", "spy", "time_arrive <= ?", [Date.now() / 1000 + 1]);
    await Elkaisar.DB.ADelete("spy", "time_arrive <= ?", [Date.now() / 1000 + 1]);
    var Players = [];
    AllSpies.forEach(async function (OneSpy, Index) {
        await Elkaisar.DB.AUpdate(`spies = spies  +  ${OneSpy['spy_num']}`, "city", `id_city = ${OneSpy['id_city']}`);


        if (OneSpy == "city") {
            Players = await Elkaisar.Helper.SPyOnCity(OneSpy);
        } else {
            Players = await Elkaisar.Helper.spyOnBarray(OneSpy);
        }

    });

    var spy;
    var ii;
    var player;
    var msg = JSON.stringify({
        "classPath": "Battel.Spy.Notif"
    });

    for (ii in Players)
        Elkaisar.Base.sendMsgToPlayer(Players[ii], msg);
    /*
     
     return [
     "state" => "ok",
     "Players" => $Players
     ];*/


    /*Elkaisar.Base.Request.postReq(
     {
     server: Elkaisar.CONST.SERVER_ID
     },
     `${Elkaisar.CONST.BASE_URL}/ws/api/ASpyFinish/Finish`,
     function (data) {
     
     var spyArr = Elkaisar.Base.isJson(data);
     if(!spyArr)
     return console.log("SpyCheck", data);
     
     var spy;
     var ii;
     var player;
     var msg = JSON.stringify({
     "classPath" : "Battel.Spy.Notif"
     });
     
     for(ii in spyArr.Players)
     {
     player = Elkaisar.Base.getPlayer(spyArr.Players[ii]);
     if(player)
     player.connection.sendUTF(msg);
     }
     
     
     }
     );*/


}, 1000);
