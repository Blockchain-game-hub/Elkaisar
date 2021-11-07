
Elkaisar.Helper.CloseQueenCity = async function (UnitType) {
    var Unites = await Elkaisar.DB.ASelectFrom("*", "world", `ut = ${UnitType}`, []);
    var Unit = Unites[0];
    if (Unit.lo == 1)
        return;
    Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y],
            function () {
                Elkaisar.World.refreshWorldUnit();
            });
    await Elkaisar.DB.AUpdate(`duration = ${Math.floor(Date.now() / 1000)} - time_stamp`, "world_unit_rank", `x = ${Unit.x} AND y = ${Unit.y} ORDER BY id_round DESC LIMIT 1`);
    var Guild = await Elkaisar.DB.ASelectFrom(`guild.name AS GuildName, SUM(duration) AS TotalD, guild.id_guild`,
            "guild JOIN world_unit_rank ON world_unit_rank.id_dominant = guild.id_guild",
            "x = ? AND y = ? GROUP BY id_dominant ORDER BY TotalD DESC LIMIT 1 ", [Unit.x, Unit.y]);
    Elkaisar.Base.broadcast(JSON.stringify({
        "classPath": "ServerAnnounce.QueenCityClosed",
        "WorldUnit": Unit,
        "WinnerGuild": Guild && Guild[0] ? Guild[0] : {}
    }));

    if (!Guild[0])
        return;

    var PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_sp", "unitType = ?", [Unit.ut]);
    var GuildMember = await Elkaisar.DB.ASelectFrom("id_player", "guild_member", "id_guild = ?", [Guild[0].id_guild])


    GuildMember.forEach(function (Member, Index) {
        var PlayerList = []
        PrizeList.forEach(function (Prize, Index) {
            var Luck = Math.floor(Math.random() * 1000);
            var amount = 0;
            if (Luck <= Prize["win_rate"]) {
                amount = Elkaisar.Base.rand(Prize["amount_min"], Prize["amount_max"]);
                Elkaisar.DB.Update(`amount = amount + ${amount}`, "player_item", "id_player = ? AND id_item = ?", [Member.id_player, Prize.prize]);
                PlayerList.push({
                    "Item": Prize["prize"],
                    "amount": amount
                });
            }
        });

        var List = ``;
        for (var iii in PlayerList) {
            var Item = Elkaisar.Lib.LItem.ItemList[PlayerList[iii].Item];
            if (!Item) {
                continue;
            }

            List += `<li style="width: 20%;">
                                                <div class="image"><img src="${Item.image}"></div>
                                                <div class="amount stroke">${PlayerList[iii].amount} X</div>
                                            </li>`;
        }
        //insertIntoTable("id_to = {$cityFrom["id_player"]} , head = 'تقرير وصول الموارد'  , body = '$body' , time_stamp = $now ", "msg_diff");
        Elkaisar.DB.Insert(
                `id_to = ${Member.id_player}, head = 'تقرير استلام جوائز الملكة', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
                [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);


        var playerTo = Elkaisar.Base.getPlayer(Member.id_player);
        if (playerTo)
            playerTo.connection.sendUTF(JSON.stringify({
                "classPath": "Chat.QueenPrizeSent",
                "xCoord": Unit.x,
                "yCoord": Unit.y
            }));

    });




}


Elkaisar.Cron.schedule(`0 0 * * 1`, function () {

    Elkaisar.Helper.CloseQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_A);
    Elkaisar.Helper.CloseQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_B);
    Elkaisar.Helper.CloseQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_C);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Helper.OpenQueenCity = async function (unitType) {
    Elkaisar.DB.SelectFrom("*", "world", `ut = ${unitType}`, [], function (Unites) {
        Unites.forEach(function (Unit, Index) {
            Elkaisar.DB.Update("lo = 0, l = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y],
                    function () {
                        Elkaisar.World.refreshWorldUnit();
                    });
            Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.Base.broadcast(JSON.stringify({
                "classPath": "ServerAnnounce.QueenCityOpened",
                "WorldUnit": Unit
            }));
        });
    });
};

Elkaisar.Cron.schedule(`0 0 * * 2`, function () {

    Elkaisar.Helper.OpenQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_A);
    Elkaisar.Helper.OpenQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_B);
    Elkaisar.Helper.OpenQueenCity(Elkaisar.Config.WUT_QUEEN_CITY_C);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Helper.OpenRepleCastle = async function (unitType) {

    var Units = await Elkaisar.DB.ASelectFrom("*", "world", `ut = ${unitType}`, []);
    var Unit = Units[0];
    Elkaisar.DB.SelectFrom(
            "guild.name AS GuildName, guild.id_guild, world_attack_queue.id",
            "guild JOIN world_attack_queue ON world_attack_queue.id_guild = guild.id_guild",
            `world_attack_queue.x_coord = ${Unit.x} AND world_attack_queue.y_coord = ${Unit.y} ORDER BY id ASC LIMIT 1`, [], function (Guild) {

        if (!Guild || !Guild[0])
            return;
        Elkaisar.DB.Update("lo = 0", "world", "x = ? AND y = ?", [Unit.x, Unit.y], function () {
            Elkaisar.World.refreshWorldUnit();
        });

        Elkaisar.DB.Update("time_start = ?, time_end = ?", "world_attack_queue", "id = ?", [Math.floor(Date.now() / 1000), Math.floor(Date.now() / 1000) + 3600, Guild[0]["id"]]);
        Elkaisar.DB.SelectFrom(
                "guild.name AS GuildName, guild.id_guild",
                "guild JOIN world_unit_rank ON world_unit_rank.id_dominant = guild.id_guild",
                `world_unit_rank.x = ${Unit.x} AND world_unit_rank.y = ${Unit.y} ORDER BY id_round DESC LIMIT 1`, [], function (GuildDef) {
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.RepleCastleOpened",
                WorldUnit: Unit,
                GuildAtt: Guild[0],
                GuildDef: GuildDef && GuildDef[0] ? GuildDef[0] : null
            }));
        });
    });

};


/* Open Reple castle*/

Elkaisar.Cron.schedule("0 12 * * *", function () {

    Elkaisar.Helper.OpenRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_A);
    Elkaisar.Helper.OpenRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_B);
    Elkaisar.Helper.OpenRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_C);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Helper.CloseRepleCastle = async function (unitType) {
    Elkaisar.DB.SelectFrom("*", "world", `ut = ${unitType}`, [], function (Units) {

        Units.forEach(function (Unit, Index) {
            Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y], function () {
                Elkaisar.World.refreshWorldUnit();
            });
            Elkaisar.DB.Delete("world_attack_queue", "time_start < ? AND x_coord = ? AND y_coord = ?", [Date.now() / 1000, Unit.x, Unit.y]);
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.RepleCastleClosed",
                WorldUnit: Unit
            }));
        });

    });
};


Elkaisar.Cron.schedule("0 13 * * *", function () {

    Elkaisar.Helper.CloseRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_A);
    Elkaisar.Helper.CloseRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_B);
    Elkaisar.Helper.CloseRepleCastle(Elkaisar.Config.WUT_REPLE_CASTLE_C);


}, {
    scheduled: true,
    timezone: "Etc/UTC"
});




Elkaisar.Helper.CloseArmyCapital = async function (UnitType) {

    var Units = await Elkaisar.DB.ASelectFrom("*", "world", `ut = ${UnitType}`, []);
    var Unit = Units[0];
    if (Unit.lo == 1)
        return;
    Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y], function () {
        Elkaisar.World.refreshWorldUnit();
    });
    await Elkaisar.DB.AUpdate("duration = ? - time_stamp", "world_unit_rank", `x = ${Unit.x} AND y = ${Unit.y} ORDER BY id_round DESC LIMIT 1`, [Math.floor(Date.now() / 1000)]);
    var Players = await Elkaisar.DB.ASelectFrom(
            `world_unit_rank.id_dominant, SUM(world_unit_rank.duration) AS d_sum, SUM(world_unit_rank.win_num) AS w_num, player.name, player.id_player  , player.guild`,
            `world_unit_rank JOIN player ON player.id_player = world_unit_rank.id_dominant`,
            `world_unit_rank.x = ${Unit["x"]}  AND  world_unit_rank.y = ${Unit["y"]}  GROUP BY world_unit_rank.id_dominant ORDER BY d_sum DESC LIMIT 5`, []);

    if (!Players)
        return;
    Players.forEach(async function (Player, Index) {

        var PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_sp", `unitType = ${Unit.ut} AND lvl = ${Index + 1}`, []);
        var List = ``;
        PrizeList.forEach(function (Prize) {
            var Luck = Math.floor(Math.random() * 1000);
            var amount = 0;
            if (Luck <= Prize["win_rate"]) {
                amount = Elkaisar.Base.rand(Prize["amount_min"], Prize["amount_max"]);
                Elkaisar.DB.Update(`amount = amount + ${amount}`, "player_item", "id_player = ? AND id_item = ?", [Player.id_player, Prize.prize]);

                var Item = Elkaisar.Lib.LItem.ItemList[Prize["prize"]];
                List += `<li style="width: 20%;">
                                    <div class="image"><img src="${Item.image}"></div>
                                    <div class="amount stroke">${amount} X</div>
                                </li>`;
            }
        });

        Elkaisar.DB.Insert(
                `id_to = ${Player.id_player}, head = 'تقرير استلام جوائز العواصم', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
                [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);
        var playerTo = Elkaisar.Base.getPlayer(Player.id_player);
        if (playerTo)
            playerTo.connection.sendUTF(JSON.stringify({
                "classPath": "Chat.PrizeSent",
                "xCoord": Unit.x,
                "yCoord": Unit.y
            }));
    });


    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.capitalLock",
        Player: Players[0],
        WorldUnit: Unit
    }));

};

