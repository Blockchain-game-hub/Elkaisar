class LAfterFight
{
    Battel = {};
    BattelReport;
    static RealHeroCount = 450000;
    static SeaCityMinAttackHero = 80;
    constructor(Battel)
    {
        this.Battel = Battel;
    }

    updateLvl() {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);


        if (
                Elkaisar.Lib.LWorldUnit.isMonawrat(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isCamp(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isAsianSquads(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isGangStar(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isCarthagianArmies(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isStatueWalf(Unit["ut"]) ||
                Elkaisar.Lib.LWorldUnit.isStatueWar(Unit["ut"])) {

            Elkaisar.DB.Update("l = l + 1", "world", "x = ? AND y = ? AND l <= ? ", [Unit.x, Unit.y, Elkaisar.World.WorldUnitData[Unit.ut].maxLvl]);
            Unit.l++;
        }
    }

    setDominant() {

        var now = Math.floor(Date.now() / 1000);
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);

        if (
                Elkaisar.Lib.LWorldUnit.isArmyCapital(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isArena(Unit["ut"])
                ) {

            var idDominant = 0;

            if (Elkaisar.Lib.LWorldUnit.isArenaGuild(Unit["ut"]))
                idDominant = this.Battel.Players[this.Battel.Battel.id_player].Player.idGuild;
            else
                idDominant = this.Battel.Battel.id_player;

            Elkaisar.DB.Update("duration = ? - time_stamp", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [now, Unit.x, Unit.y]);
            Elkaisar.DB.Update("time_end = time_end + 300", "battel", "x_coord = ? AND y_coord = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Insert("x = ?, y = ?, id_dominant = ?, time_stamp = ?", "world_unit_rank", [Unit.x, Unit.y, idDominant, now]);


        } else if (Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isQueenCity(Unit["ut"])) {

            var idGuild = this.Battel.Players[this.Battel.Battel.id_player].Player.idGuild;
            if (!idGuild)
                return;

            var idDominant = idGuild;

            if (Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit["ut"]))
                Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [Unit.x, Unit.y]);


            Elkaisar.DB.Update("duration = ? - time_stamp", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [now, Unit.x, Unit.y]);
            Elkaisar.DB.Update("time_end = time_end + 300", "battel", "x_coord = ? AND y_coord = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Insert("x = ?, y = ?, id_dominant = ?, id_guild = ?, time_stamp = ?", "world_unit_rank", [Unit.x, Unit.y, idDominant, idDominant, now]);

        }
    }

    setCityColonizer()
    {
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        var This = this;
        Elkaisar.DB.SelectFrom("loy, id_city,  player.id_player, city.name AS CityName, player.name AS PlayerName, player.id_guild",
                "city JOIN player ON player.id_player = city.id_player", "x = ? AND y = ?", [Unit.x, Unit.y],
                function (CityLoy) {
                    Elkaisar.DB.SelectFrom("id_city, player.id_player, player.name AS PlayerName, player.id_guild, player.city_flag",
                            "city JOIN player ON player.id_player = city.id_player", "x = ? AND y = ?", [This.Battel.Battel.x_city, This.Battel.Battel.y_city],
                            function (CityColonizer) {
                                if (!CityLoy[0] || !CityColonizer[0])
                                    return;
                                if (CityLoy[0]["loy"] <= 0)
                                {
                                    Elkaisar.DB.SelectFrom("*", "city_colonize", "id_city_colonized = ?", [CityLoy[0]["id_city"]], function (Colonizer) {
                                        if (Colonizer[0]) {
                                            if (Colonizer[0]["time_stamp"] + 24 * 60 * 60 > Date.now() / 1000)
                                                Elkaisar.Lib.LWorld.removeCityColonizer(Colonizer[0]["id_city_colonized"]);
                                            else
                                                return;
                                        }



                                        Elkaisar.DB.Insert(
                                                `id_colonizer = ${CityColonizer[0]["id_player"]}, id_colonized = ${CityLoy[0]["id_player"]},
                                                    id_city_colonizer  = ${CityColonizer[0]["id_city"]}, id_city_colonized = ${CityLoy[0]["id_city"]},
                                                    time_stamp = ${Math.floor(Date.now() / 1000)}`, `city_colonize`, []);

                                        Elkaisar.Lib.LSaveState.afterCityColonized(CityLoy[0]["id_player"], CityLoy[0]["id_city"]);
                                        Elkaisar.Lib.LSaveState.afterCityColonizer(CityColonizer[0]["id_player"], CityColonizer[0]["id_city"]);

                                        Elkaisar.WsLib.ServerAnnounce.CityColonized(null, {
                                            "ColonizerName": CityColonizer[0]["PlayerName"],
                                            "ColonizedName": CityLoy[0]["PlayerName"],
                                            "CityColonizedName": CityLoy[0]["CityName"],
                                            "xCoord": Unit.x,
                                            "yCoord": Unit.y,
                                            "ColonizerIdGuild": CityColonizer[0]["id_guild"],
                                            "ColonizerIdPlayer": CityColonizer[0]["id_player"],
                                            "CityColonizerFlag": CityColonizer[0]["city_flag"]
                                        });
                                    });
                                } else {
                                    Elkaisar.DB.Update("loy = ?", "city", "id_city = ?", [Math.max(0, CityLoy[0]["loy"] - 5), CityLoy[0]["id_city"]]);
                                }

                            }
                    );
                });
    }

    setBarrayColonizer()
    {
        var idPlayer = this.Battel.Battel["id_player"];
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel["x_coord"], this.Battel.Battel["y_coord"]);
        var This = this;
        var idCity = 0;
        for (var iii in this.Battel.Heros)
            if (this.Battel.Heros[iii].Hero.id_hero === this.Battel.Battel.id_hero)
            {
                idCity = this.Battel.Heros[iii].Hero.id_city;
                break;
            }

        Elkaisar.DB.SelectFrom("COUNT(*)as coun", "city_bar", "id_city = ?", [idCity], function (BarrCount) {
            Elkaisar.DB.SelectFrom("palace", "city_building_lvl", "id_city = ?", [idCity], function (PalaceLvl) {
                if (BarrCount[0]["coun"] >= Math.min(PalaceLvl[0]["palace"], 10))
                    return;

                Elkaisar.DB.Delete("city_bar", "x_coord = ? AND y_coord = ?", [Unit.x, Unit.y], function (Res) {
                    Elkaisar.DB.Insert(
                            "id_player = ?, id_city = ?, x_coord = ?, y_coord = ?",
                            "city_bar",
                            [This.Battel.Battel.id_player, idCity, Unit["x"], Unit["y"]]);
                });
            });
        });
    }

    setColonizer() {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        if (this.Battel.Battel["task"] != Elkaisar.Config.BATTEL_TASK_DOMINATE)
            return;

        if (Elkaisar.Lib.LWorldUnit.isBarrary(Unit["ut"]))
            this.setBarrayColonizer();
        if (Elkaisar.Lib.LWorldUnit.isCity(Unit["ut"]))
            this.setCityColonizer();
    }

    setRank()
    {
        var This = this;
        if (this.Battel.Battel["task"] === Elkaisar.Config.BATTEL_TASK_CHALLANGE)
        {
            Elkaisar.DB.QueryExc(`UPDATE
                                    arena_player_challange AS a1
                                    JOIN arena_player_challange AS a2 ON
                                    ( a1.id_player = ${This.Battel.Battel["id_player_def"]} AND a2.id_player = ${This.Battel.Battel["id_player"]} )
                                    SET
                                    a1.rank = a2.rank,
                                    a2.rank = a1.rank WHERE a2.rank > a1.rank;`, [], function (Res) {

                Elkaisar.DB.Update("win = win + 1, exp = exp + 3", "arena_player_challange", "id_player = ?", [This.Battel.Battel["id_player"]]);
                Elkaisar.DB.SelectFrom("exp, lvl, rank", "arena_player_challange", "id_player = ?", [This.Battel.Battel.id_player], function (Arena) {

                    if (Arena[0]["rank"] == 1 && Res.affectedRows > 0)
                        Elkaisar.Base.broadcast(JSON.stringify({
                            classPath: "ServerAnnounce.KingOfArenaChallange",
                            Player: This.Battel.Players[This.Battel.Battel.id_player].Player
                        }));

                    if (Arena[0]["lvl"] >= 15)
                        return;

                    if (Arena[0]["exp"] >= Elkaisar.Config.ArenaChallangeLvlReqExp[Number(Arena[0]["lvl"] + 1)]) {
                        Elkaisar.DB.Update("lvl = lvl + 1", "arena_player_challange", "id_player = ?", [This.Battel.Battel.id_player]);

                        Elkaisar.Base.broadcast(JSON.stringify({
                            classPath: "ServerAnnounce.ArenachallangeLvlUp",
                            Player: This.Battel.Players[This.Battel.Battel.id_player].Player,
                            ArenaData: Arena[0]
                        }));
                    }
                    ;
                });
            });


        }
    }

    afterWin(callBack) {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        this.Battel.WinLvl = Unit.l;
        var This = this;
        this.BattelReport = new Elkaisar.Lib.LBattelReport(this.Battel);
        this.updateLvl();
        this.setDominant();
        this.setColonizer();
        this.setRank();
        this.BattelReport.addReport(function () {
            This.giveWinnerPrize(callBack);
        });

    }

    afterLose(callBack) {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        if (this.Battel.Battel["task"] === Elkaisar.Config.BATTEL_TASK_CHALLANGE)
            Elkaisar.DB.Update("lose = lose + 1", "arena_player_challange", "id_player = ?", [this.Battel.Battel.id_player]);
        this.Battel.WinLvl = Unit.l;
        var This = this;

        this.BattelReport = new Elkaisar.Lib.LBattelReport(this.Battel);

        this.BattelReport.addReport(function () {
            This.giveLoserPrize(callBack);
        });

    }

    keepGarrisonHero(Hero) {
        if (Hero["is_garrsion"])
            for (var ii in Hero["real_eff"])
                if (Hero["real_eff"][ii].unit > 0)
                    ;
        return false;
    }

    heroBattelBack() {

        if (this.Battel.Battel["task"] === Elkaisar.Config.BATTEL_TASK_CHALLANGE)
            return;
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        var This = this;

        This.Battel.HeroReadyList.forEach(function (OneHero, Index) {

            if (OneHero.id_hero <= 0)
                return;
            var RT = Elkaisar.Lib.LWorldUnit.calReturningTime(OneHero, Unit);


            if (This.keepGarrisonHero(OneHero))
                return;

            Elkaisar.DB.Insert(
                    `id_hero = ${OneHero.id_hero}, x_from  = ${Unit.x}, y_from = ${Unit.y},
                     task = ${This.Battel.Battel.task}, x_to = ${OneHero.x_coord} , y_to = ${OneHero.y_coord},
                     time_back = ${RT} , id_player = ${OneHero["id_player"]}`, "hero_back", []);

        });
    }

    getAllPlayers()
    {
        return this.Battel.Players;
    }

    giveWinnerPrize(callBack)
    {
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        var This = this;

        var Prize = new Elkaisar.Lib.LPrize(This.Battel);

        if (Elkaisar.Lib.LWorldUnit.isSharablePrize(Unit.ut))
            Prize.heroShare();

        if (Elkaisar.Lib.LWorldUnit.isCity(Unit.ut)) {
            Prize.getCityAvailRes(function () {
                Prize.takeCityRes(function () {
                    Object.values(This.Battel.Players).forEach(function (Player, Index) {
                        if (Player.idPlayer <= 0)
                            return;
                        if (Player.side === Elkaisar.Config.BATTEL_SIDE_DEF)
                            return;
                        if (Player.idPlayer === This.Battel.Battel.id_player)
                            Prize.givePrize(Player, function () {
                                This.BattelReport.addPrize(Player);
                            });
                        else if (Elkaisar.Lib.LWorldUnit.isSharablePrize(Unit.ut))
                            Prize.givePrize(Player, function () {
                                This.BattelReport.addPrize(Player);
                            });
                    });

                    if (callBack)
                        callBack();
                });
            });

            return;
        } else if (Elkaisar.Lib.LWorldUnit.isSeaCity(Unit.ut)) {


            var TotalHeroCount = 0;

            for (var iii in this.Battel.HeroReadyList) {
                var OneHero = this.Battel.HeroReadyList[iii];
                var OneHeroRealCount = 0;

                for (var ii in OneHero.type) {
                    var armyType = OneHero.type[ii];
                    OneHeroRealCount += Elkaisar.Config.CArmy.ArmyCap[armyType] * OneHero["pre"][ii];
                }
                if (OneHeroRealCount > LAfterFight.RealHeroCount)
                    TotalHeroCount++;
            }
            if (TotalHeroCount < LAfterFight.SeaCityMinAttackHero) {
                if (callBack)
                    callBack();
                return ;
            }

        }
        
        
        Object.values(This.Battel.Players).forEach(function (Player, Index) {
            if (Player.idPlayer <= 0)
                return;
            if (Player.side === Elkaisar.Config.BATTEL_SIDE_DEF)
                return;
            if (Player.idPlayer === This.Battel.Battel.id_player)
                Prize.givePrize(Player, function () {
                    This.BattelReport.addPrize(Player);
                });
            else if (Elkaisar.Lib.LWorldUnit.isSharablePrize(Unit.ut))
                Prize.givePrize(Player, function () {
                    This.BattelReport.addPrize(Player);
                });
        });
        if (callBack)
            callBack();




    }

    giveLoserPrize(callBack) {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        var This = this;


        var Prize = new Elkaisar.Lib.LPrize(This.Battel);

        if (Elkaisar.Lib.LWorldUnit.isSharablePrize(Unit.ut))
            Prize.heroShare();

        Object.values(This.Battel.Players).forEach(function (Player, Index) {
            if (Player.idPlayer <= 0)
                return;
            if (Player.side === Elkaisar.Config.BATTEL_SIDE_DEF)
                return;
            if (Player.idPlayer === This.Battel.Battel.id_player)
                Prize.givePrize(Player, function () {
                    This.BattelReport.addPrize(Player);
                });
            else if (Elkaisar.Lib.LWorldUnit.isSharablePrize(Unit.ut))
                Prize.givePrize(Player, function () {
                    This.BattelReport.addPrize(Player);
                });
        });

    }

    lastLvlDone() {
        var This = this;

        Object.values(Elkaisar.Battel.BattelList).forEach(function (Battel, Index) {
            var TimeBack = 2 * Date.now() / 1000 - Battel.Battel.time_start;
            if (Battel.Battel.x_coord !== This.Battel.Battel.x_coord)
                return;
            if (Battel.Battel.y_coord !== This.Battel.Battel.y_coord)
                return;

            Battel.HeroReadyList.forEach(function (Hero, HeroIndex) {
                if (Hero.id_hero <= 0)
                    return;

                Elkaisar.DB.Insert(
                        `id_hero = ${Hero.id_hero}, x_from  = ${Battel.Battel.x_coord}, y_from = ${Battel.Battel.y_coord},
                     task = ${This.Battel.Battel.task}, x_to = ${Hero.x_coord} , y_to = ${Hero.y_coord},
                     time_back = ${TimeBack} , id_player = ${Hero["id_player"]}`, "hero_back", []);
            });
            Elkaisar.Lib.LBattel.removeBattel(Battel.Battel.id_battel);
            Elkaisar.WsLib.Battel.BattelCanceled(null, {
                Players: Object.keys(Battel.Players)
            });
        });
    }

    afterWinAnnounce() {

        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);

        if (!Elkaisar.Lib.LWorldUnit.afterWinAnnounceable(Unit, this.Battel.WinLvl))
            return;

        if (Elkaisar.Lib.LWorldUnit.isRepelCastle(Unit.ut) || Elkaisar.Lib.LWorldUnit.isQueenCity(Unit.ut)) {

            this.announceGuildWin();
            return;
        }

        var TotalHonor = 0;
        var ItemPrize = [];
        var Attacker = {};
        var idPlayers = {
            [Elkaisar.Config.BATTEL_SIDE_ATT]: [],
            [Elkaisar.Config.BATTEL_SIDE_DEF]: []
        };
        for (var iii in this.Battel.Players) {
            var Player = this.Battel.Players[iii];
            if (this.Battel.Battel.id_player === Player.idPlayer)
                Attacker = {
                    name: Player.Player.PlayerName,
                    GuildName: Player.Player.GuildName,
                    id_player: Player.idPlayer,
                    ItemPrize: Player.ItemPrize,
                    Honor: Player.Honor
                };
            else
                idPlayers[this.Battel.Players[iii].side].push({
                    name: Player.Player.PlayerName,
                    GuildName: Player.Player.GuildName,
                    idPlayer: Player.idPlayer,
                    ItemPrize: Player.ItemPrize,
                    Honor: Player.Honor
                });

            if (idPlayers[Elkaisar.Config.BATTEL_SIDE_ATT].length >= 4)
                break;

        }

        Elkaisar.WsLib.ServerAnnounce.BattelWin(null, {
            "Attacker": Attacker,
            "Joiners": idPlayers[Elkaisar.Config.BATTEL_SIDE_ATT],
            "Defender": idPlayers[Elkaisar.Config.BATTEL_SIDE_DEF],
            "EnemyName": "بطل النظام",
            "WinPrize": Attacker.ItemPrize,
            "honor": Attacker.Honor,
            "WorldUnit": {x: Unit.x, y: Unit.y, l: Unit.l, ut: Unit.ut, t: Unit.t}
        });
    }

    announceGuildWin()
    {
        var This = this;
        Elkaisar.DB.SelectFrom(
                "guild_member.id_guild, guild_member.id_player, guild.slog_top, guild.slog_cnt, guild.slog_btm, guild.name AS GuildName, player.name AS PlayerName",
                "guild_member JOIN guild ON guild.id_guild = guild_member.id_guild JOIN player ON player.id_player = guild_member.id_player",
                "guild_member.id_player = ?", [This.Battel.Battel.id_player], function (PlayerGuild) {
            Elkaisar.WsLib.ServerAnnounce.BattelGuildWin(null, {
                Guild: PlayerGuild[0],
                Battel: This.Battel.Battel
            });
        });


    }
}

module.exports = LAfterFight;