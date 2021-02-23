



Elkaisar.Cron.schedule(`0 0 * * 1`, function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN (${Elkaisar.Config.WUT_QUEEN_CITY_A}, ${Elkaisar.Config.WUT_QUEEN_CITY_B}, ${Elkaisar.Config.WUT_QUEEN_CITY_C })`, [], function (Unites) {
        Unites.forEach(function (Unit, Index) {
            if (Unit.lo == 1)
                return;
            Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Update("duration = ? - time_stamp", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Math.floor(Date.now() / 1000), Unit.x, Unit.y], function () {
                Elkaisar.DB.SelectFrom(
                        `guild.name AS GuildName, SUM(duration) AS TotalD, guild.id_guild`,
                        "guild JOIN world_unit_rank ON world_unit_rank.id_dominant = guild.id_guild",
                        "x = ? AND y = ? GROUP BY id_dominant ORDER BY TotalD DESC LIMIT 1 ", [Unit.x, Unit.y], function (Guild) {

                    Elkaisar.Base.broadcast(JSON.stringify({
                        "classPath": "ServerAnnounce.QueenCityClosed",
                        "WorldUnit": Unit,
                        "WinnerGuild": Guild && Guild[0] ? Guild[0] : {}
                    }));
                    if (!Guild[0])
                        return;

                    Elkaisar.DB.SelectFrom("*", "world_unit_prize_sp", "unitType = ?", [Unit.ut], function (PrizeList) {
                        Elkaisar.DB.SelectFrom("id_player", "guild_member", "id_guild = ?", [Guild[0].id_guild], function (GuildMember) {
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
                                        console.log("Error AdingItem For Prize", Item);
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
                        });
                    });

                });
            });
        });
    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Cron.schedule(`0 0 * * 2`, function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN (${Elkaisar.Config.WUT_QUEEN_CITY_A}, ${Elkaisar.Config.WUT_QUEEN_CITY_B}, ${Elkaisar.Config.WUT_QUEEN_CITY_C })`, [], function (Unites) {
        Unites.forEach(function (Unit, Index) {
            Elkaisar.DB.Update("lo = 0, l = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.Base.broadcast(JSON.stringify({
                "classPath": "ServerAnnounce.QueenCityOpened",
                "WorldUnit": Unit
            }));
        });
    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});





/* Open Reple castle*/

Elkaisar.Cron.schedule("0 12 * * *", function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN(${Elkaisar.Config.WUT_REPLE_CASTLE_A}, ${Elkaisar.Config.WUT_REPLE_CASTLE_B}, ${Elkaisar.Config.WUT_REPLE_CASTLE_C})`, [], function (Units) {

        Units.forEach(function (Unit, Index) {

            Elkaisar.DB.SelectFrom(
                    "guild.name AS GuildName, guild.id_guild, world_attack_queue.id",
                    "guild JOIN world_attack_queue ON world_attack_queue.id_guild = guild.id_guild",
                    `world_attack_queue.x_coord = ${Unit.x} AND world_attack_queue.y_coord = ${Unit.y} ORDER BY id ASC LIMIT 1`, [], function (Guild) {


                if (!Guild || !Guild[0])
                    return;
                Elkaisar.DB.Update("lo = 0", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);
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

        });

    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule("0 13 * * *", function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN(${Elkaisar.Config.WUT_REPLE_CASTLE_A}, ${Elkaisar.Config.WUT_REPLE_CASTLE_B}, ${Elkaisar.Config.WUT_REPLE_CASTLE_C})`, [], function (Units) {

        Units.forEach(function (Unit, Index) {
            Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Delete("world_attack_queue", "time_start < ? AND x_coord = ? AND y_coord = ?", [Date.now() / 1000, Unit.x, Unit.y]);
            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.RepleCastleClosed",
                WorldUnit: Unit
            }));
        });

    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});