/*close Army Capital*/
Elkaisar.Cron.schedule("0 19 * * 1", function () {

    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_A);
    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_B);
    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_C);
    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_D);
    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_E);
    Elkaisar.Helper.CloseArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_F);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Helper.OpenArmyCapital = async function (unitType) {
    Elkaisar.DB.SelectFrom("*", "world", `ut = ${unitType} `, [], function (Units) {

        Units.forEach(function (Unit, Index) {

            Elkaisar.DB.Update("lo = 0", "world", "x = ? AND y = ?", [Unit.x, Unit.y], function () {
                Elkaisar.World.refreshWorldUnit();
            });
            Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [Unit.x, Unit.y]);

            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.capitalUnLock",
                WorldUnit: Unit
            }));


        });

    });
};



/* Open Army Capital*/
Elkaisar.Cron.schedule("30 17 * * 1", function () {

    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_A);
    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_B);
    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_C);
    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_D);
    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_E);
    Elkaisar.Helper.OpenArmyCapital(Elkaisar.Config.WUT_ARMY_CAPITAL_F);

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});







Elkaisar.Helper.CloseArenaChallange = async function () {

    Elkaisar.DB.SelectFrom(
            "arena_player_challange.*, player.name",
            "arena_player_challange JOIN player ON player.id_player = arena_player_challange.id_player",
            "arena_player_challange.rank = 1", [], function (Player) {

        Elkaisar.DB.SelectFrom("*", "world_unit_prize_sp", "unitType = ?", [Elkaisar.Config.WUT_CHALLAGE_FIELD_PLAYER], function (PrizeList) {
            var List = ``;
            PrizeList.forEach(function (Prize) {
                var Luck = Math.floor(Math.random() * 1000);
                var amount = 0;
                if (Luck <= Prize["win_rate"]) {
                    amount = Elkaisar.Base.rand(Prize["amount_min"], Prize["amount_max"]);
                    Elkaisar.DB.Update(`amount = amount + ${amount}`, "player_item", "id_player = ? AND id_item = ?", [Player[0].id_player, Prize.prize]);

                    var Item = Elkaisar.Lib.LItem.ItemList[Prize["prize"]];
                    List += `<li style="width: 20%;">
                                <div class="image"><img src="${Item.image}"></div>
                                <div class="amount stroke">${amount} X</div>
                            </li>`;
                }
            });

            Elkaisar.DB.Insert(
                    `id_to = ${Player[0].id_player}, head = 'تقرير استلام جوائز ميدان التحدى', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
                    [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);
            Elkaisar.DB.Update("champion = champion + 1", "arena_player_challange", `id_player = ${Player[0].id_player}`);
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.ArenaChallangeRoundEnd",
                PlayerName: Player[0].name
            }));
        });
    });
};


Elkaisar.Helper.CloseArenaChallangeTeam = async function () {

    const Team = await Elkaisar.DB.ASelectFrom("arena_team_challange.*, team.name",
            "arena_team_challange JOIN team ON team.id_team = arena_team_challange.id_team",
            "arena_team_challange.rank = 1", []);
    
    if (!Team.length)
        return;

    const PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_sp", "unitType = ?", [Elkaisar.Config.WUT_CHALLAGE_FIELD_TEAM]);
    const PlayerTeam = await Elkaisar.DB.ASelectFrom("DISTINCT id_player", "arena_team_challange_hero", "id_team = ?", [Team[0].id_team]);
    
    
    
    PlayerTeam.forEach(function (Player) {
        var List = ``;
        PrizeList.forEach(function (Prize) {
            var Luck = Math.floor(Math.random() * 1000);
            var amount = 0;
            if (Luck <= Prize["win_rate"]) {
                amount = Elkaisar.Base.rand(Prize["amount_min"], Prize["amount_max"]);
                Elkaisar.DB.Update(`amount = amount + ${amount}`, "player_item", "id_player = ? AND id_item = ?", [Player.id_player, Prize.prize]);

                var Item = Elkaisar.Lib.LItem.ItemList[Prize["prize"]];
                List += `<li style="width: 20%;">
                        <div class="image"><img src="${Item.image}"></div>
                        <div class="amount stroke">${amount} X</div>
                    </li>`;
            }
        });

        Elkaisar.DB.Insert(
                `id_to = ${Player.id_player}, head = 'تقرير استلام جوائز ميدان تحدى الفريق', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
                [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);
        
    });
    Elkaisar.DB.Update("champion = champion + 1", "arena_team_challange", `id_team = ${Team[0].id_team}`);

    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.ArenaChallangeTeamRoundEnd",
        TeamName: Team[0].name
    }));
};


