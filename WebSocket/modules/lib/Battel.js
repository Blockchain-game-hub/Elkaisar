

exports.abort = function (con, msgObj) {

    var idHero = msgObj.idHero;
    var Player = {};
    Elkaisar.Lib.LBattel.removeHeroFromBattel(idHero, function (Players) {
        Players.forEach(function (idPlayer, Index) {
            player = Elkaisar.Base.getPlayer(idPlayer);
            if (player) {
                player.connection.sendUTF(JSON.stringify({
                    "classPath": "Battel.Aborted"
                }));
            }
        });
    });

};


exports.Join = function (con, msgObj) {

    Elkaisar.Lib.LBattel.heroJoinedBattel(msgObj.Hero, msgObj.Battel);

};

exports.start = async function (con, msgObj) {
    const idPlayer      = con.idPlayer;
    const idHero        = Elkaisar.Base.validateId(msgObj["idHero"]);
    const xCoord        = Elkaisar.Base.validateId(msgObj["xCoord"]);
    const yCoord        = Elkaisar.Base.validateId(msgObj["yCoord"]);
    const attackTask    = Elkaisar.Base.validateId(msgObj["attackTask"]);
    const Hero          = await Elkaisar.DB.ASelectFrom("hero.in_city, city.x, city.y, hero.power, hero.id_city, hero.id_hero, hero.id_player", "hero JOIN city ON city.id_city = hero.id_city", "hero.id_hero = ? AND hero.id_player = ?", [idHero, idPlayer]);
    const Unit          = Elkaisar.World.getUnit(xCoord, yCoord);
    const UnitData      = Elkaisar.World.WorldUnitData[Unit.ut]
    const powerNeeded   = UnitData.reqFitness;
    const LHArmy        = new Elkaisar.Lib.LHeroArmy();
    
    if(!Hero[0] || !UnitData)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "error_0","d" : Unit, "k": UnitData}));
    if(await LHArmy.isCarringArmy(idHero) === false)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "hero_carry_no_army"}));
    if(Hero[0]["in_city"] != Elkaisar.Config.HERO_IN_CITY)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "not_in_city"}));
    if(UnitData.maxLvl > 0 && Unit.l > UnitData.maxLvl)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "no_more_lvls"}));
    if(Unit["lo"] == Elkaisar.Config.WU_lOCKED_UNIT)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "locked_unit"}));
    if(!LHArmy.heroCanAttack(Unit["ut"]))
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "hero_cant_used"}));
    if(!(await Elkaisar.Lib.LBattelUnit.isAttackable(idPlayer, idHero, Unit)))
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "in_attackable"}));
    if(!(await Elkaisar.Lib.LBattelUnit.takeStartingPrice(idPlayer, Unit)))
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "no_enough_mat"}));
    if(Hero[0].power < powerNeeded)
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "no_enough_hero_power"}));
    if(!(await Elkaisar.Lib.LBattelUnit.onTheRoleInAttQue(idPlayer, Unit)))
        return con.sendUTF(JSON.stringify({"classPath": "Battel.StartFailed", "state": "not_his_role"}));

    Hero[0].LHArmy = LHArmy;

    Elkaisar.Lib.LBattelUnit.startBattel(idPlayer, idHero, Hero , Unit, attackTask);
   

};

exports.watchListNewBattelNotif = function (con, msgObj) {

    var key = `${msgObj.x_to}-${msgObj.y_to}`;
    var WList = Elkaisar.Arr.BattelWatchList[key];
    if (!WList)
        return;

    var ii;
    var player;
    var msg = JSON.stringify({
        "classPath": "BattelWatchList.newBattel",
        "task": "YOUR_CITY_FIRE",
        "battel": {
            "x_coord": msgObj.x_to,
            "y_coord": msgObj.y_to
        }
    });
    for (ii in WList) {

        player = Elkaisar.Base.getPlayer(WList[ii]);
        if (!player)
            continue;

        player.connection.sendUTF(msg);


    }

};


exports.BattelCanceled = function (con, msgObj)
{
    var ii;
    var Players = msgObj.Players;
    var msg = JSON.stringify({
        "classPath": "Battel.Canceled"
    });

    for (ii in Players) {

        player = Elkaisar.Base.getPlayer(Players[ii]);
        if (!player)
            continue;

        player.connection.sendUTF(msg);

    }


};


exports.getAllWorldBattels = function (con, msgObj)
{
    con.sendUTF(JSON.stringify({
        "classPath": "Base.Ack",
        ReqId      : msgObj.ReqId,
        Res        : Elkaisar.World.WorldBattels
    }));
};


