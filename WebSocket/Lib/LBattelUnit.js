


class LBattelUnit {
    
    static async getBattelById(idBattel){
        return await Elkaisar.DB.ASelectFrom(
                        "battel.*, player.name AS PlayerName, city.name AS CityName, player.id_guild AS idGuild,player.guild AS GuildName",
                        "battel JOIN city ON city.x = battel.x_city AND city.y = battel.y_city  JOIN player ON battel.id_player = player.id_player",
                        "id_battel = ?", [idBattel]);
    }

    static async isAttackable(idPlayer, idHero, Unit) {

        if (Elkaisar.Lib.LWorldUnit.isFightField(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isChallangeField(Unit["ut"]))
            return false;
        if (Elkaisar.Lib.LWorldUnit.isArenaGuild(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isStatueWar(Unit["ut"])) {
            return (await Elkaisar.DB.AExist("guild_member", "id_player = ?", [idPlayer]));
        } else if (Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isQueenCity(Unit["ut"])) {

            if (await Elkaisar.DB.AExist("guild_member", "id_player = ?", [idPlayer]) == false)
                return false;
            if (await Elkaisar.DB.AExist("battel", "x_coord = ? AND y_coord = ?", [Unit.x, Unit.y]) == true)
                return false;
        } else if (Elkaisar.Lib.LWorldUnit.isEmpty(Unit["ut"])) {
            return false;
        }

        return true;
    }

    static async takeStartingPrice(idPlayer, Unit) {

        const UnitData = Elkaisar.World.WorldUnitData[Unit.ut];
        
        if (!UnitData)
            return false;
            
        if (!Array.isArray(UnitData.MakeReq))
            return false;
        if (UnitData.MakeReq.legnth == 0)
            return true;
        var iii;
        var PlayerAmount;
        for (iii in UnitData.MakeReq) {
            PlayerAmount = await Elkaisar.DB.ASelectFrom("*", "player_item", "id_player = ? AND id_item = ?", [idPlayer, UnitData.MakeReq[iii].Item]);
            if (!PlayerAmount[0])
                return false;
            if (PlayerAmount[0].amount < UnitData.MakeReq[iii].amount)
                return false;
            Elkaisar.DB.Update("amount = amount - ?", "player_item", "id_player = ? AND id_item = ?", [UnitData.MakeReq[iii].amount, idPlayer, UnitData.MakeReq[iii].Item]);
        }

        return true;

    }
    
    static async takeJoinPrice(idPlayer, ut) {

        const UnitData = Elkaisar.World.WorldUnitData[ut];
        
        if (!UnitData)
            return false;
            
        if (!Array.isArray(UnitData.JoinReq))
            return false;
        if (UnitData.JoinReq.legnth == 0)
            return true;
        var iii;
        var PlayerAmount;
        for (iii in UnitData.JoinReq) {
            PlayerAmount = await Elkaisar.DB.ASelectFrom("*", "player_item", "id_player = ? AND id_item = ?", [idPlayer, UnitData.JoinReq[iii].Item]);
            if (!PlayerAmount[0])
                return false;
            if (PlayerAmount[0].amount < UnitData.JoinReq[iii].amount)
                return false;
            Elkaisar.DB.Update("amount = amount - ?", "player_item", "id_player = ? AND id_item = ?", [UnitData.JoinReq[iii].amount, idPlayer, UnitData.JoinReq[iii].Item]);
        }

        return true;

    }
    
    
    
    

    static takeHeroPower(idHero, unitType) {

        const UnitData = Elkaisar.World.WorldUnitData[unitType];
        if (!UnitData)
            return false;
        if (UnitData.reqFitness == undefined)
            return false;
        Elkaisar.DB.Update("power = power - ?", "hero", "id_hero = ?", [UnitData.reqFitness, idHero]);
        return UnitData.reqFitness;
        
       
    }

    static async onTheRoleInAttQue(idPlayer, Unit) {

        if (!Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit["ut"]))
            return true;

        const GuildPlayer = await Elkaisar.DB.ASelectFrom("id_player, id_guild, rank", "guild_member", "id_player = ?", [idPlayer]);

        if (!GuildPlayer[0])
            return false;
        if (GuildPlayer[0]["rank"] < Elkaisar.Config.GUILD_R_DEPUTY_2)
            return false;
        const QueueRole = await Elkaisar.DB.ASelectFrom("*", "world_attack_queue", "x_coord = ? AND y_coord = ? ORDER BY id ASC LIMIT 1", [Unit["x"], Unit["y"]]);

        if (!QueueRole.length)
            return false;
        if (QueueRole[0]["id_guild"] != GuildPlayer[0]["id_guild"])
            return false;
        if (Date.now() / 1000 < QueueRole[0]["time_start"])
            return false;
        if (Date.now() / 1000 > QueueRole[0]["time_end"])
            return false;

        return true;

    }



    static calAttackTime(City, Unit, slowestSpeed) {
      

        if (
            Elkaisar.Lib.LWorldUnit.isAsianSquads(Unit.ut) || Elkaisar.Lib.LWorldUnit.isGangStar(Unit.ut)
            || Elkaisar.Lib.LWorldUnit.isCarthagianArmies(Unit.ut) || Elkaisar.Lib.LWorldUnit.isArenaChallange(Unit.ut)
            || Elkaisar.Lib.LWorldUnit.isArenaDeath(Unit.ut) || Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut)
            || Elkaisar.Lib.LWorldUnit.isMonawrat(Unit.ut) || Elkaisar.Lib.LWorldUnit.isStatueWar(Unit.ut)
            || Elkaisar.Lib.LWorldUnit.isStatueWalf(Unit.ut)
        )
            return 120;
        else if (Elkaisar.Lib.LWorldUnit.isArenaGuild(Unit.ut))
            return 5 * 60;
        else if (Elkaisar.Lib.LWorldUnit.isQueenCity(Unit.ut) || Elkaisar.Lib.LWorldUnit.isSeaCity(Unit.ut))
            return 15 * 60;
        else if (Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit.ut))
            return 60 * 60;