Elkaisar.Helper.CloseArenaChallangeGuild = async function () {

    const Guild = await Elkaisar.DB.ASelectFrom("arena_guild_challange.*, guild.name",
            "arena_guild_challange JOIN guild ON guild.id_guild = arena_guild_challange.id_guild",
            "arena_guild_challange.rank = 1", []);

    if (!Guild.length)
        return;

    const PrizeList = await Elkaisar.DB.ASelectFrom("*", "world_unit_prize_sp", "unitType = ?", [Elkaisar.Config.WUT_CHALLAGE_FIELD_GUILD]);
    const PlayerGuild = await Elkaisar.DB.ASelectFrom("DISTINCT id_player", "arena_guild_challange_hero", "id_guild = ?", [Guild[0].id_guild]);
    PlayerGuild.forEach(function (Player) {
        var List = ``;
        PrizeList.forEach(function (Prize) {
            var Luck = Math.floor(Math.random() * 1000);
            var amount = 0;
            if (Luck <= Prize["win_rate"]) {
                amount = Elkaisar.Base.rand(Prize["amount_min"], Prize["amount_max"]);
                Elkaisar.DB.Update(`amount = amount + ${amount}`, "player_item", "id_player = ? AND id_item = ?", [Player.id_player, Prize.prize]);

                var Item = Elkaisar.Lib.LItem.ItemList[Prize["prize"]];
                List += `<li style="width: 20%;">
                        <div class="image"><img src="${Item.image}"></div>
                        <div class="amount stroke">${amount} X</div>
                    </li>`;
            }
        });

        Elkaisar.DB.Insert(
                `id_to = ${Player.id_player}, head = 'تقرير استلام جوائز ميدان تحدى الأحلاف', body=?, time_stamp = ${Math.floor(Date.now() / 1000)}`, "msg_diff",
                [`<div id="matrial-box-gift" style="border: none; background: none"><ul class="matrial-list">${List}</ul></div>`]);
        
    });
    
    Elkaisar.DB.Update("champion = champion + 1", "arena_guild_challange", `id_guild = ${Guild[0].id_guild}`);

    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.ArenaChallangeGuildRoundEnd",
        GuildName: Guild[0].name
    }));
};

