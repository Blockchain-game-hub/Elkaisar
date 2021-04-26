class LFightRecord {

    Record = {
        Rounds: [],
        Heros: [],
        Players: []
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
            HeroIndex:HeroIndex,
            side: Hero.side,
            type: Hero.type,
            ArmyBlocks: Blocks
        };

    }

    addAttack(Attack) {
        this.Record.Rounds[this.currentRound - 1].Attacks.push(Attack);
    }

    addHero(Hero) {
        this.Record.Heros.push(Hero);
    }

    addPlayer(Player) {
        this.Record.Rounds[this.currentRound - 1].Players.push(Player);
    }

    saveRecord() {
        var This = this;
        
        setTimeout(function () {
            Elkaisar.MysqlBattelReplay.getConnection(function (err, connection) {
                if (err)
                    throw err;
                connection.query(`INSERT IGNORE INTO battel_replay SET id_battel_char = ?, battel_replay = ?, id_server = ?`,
                        [This.Fight.FightReplayId, JSON.stringify(This.Record), Elkaisar.CONST.SERVER_ID], function (err, result) {
                    if (err)
                        throw err;
                    console.log("Test Is Ok")
                    connection.release();
                });
            });
        }, 1000);

    }
}


module.exports = LFightRecord;