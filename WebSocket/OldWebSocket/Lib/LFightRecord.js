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

    addRound(Attack, Defence) {
        this.currentRound = this.Record.Rounds.push({
            Heros: [],
            Attacks: []
        });

        this.Record.Rounds[this.currentRound - 1].Heros.push(...Attack);
        this.Record.Rounds[this.currentRound - 1].Heros.push(...Defence);

        return  this.Record.Rounds[this.currentRound - 1];

    }

    addAttack(Attack) {
        this.Record.Rounds[this.currentRound - 1].Attacks.push(Attack);
    }

    addHero(Hero) {
        this.Record.Rounds[this.currentRound - 1].Heros.push(Hero);
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
                connection.query(`INSERT IGNORE INTO battel_replay SET id_battel = ?, battel_replay = ?, id_server = ?`,
                        [Elkaisar.Lib.LBase.MakeStringId(32), JSON.stringify(This.Record), Elkaisar.CONST.SERVER_ID], function (err, result) {
                    if (err)
                        throw err;
                    connection.release();
                });
            });
        }, 1000);

    }
}


module.exports = LFightRecord;