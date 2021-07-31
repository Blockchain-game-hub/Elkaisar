class CPSendPrize {

    Parm;
    idPlayer;
    constructor(Url) {
        this.Parm = Url;
    }


    async sendPrizeToGuild() {
        if (this.Parm.DistBy == "Manually") {
            return this.sendManuallyDistForGuild();
        } else {
            return this.sendEquallyDistForGuild();
        }


    }

    async sendEquallyDistForGuild() {

        const idGuild = Elkaisar.Base.validateId(this.Parm.idGuild);
        const Prizes = Elkaisar.Base.validateJson(this.Parm.Prizes);

        const PrizeList = JSON.parse(Prizes);
        const GuildMember = await Elkaisar.DB.ASelectFrom("*", "guild_member", "id_guild = ?", [idGuild]);
        const Guild = await Elkaisar.DB.ASelectFrom("*", "guild", "id_guild = ?", [idGuild]);

        const JsonStrMsg = JSON.stringify({
            classPath: "Guild.PrizeSent",
            GuildName : Guild[0].name
        });
        GuildMember.forEach(function (Player, PlayerIndex) {
            PrizeList.forEach(function (Prize, Index) {
                if (Prize.type == "matrial")
                    Elkaisar.Lib.LItem.addItem(Player.id_player, Prize.matrial, Prize.amount);
                else if (Prize.type == "equip") {
                    Elkaisar.Lib.LItem.addEquip(Player.id_player, Prize.idEquip, Prize.amount)
                }
            });

            Elkaisar.Base.broadcast(JsonStrMsg)

        });


    }
    async sendManuallyDistForGuild() {
        const idGuild = Elkaisar.Base.validateId(this.Parm.idGuild);
        const Prizes = Elkaisar.Base.validateJson(this.Parm.Prizes);

        const PrizeList = JSON.parse(Prizes);
        const GuildMember = await Elkaisar.DB.ASelectFrom("*", "guild_member", "id_guild = ?", [idGuild]);
        const Guild = await Elkaisar.DB.ASelectFrom("*", "guild", "id_guild = ?", [idGuild]);

        const JsonStrMsg = JSON.stringify({
            classPath: "Guild.PrizeSent",
            GuildName : Guild[0].name
        });
        GuildMember.forEach(function (Player, PlayerIndex) {
            PrizeList.forEach(function (Prize, Index) {

                if (Math.floor(Prize.amount * Player.prize_share / 100) <= 0)
                    return;
                if (Prize.type == "matrial")
                    Elkaisar.Lib.LItem.addItem(Player.id_player, Prize.matrial, Math.floor(Prize.amount * Player.prize_share / 100));
                else if (Prize.type == "equip") {
                    Elkaisar.Lib.LItem.addEquip(Player.id_player, Prize.idEquip, Math.floor(Prize.amount * Player.prize_share / 100))
                }
            });

            Elkaisar.Base.broadcast(JsonStrMsg)

        });


    }

    async searchByGuildName() {

        return await Elkaisar.DB.ASelectFrom("*", "guild", `name LIKE '%${this.Parm.seg}%' ORDER BY prestige DESC`);

    }

}

module.exports = CPSendPrize;