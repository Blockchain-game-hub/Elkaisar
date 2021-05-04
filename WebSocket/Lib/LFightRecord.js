class LFightRecord {

    Record = {
        Rounds: [],
        Heros: [],
        Players: {}
    };
    Fight;
    currentRound = 0;
    constructor(Fight) {
        this.Fight = Fight;
    }

    addRound(Heros) {
        this.Record.Rounds.push({
            Heros: {},
            Attacks: []
        });

        this.currentRound = this.Record.Rounds.length;


        return  this.Record.Rounds[this.currentRound - 1];

    }

    addHeroToRound(Hero, Blocks, HeroIndex) {


        this.Record.Rounds[this.currentRound - 1].Heros[HeroIndex] = {
            idHero: Hero.idHero,
            HeroIndex: HeroIndex,
            side: Hero.side,
            ArmyBlocks: Blocks
        };

    }

    addAttack(Attack) {
        this.Record.Rounds[this.currentRound - 1].Attacks.push(Attack);
    }

    addHero(Hero) {

        this.Record.Heros.push({
            idHero: Hero.idHero,
            Hero: {
                idHero: Hero.Hero.id_hero,
                idCity: Hero.Hero.id_city,
                idPlayer: Hero.Hero.id_player,
                avatar: Hero.Hero.avatar,
                CityName: Hero.Hero.CityName,
                HeroName: Hero.Hero.HeroName,
                gainXp: Hero.gainXp,
                side: Hero.side
            },
            type: Hero.type,
            pre: Hero.pre,
            post: Hero.post
        });
    }

    addPlayer(Player) {

        this.Record.Players[Player.idPlayer] = {
            ItemPrize: Player.ItemPrize,
            ResourcePrize: Player.ResourcePrize,
            Player: Player.Player
        };
    }

    saveRecord(Battel) {
        var This = this;

        setTimeout(function () {
            Elkaisar.ZLib.gzip(JSON.stringify(This.Record.Rounds), function (Err, Rounds) {
                Elkaisar.ZLib.gzip(JSON.stringify(This.Record.Players), function (Err, Players) {
                    Elkaisar.ZLib.gzip(JSON.stringify(This.Record.Heros), function (Err, Heros) {
                        Elkaisar.ZLib.gzip(JSON.stringify(Battel), function (Err, Battel) {
                            Elkaisar.MysqlBattelReplay.getConnection(function (err, connection) {
                                if (err)
                                    throw err;
                                connection.query(`INSERT IGNORE INTO battel_replay SET id_battel_char = ?, battel_replay = ?, id_server = ?, battel_report_players = ?, battel_report_heros = ?, battel_report_data = ?`,
                                        [
                                            This.Fight.FightReplayId,
                                            Rounds.toString("base64"),
                                            Elkaisar.CONST.SERVER_ID,
                                            Players.toString("base64"),
                                            Heros.toString("base64"),
                                            Battel.toString("base64")
                                        ], function (err, result) {
                                    if (err)
                                        throw err;
                                    connection.release();
                                });
                            });
                        });
                    });
                });

            });

        }, 1000);

    }

    saveAllPlayers(Battel) {
        for (var ii in Battel.Players) {
            this.addPlayer(Battel.Players[ii]);
        }
    }
}


module.exports = LFightRecord;