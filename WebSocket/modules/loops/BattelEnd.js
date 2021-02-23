
setInterval(function () {

    var Now = Date.now() / 1000;
    Object.values(Elkaisar.Battel.BattelList).forEach(function (Battel, Index) {
        
        if(Battel.Battel.time_end < Now - 5 && Battel.Taken){
            if(Elkaisar.Battel.BattelList[Battel.Battel.id_battel])
                delete(Elkaisar.Battel.BattelList[Battel.Battel.id_battel]);
        }
        
        if (Battel.Taken)
            return;
        if (Battel.Battel.time_end > Now)
            return;
        Battel.StartedAt = Date.now();

        Battel.Taken = true;
        var Fight = new Elkaisar.Lib.LFight(Battel);
        var AFterFight = new Elkaisar.Lib.LAfterFight(Battel);

        var Unit = Elkaisar.World.getUnit(Battel.Battel.x_coord, Battel.Battel.y_coord);
        if (Unit.l > Elkaisar.World.WorldUnitData[Unit.ut].maxLvl && Elkaisar.World.WorldUnitData[Unit.ut].lvlChange)
            return AFterFight.lastLvlDone();


        Battel.Fight = Fight;
        Fight.prepareFight();
        Fight.startFight();
        AFterFight.heroBattelBack();

        if (Fight.sideWin === Elkaisar.Config.BATTEL_SIDE_ATT)
            AFterFight.afterWin(function () {
                AFterFight.afterWinAnnounce();
            });
        else
            AFterFight.afterLose();



        Fight.killHeroArmy();


        var xCoord = Battel.Battel.x_coord, yCoord = Battel.Battel.y_coord;

        for (var iii in Fight.Battel.Players) {
            var Player = Elkaisar.Base.getPlayer(Fight.Battel.Players[iii].idPlayer);
            if (!Player)
                continue;
            Player.connection.sendUTF(JSON.stringify({
                "classPath": "Battel.Finished",
                ItemPrize: Fight.Battel.Players[iii].ItemPrize,
                Battel: Battel.Battel,
                Player: Fight.Battel.Players[iii],
                sideWin: Fight.sideWin
            }));
        }
        Elkaisar.Lib.LBattel.removeBattel(Battel.Battel.id_battel);
    });
}, 1000);