        const distance = Elkaisar.Lib.LWorldUnit.calDist(City["x"], Unit["x"], City["y"], Unit["y"]);

        if (Elkaisar.Lib.LWorldUnit.isCity(Unit.ut)) {
            return Math.max(Math.ceil(distance / slowestSpeed), 15 * 60);
        } else if (Elkaisar.Lib.LWorldUnit.isCamp(Unit.ut)) {

            return Math.max(Math.ceil(distance / slowestSpeed), 120);
        }

        return Math.ceil(distance / slowestSpeed);

    }
    static async getBattelById(idBattel) {

        return (await Elkaisar.DB.ASelectFrom(
            "battel.*, player.name AS PlayerName, city.name AS CityName, player.id_guild AS idGuild,player.guild AS GuildName",
            "battel JOIN city ON city.x = battel.x_city AND city.y = battel.y_city  JOIN player ON battel.id_player = player.id_player",
            "id_battel = ?", [idBattel]))[0];
    }


    static async announceStart(idPlayer, Unit) {
        if (!Elkaisar.Lib.LWorldUnit.isQueenCity(Unit["ut"]) && !Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit["ut"]))
            return;



        const Player = await Elkaisar.DB.ASelectFrom(
            "player.id_player, player.name AS PlayerName, guild.name AS GuildName, guild.slog_top, guild.slog_cnt, guild.slog_btm, player.id_guild",
            "player LEFT JOIN guild ON guild.id_guild = player.id_guild", "player.id_player = ?", [idPlayer]);
        if (!Player[0])
            return;
        Player[0]["x_coord"] = Unit["x"];
        Player[0]["y_coord"] = Unit["y"];
        const msg = JSON.stringify({
            "classPath": "ServerAnnounce.Battel.Started",
            "GuildName": Player[0].GuildName,
            "PlayerName": Player[0].PlayerName,
            "slog_top": Player[0].slog_top,
            "slog_cnt": Player[0].slog_cnt,
            "slog_btm": Player[0].slog_btm,
            "id_guild": Player[0].id_guild,
            "id_player": Player[0].id_player,
            "xCoord": Player[0].x_coord,
            "yCoord": Player[0].y_coord
        });
        Elkaisar.Base.broadcast(msg);
    }

    static join(idPlayer, Battel, Hero, side) {
        const Ord = Date.now() / 1000;
        if (side == Elkaisar.Config.BATTEL_SIDE_ATT)
            Elkaisar.DB.Update("attackNum = attackNum + 1", "battel", "id_battel = ?", [Battel["id_battel"]]);
        else if (side == Elkaisar.Config.BATTEL_SIDE_DEF)
            Elkaisar.DB.Update("defenceNum = defenceNum + 1", "battel", "id_battel = ?", [Battel["id_battel"]]);
        Hero.side = side;
        Elkaisar.Lib.LBattel.heroJoinedBattel(Hero, Battel);
        Elkaisar.DB.Insert(
            "id_battel = ? , id_player = ? ,id_hero = ? ,  side = ? , ord = ?", "battel_member",
            [Battel["id_battel"], idPlayer, Hero["id_hero"], side, Ord]
        );
    }

    static async involvedPlayers(Unit) {
        var ArmyCapital = [];
        var City = [];
        var Garrison = await Elkaisar.DB.ASelectFrom("id_player", "world_unit_garrison", "x_coord = ? AND y_coord = ?", [Unit["x"], Unit["y"]]);
        var Players = {};


        if (Elkaisar.Lib.LWorldUnit.isCity(Unit["ut"]))
            City = await Elkaisar.DB.ASelectFrom("id_player", "city", "x = ? AND y = ?", [Unit["x"], Unit["y"]]);
        if (Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit["ut"]))
            ArmyCapital = await Elkaisar.DB.ASelectFrom("id_dominant", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Unit["x"], Unit["y"]]);

        City.forEach(function (C) { Players[C.id_player] = true });
        ArmyCapital.forEach(function (C) { Players[C.id_player] = true });
        Garrison.forEach(function (C) { Players[C.id_dominant] = true });

        return Players;

    }
    static worldUnitFire(Unit) {
        if (Unit["s"] != Elkaisar.Config.WU_ON_FIRE) {
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "World.Fire.On",
                xCoord: Unit.x,
                yCoord: Unit.y
            }));
        }
        Unit.s = Elkaisar.Config.WU_ON_FIRE;
        Elkaisar.World.OnFireUnits[Unit.x * 500 + Unit.y] = true;
    }
    static async  informRelatedPlayers (Battel) {
    
        var Unit = Elkaisar.World.getUnit(Battel.x_coord, Battel.y_coord);
        if(!Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut) && Elkaisar.Lib.LWorldUnit.isCity(Unit.ut))
        return;
        const Attack = await Elkaisar.DB.ASelectFrom(
            "player.name AS PlayerName, city.name AS CityName, city.x, city.y",
            "city JOIN player ON player.id_player = city.id_player",
            "city.x = ? AND city.y = ?", [Battel.x_city, Battel.y_city]);
    
        if (Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit.ut)) {
            const Defence = await Elkaisar.DB.ASelectFrom("id_dominant", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Unit["x"], Unit["y"]]);
            if (!Defence.legnth)
                return;
            const Player = Elkaisar.Base.getPlayer(Defence[0]["id_dominant"]);
            if (Player)
                Player.connection.sendUTF(JSON.stringify({
                    "classPath": "Battel.startAnnounce", "Battel": Battel, 
                    "Attacker": Attack[0], "Defender": Defence[0]
                }));
    
        } else if (Elkaisar.Lib.LWorldUnit.isCity(Unit.ut)) {
            const GuildMembers =   Elkaisar.DB.ASelectFrom("id_player", "player", "online = 1 AND id_guild = ?", [Defence[0]["id_guild"]]);
            
            const Msg = JSON.stringify({
                "classPath": "Battel.startAnnounce", "Battel": Battel,
                "Attacker": Attack[0], "Defender": Defence[0]
            });
            var P;
            if (Players.length && Defence[0]["id_guild"] != null)
                Players.forEach(function (Player, Index) {
                    P = Elkaisar.Base.getPlayer(Player["id_player"]);
                    if (P)
                        P.connection.sendUTF(Msg);
                });
            else {
                P = Elkaisar.Base.getPlayer(Defence[0]["id_player"]);
                if (P)
                    P.connection.sendUTF(Msg);
            }
    
        }
    
    };
    static async informInvolvedPlayers(Battel) {

        const Unit = Elkaisar.World.getUnit(Battel.x_coord, Battel.y_coord);
        const UnitData = Elkaisar.World.WorldUnitData[Unit.ut];
        var InvolvedPlayer = await Elkaisar.Lib.LBattelUnit.involvedPlayers(Unit);
        InvolvedPlayer[Battel.id_player] = true;

        Object.keys(InvolvedPlayer).forEach(function (idp) {
            const Player = Elkaisar.Base.getPlayer(idp);
            if (Player) {
                Player.connection.sendUTF(JSON.stringify({
                    "classPath": "Battel.Started",
                    "Battel": Battel,
                    "StartingPrice": UnitData.MakeReq,
                    "state": "ok"
                }));
            }
        });

    }
    static  showBattelOnMap(Battel) {
        const x = Battel.x_coord;
        const y = Battel.y_coord;
        const WorldBattelKey = `${Battel.x_city}.${Battel.y_city}-${Battel.x_coord}.${Battel.x_coord}`;
        if (Elkaisar.World.WorldBattels[WorldBattelKey])
            return;
        Elkaisar.World.WorldBattels[WorldBattelKey] = {
            classPath: "World.Battel.Started",
            xCoord: Battel.x_coord,
            yCoord: Battel.y_coord,
            xCity: Battel.x_city,
            yCity: Battel.y_city,
            idPlayer: Battel.id_player,
            idGuild: Battel.idGuild,
            CityName: Battel.CityName,
            PlayerName: Battel.PlayerName,
            GuildName: Battel.GuildName,
            timeEnd: Battel.time_end,
            timeStart: Battel.time_start
        };
        Elkaisar.Base.broadcast(JSON.stringify(Elkaisar.World.WorldBattels[WorldBattelKey]));
    }

    static async startBattel(idPlayer, idHero, Hero, Unit, attackTask) {



        Elkaisar.DB.Update("s = ?", "world", "x = ? AND y = ?", [Elkaisar.Config.WU_ON_FIRE, Unit.x, Unit.y]);
        Elkaisar.DB.Update("in_city = 0", "hero", "id_hero = ?", [idHero]);
        const attackTime = Elkaisar.Lib.LBattelUnit.calAttackTime(Hero[0], Unit, Hero[0].LHArmy.getSlowestSpeed());
        const now = Date.now() / 1000;
        const InsBattel = await Elkaisar.DB.AInsert(
            "id_hero = ?, time_start = ?, time_end = ?, x_coord = ?, y_coord = ?, id_player = ?, x_city = ? , y_city = ? , task = ?", "battel",
            [idHero, now, now + attackTime, Unit["x"], Unit["y"], idPlayer, Hero[0]["x"], Hero[0]["y"], attackTask]
        );
        const Battel = await Elkaisar.Lib.LBattelUnit.getBattelById(InsBattel.insertId);


        Elkaisar.Lib.LBattel.newBattelStarted(Battel);
        Elkaisar.Lib.LBattelUnit.join(idPlayer, Battel, Hero[0], Elkaisar.Config.BATTEL_SIDE_ATT);

        const TakenPower = Elkaisar.Lib.LBattelUnit.takeHeroPower(idHero, Unit["ut"]);
        Elkaisar.Lib.LBattelUnit.announceStart(idPlayer, Unit);
        Elkaisar.Lib.LBattelUnit.worldUnitFire(Unit);
        Elkaisar.Lib.LBattelUnit.informInvolvedPlayers(Battel);
        Elkaisar.Lib.LBattelUnit.informRelatedPlayers(Battel);
        Elkaisar.Lib.LBattelUnit.showBattelOnMap(Battel);
        Elkaisar.Base.sendMsgToPlayer(idPlayer , JSON.stringify({
            "classPath": "Hero.Power.Added",
            "Heros": [{idHero: idHero, power: Hero[0].power - TakenPower}]
        }));

    }


}


module.exports = LBattelUnit;

