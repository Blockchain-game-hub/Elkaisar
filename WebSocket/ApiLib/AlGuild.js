class ALGuild {

    static async addPlayer(idGuild, idPlayer, Rank) {

        const Player = await Elkaisar.DB.AInsert(
                "id_guild = ?, id_player = ?, time_join = ?, rank = ?",
                "guild_member", [idGuild, idPlayer, Date.now() / 1000, Rank]
                );
        Elkaisar.DB.Update("id_guild = ?, guild = (SELECT name FROM guild WHERE id_guild = ?)", "player", "id_player = ?", [idGuild, idGuild, idPlayer]);
        Elkaisar.DB.Update("mem_num = (SELECT COUNT(*) FROM guild_member WHERE id_guild = ?)", "guild", "id_guild = ?", [idGuild, idGuild]);
        Elkaisar.DB.Delete("guild_req", "id_player = ?", [idPlayer]);
        Elkaisar.DB.Delete("guild_inv", "id_player = ?", [idPlayer]);
        return Player;
    }
    
    static async RefreshPlayerGuild(){
        await Elkaisar.DB.Update("guild = NULL, id_guild = NULL", "player", "1");
        
        const PlayerGuild = await Elkaisar.DB.ASelectFrom("guild_member.*, guild.name", "guild_member JOIN guild ON guild.id_guild  = guild_member.id_guild", "1");
        PlayerGuild.forEach(function (Player){
           Elkaisar.DB.Update("id_guild = ?, guild = ?", "player", "id_player = ?", [Player.id_guild, Player.name, Player.id_player]); 
        });
    }
    
    static async inSameGuild(idPlayer, idPlayer2) {
        const idGuild1 = await Elkaisar.DB.ASelectFrom("id_guild", "guild_member", "id_player = ?", [idPlayer]);
        if (!idGuild1.length) return false;
        const idGuild2 = await Elkaisar.DB.ASelectFrom("id_guild", "guild_member", "id_player = ?", [idPlayer2]);
        if (!idGuild2.length) return false;
        if(idGuild1[0]["id_guild"] !=  idGuild2[0]["id_guild"]) return false;
        return true;
    }
    
    static async canDefenceGuildWar(idPlayer, Battel)
    {
        
        if(Elkaisar.Lib.LWorldUnit.isRepelCastle(Battel["ut"]) || Elkaisar.Lib.LWorldUnit.isQueenCity(Battel["ut"]))
        {
            const PlayerGuild = await Elkaisar.DB.ASelectFrom("id_guild", "guild_member", "id_player = ?", [idPlayer]);
            if(!PlayerGuild.length)
                return false;
            
            const GuildDominant = await Elkaisar.DB.ASelectFrom("*", "world_unit_rank", "x = ? AND y = ? ORDER BY id_round DESC LIMIT 1", [Battel["x"],Battel["y"]]);
            
            if(!GuildDominant.length)
                return false;
            return GuildDominant[0]["id_guild"] == PlayerGuild[0]["id_guild"];
        }
        
        return true;
    }

}

ALGuild.RefreshPlayerGuild();





module.exports = ALGuild;