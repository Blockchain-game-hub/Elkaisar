Elkaisar.Helper.armyGainPrestige = function ($ArmyType, $amount) {

    switch ($ArmyType) {
        case "army_a":
            return $amount * 1.5;
        case "army_b":
            return $amount * 3;
        case "army_c":
            return $amount * 19;
        case "army_d":
            return $amount * 2;
        case "army_e":
            return $amount * 8;
        case "army_f":
            return $amount * 38;
        case "spies":
            return $amount * 3;
        case "wall_a":
            return $amount * 1.5;
        case "wall_b":
            return $amount * 9;
        case "wall_c":
            return $amount * 27;
    }

};

var ArmyTaskOnWork = {};

setInterval(function () {

    var Now = Date.now() / 1000;
    Elkaisar.DB.SelectFrom("*", "build_army", "time_end <= ?", [Now], function (ArmyBatches) {
       
        Elkaisar.DB.Delete("build_army", "time_end <= ?", [Now]);
        ArmyBatches.forEach(function (Batch, Index) {
            if(ArmyTaskOnWork[Batch["id"]])
                return ;
            ArmyTaskOnWork[Batch["id"]] = true;
            var PrestigeGain = Elkaisar.Helper.armyGainPrestige(Batch.army_type, Batch.amount);
            Elkaisar.DB.Update("prestige = prestige + ?", "player", "id_player = ?", [PrestigeGain, Batch.id_player]);
            Elkaisar.DB.Update("exp = exp + ?", "hero", "id_hero = (SELECT console FROM city WHERE id_city = ?)", [PrestigeGain * 2, Batch["id_city"]]);
            Elkaisar.DB.Update("`" + Batch.army_type + "` = `" + Batch.army_type + "` + ?", "city", "id_city = ?", [Batch["amount"], Batch["id_city"]]);
            Elkaisar.Lib.LSaveState.saveCityState(Batch.id_city);
            Elkaisar.Lib.LSaveState.foodOutState(Batch.id_player, Batch.id_city);
            var Player = Elkaisar.Base.getPlayer(Batch.id_player);

            if (!Player)
                return;
            Player.connection.sendUTF(JSON.stringify({
                classPath : "TimedTask.Army",
                Task      : Batch,
                prestige  : PrestigeGain
            }));
        });


    });

}, 1000);


setInterval(function (){ArmyTaskOnWork = {};}, 43*60*1000);
