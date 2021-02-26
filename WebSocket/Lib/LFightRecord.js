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
        
        this.currentRound = this.Record.Rounds.length;
        for(var HeroAtt of Attack){
            this.Record.Rounds[this.currentRound - 1].Heros.push({
                idHero : HeroAtt.id_hero,
                side   : HeroAtt.side,
                type   : HeroAtt.type,
                pre    : HeroAtt.pre,
                post   : HeroAtt.post
            });
            
        };
        
        for(var HeroDef of Defence){
            this.Record.Rounds[this.currentRound - 1].Heros.push({
                idHero : HeroDef.id_hero,
                side   : HeroDef.side,
                type   : HeroDef.type,
                pre    : HeroDef.pre,
                post   : HeroDef.post
            });
            
        };
        
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
                connection.query(`INSERT IGNORE INTO battel_replay SET id_battel_char = ?, battel_replay = ?, id_server = ?`,
                        [This.Fight.FightReplayId, JSON.stringify(This.Record, null, 4), Elkaisar.CONST.SERVER_ID], function (err, result) {
                    if (err)
                        throw err;
                    connection.release();
                });
            });
        }, 1000);

    }
}


module.exports = LFightRecord;