/*   Arena challange */
Elkaisar.Cron.schedule("59 19 * * *", function () {
    Elkaisar.Helper.CloseArenaChallange();
    Elkaisar.Helper.CloseArenaChallangeTeam();
    Elkaisar.Helper.CloseArenaChallangeGuild();
});



/*   Arena challange */
Elkaisar.Cron.schedule("0 16 * * 0,1,2,3,4,6", function () {
    Elkaisar.DB.Update("lo = 0", "world",
            `ut IN (${Elkaisar.Config.WUT_SEA_CITY_1}, ${Elkaisar.Config.WUT_SEA_CITY_2}, ${Elkaisar.Config.WUT_SEA_CITY_3}, ${Elkaisar.Config.WUT_SEA_CITY_4})`,
            [], function () {
        Elkaisar.World.refreshWorldUnit();
    });
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityOppend"
    }));
});

Elkaisar.Cron.schedule("0 17 * * 0,1,2,3,4,6", function () {
    Elkaisar.DB.Update("lo = 1", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_1}, ${Elkaisar.Config.WUT_SEA_CITY_2}, ${Elkaisar.Config.WUT_SEA_CITY_3}, ${Elkaisar.Config.WUT_SEA_CITY_4})`,
            [], function () {
        Elkaisar.World.refreshWorldUnit();
    });
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityClosed"
    }));
});



/*   Arena challange */
Elkaisar.Cron.schedule("0 16 * * 5", function () {
    Elkaisar.DB.Update("lo = 0", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_5})`,
            [], function () {
        Elkaisar.World.refreshWorldUnit();
    });
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityCoinOppend"
    }));
});

Elkaisar.Cron.schedule("0 17 * * 5", function () {
    Elkaisar.DB.Update("lo = 1", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_5})`,
            [], function () {
        Elkaisar.World.refreshWorldUnit();
    });
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityCoinClosed"
    }));
});


