class AWorldUnit{
    
    Parm;
    idPlayer;
    constructor(idPlayer, Url){
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }
    
    async  plundePrize() {
        
        const xCoord = Elkaisar.Base.validateId(this.Parm["xCoord"]);
        const yCoord = Elkaisar.Base.validateId(this.Parm["yCoord"]);
        
        const Unit = await Elkaisar.DB.ASelectFrom("*", "world", "x = ? AND y = ?", [xCoord, yCoord]);

        const Domainant   = await Elkaisar.DB.ASelectFrom("*", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [xCoord, yCoord]);
        const playerGuild = await Elkaisar.DB.ASelectFrom("id_guild, time_join", "guild_member", "id_player = ?", [this.idPlayer]);
        const PrizeTaken  = await Elkaisar.DB.ASelectFrom("*"  ,"world_prize_taken", "x = ? AND y = ? AND id_player = ?", [xCoord, yCoord, this.idPlayer]);
        
        if (!Domainant.length) return {"state" : "error_1_0"};
        if (playerGuild.length == 0) return {"state" : "error_1_1"};
        if (Domainant[0]["id_dominant"] != playerGuild[0]["id_guild"])
                return {"state" : "error_1_2"};
        if (PrizeTaken.length > 0) return {"state" : "error_1_3"};
        if (playerGuild[0]["time_join"] + 3 * 24 * 60 * 60 > Date.now()/1000)
                return {"state" : "error_1_4"};

        var PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_plunder", "unitType = ? ORDER BY RAND()", [Unit[0]["ut"]]);

        var PrizWin = [];
        var ii, Luck, onePrize, amount;
        for(ii = 0; ii < PrizeList.length; ii++){
            onePrize = PrizeList[ii];
            Luck = Elkaisar.Base.rand(1, 1000);
            if (Luck > onePrize["win_rate"]) continue;

            amount = Elkaisar.Base.rand(onePrize["amount_min"], onePrize["amount_max"]);
            Elkaisar.Lib.LItem.addItem(this.idPlayer, onePrize["prize"], amount);
            PrizWin.push({
                "Item" : onePrize["prize"],
                "amount" : amount
            });
            
        }
        Elkaisar.DB.QueryExc("INSERT INTO world_prize_taken(x, y, id_player) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE take_time = take_time + 1", [xCoord, yCoord, this.idPlayer]);
        
        Elkaisar.Base.playerWinPrize(this.idPlayer, `تقرير إستلام غنيمة ${Elkaisar.World.WorldUnitData[Unit[0].ut].Title}`, PrizWin);
        
        return {
            "state" : "ok",
            "PrizeList" : PrizWin
        };
    }
    
    async plundeRepleCastlePrize(Unit){
        
        const Domainant   = await Elkaisar.DB.ASelectFrom("*", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Unit["x"], Unit["y"]]);
        const playerGuild = await Elkaisar.DB.ASelectFrom("id_guild, time_join", "guild_member", "id_player = ?", [this.idPlayer]);
        const PrizeTaken  = await Elkaisar.DB.ASelectFrom("*"  ,"world_prize_taken", "x = ? AND y = ? AND id_player = ?", [Unit["x"],Unit["y"], this.idPlayer]);

        if (!Domainant.length) return {"state" : "error_1_0"};
        if (playerGuild.length == 0) return {"state" : "error_1_1"};
        if (Domainant[0]["id_dominant"] != playerGuild[0]["id_guild"])
                return {"state" : "error_1_2"};
        if (PrizeTaken.length > 0) return {"state" : "error_1_3"};
        if (playerGuild[0]["time_join"] + 3 * 24 * 60 * 60 > Date.now()/1000)
                return {"state" : "error_1_4"};

        var PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_plunder", "unitType = ? ORDER BY RAND()", [Unit["ut"]]);

        var PrizWin = [];
        var ii, Luck, onePrize, amount;
        for(ii = 0; ii < PrizeList.length; ii++){
            onePrize = PrizeList[ii];
            Luck = Elkaisar.Base.rand(1, 1000);
            if (Luck > onePrize["win_rate"]) continue;

            amount = Elkaisar.Base.rand(onePrize["amount_min"], onePrize["amount_max"]);
            Elkaisar.Lib.LItem.addItem(this.idPlayer, onePrize["prize"], amount)
            PrizWin.push({
                "Item" : onePrize["prize"],
                "amount" : amount
            });
            
        }
        Elkaisar.DB.QueryExc("INSERT INTO world_prize_taken(x, y, id_player) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE take_time = take_time + 1", [Unit["x"], Unit["y"], this.idPlayer]);
        return {
            "state" : "ok",
            "PrizeList" : PrizWin
        };
    }
    
    async plundeQueenCityPrize(Unit){
        
        const Domainant   = await Elkaisar.DB.ASelectFrom("*", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Unit["x"], Unit["y"]]);
        const playerGuild = await Elkaisar.DB.ASelectFrom("id_guild, time_join", "guild_member", "id_player = ?", [this.idPlayer]);
        const PrizeTaken  = await Elkaisar.DB.ASelectFrom("*","world_prize_taken", "x = ? AND y = ? AND id_player = ?", [Unit["x"], Unit["y"], this.idPlayer]);

        if (!Domainant.length) return {"state" : "error_1_0"};
        if (playerGuild.length == 0) return {"state" : "error_1_1"};
        if (Domainant[0]["id_dominant"] != playerGuild[0]["id_guild"])
                return {"state" : "error_1_2"};
        if (PrizeTaken.length > 0) return {"state" : "error_1_3"};
        if (playerGuild[0]["time_join"] + 3 * 24 * 60 * 60 > Date.now()/1000)
                return {"state" : "error_1_4"};

        var PrizeList = selectFromTable("*", "world_unit_prize_plunder", "unitType = ? ORDER BY RAND()", [Unit["ut"]]);

        var PrizWin = [];
         var ii, Luck, onePrize, amount;
        for(ii = 0; ii < PrizeList.length; ii++){
            onePrize = PrizeList[ii];
            Luck = Elkaisar.Base.rand(1, 1000);
            if (Luck > onePrize["win_rate"]) continue;

            amount = Elkaisar.Base.rand(onePrize["amount_min"], onePrize["amount_max"]);
            Elkaisar.Lib.LItem.addItem(this.idPlayer, onePrize["prize"], amount)
            PrizWin.push({
                "Item" : onePrize["prize"],
                "amount" : amount
            });
            
        }
        Elkaisar.DB.QueryExc("INSERT INTO world_prize_taken(x, y, id_player) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE take_time = take_time + 1", [Unit["x"], Unit["y"], this.idPlayer]);
        return {
            "state" : "ok",
            "PrizeList" : PrizWin
        };
        
    }
    
    
}

module.exports = AWorldUnit;