/* Open Army Capital*/
Elkaisar.Cron.schedule("30 17 * * 1", function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN(${Elkaisar.Config.WUT_ARMY_CAPITAL_A}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_B}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_C}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_D}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_E}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_F})`, [], function (Units) {

        Units.forEach(function (Unit, Index) {

            Elkaisar.DB.Update("lo = 0", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);
            Elkaisar.DB.Delete("world_unit_rank", "x = ? AND y = ?", [Unit.x, Unit.y]);

            Elkaisar.Base.broadcast(JSON.stringify({
                classPath: "ServerAnnounce.capitalUnLock",
                WorldUnit: Unit
            }));


        });

    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});

/*close Army Capital*/
Elkaisar.Cron.schedule("0 19 * * 1", function () {

    Elkaisar.DB.SelectFrom("*", "world", `ut IN(${Elkaisar.Config.WUT_ARMY_CAPITAL_A}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_B}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_C}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_D}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_E}, ${Elkaisar.Config.WUT_ARMY_CAPITAL_F})`, [], function (Units) {

        Units.forEach(function (Unit, Index) {

            if (Unit.lo == 1)
                return;
            Elkaisar.DB.Update("lo = 1", "world", "x = ? AND y = ?", [Unit.x, Unit.y]);

            Elkaisar.DB.Update("duration = ? - time_stamp", "world_unit_rank", `x = ${Unit.x} AND y = ${Unit.y} ORDER BY id_round DESC LIMIT 1`, [Math.floor(Date.now() / 1000)], function () {
                Elkaisar.DB.SelectFrom(
                        `world_unit_rank.id_dominant, SUM(world_unit_rank.duration) AS d_sum, SUM(world_unit_rank.win_num) AS w_num, player.name, player.id_player  , player.guild`,
                        `world_unit_rank JOIN player ON player.id_player = world_unit_rank.id_dominant`,
                        `world_unit_rank.x = ${Unit["x"]}  AND  world_unit_rank.y = ${Unit["y"]}  GROUP BY world_unit_rank.id_dominant ORDER BY d_sum DESC LIMIT 5`, [], function (Players, PlayerRank) {
                    if (!Players)
                        return;
                    Players.forEach(function (Player, Index) {
                        Elkaisar.DB.SelectFrom("*", "world_unit_prize_sp", `unitType = ${Unit.ut} AND lvl = ${Index + 1}`, [], function (PrizeList) {
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
                    });


                    Elkaisar.Base.broadcast(JSON.stringify({

                        classPath: "ServerAnnounce.capitalLock",
                        Player: Players[0],
                        WorldUnit: Unit

                    }));


                });
            });

        });

    });

}, {
    scheduled: true,
    timezone: "Etc/UTC"
});




/*   Arena challange */
Elkaisar.Cron.schedule("59 19 * * *", function () {

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

});

/*   Arena challange */
Elkaisar.Cron.schedule("0 16 * * 0,1,2,3,4,6", function () {
    Elkaisar.DB.Update("lo = 0", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_1}, ${Elkaisar.Config.WUT_SEA_CITY_2}, ${Elkaisar.Config.WUT_SEA_CITY_3}, ${Elkaisar.Config.WUT_SEA_CITY_4})`);
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityOppend"
    }));
});

Elkaisar.Cron.schedule("0 17 * * 0,1,2,3,4,6", function () {
    Elkaisar.DB.Update("lo = 1", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_1}, ${Elkaisar.Config.WUT_SEA_CITY_2}, ${Elkaisar.Config.WUT_SEA_CITY_3}, ${Elkaisar.Config.WUT_SEA_CITY_4})`);
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityClosed"
    }));
});



/*   Arena challange */
Elkaisar.Cron.schedule("0 16 * * 5", function () {
    Elkaisar.DB.Update("lo = 0", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_5})`);
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityCoinOppend"
    }));
});

Elkaisar.Cron.schedule("0 17 * * 5", function () {
    Elkaisar.DB.Update("lo = 1", "world", `ut IN (${Elkaisar.Config.WUT_SEA_CITY_5})`);
    Elkaisar.Base.broadcast(JSON.stringify({
        classPath: "ServerAnnounce.SeaCityCoinClosed"
    }));
});


