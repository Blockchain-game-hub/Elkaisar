class ALTeam {

    static async getTeamMamber(idTeam) {

        return await Elkaisar.DB.ASelectFrom(
                "team_member.*, player.name AS PlayerName, player.id_player, player.avatar",
                "team_member JOIN player ON team_member.id_player = player.id_player",
                "team_member.id_team = ? ORDER BY team_member.rank DESC", [idTeam]);
    }
    static async getTeamBaseData(idTeam) {

        return await Elkaisar.DB.ASelectFrom(
                "col.*, player.name As PlayerName, player.avatar, player.id_player",
                `(SELECT team.*, @row:=@row+1 as 'rank' FROM team,(SELECT @row:=0) r ORDER BY team.prestige DESC ) AS col
             JOIN player ON col.id_leader = player.id_player`, `col.id_team = ?`, [idTeam]
                );
    }
    static async getTeamRelation(idTeam) {

        return await Elkaisar.DB.ASelectFrom(
                "team.*, player.name As PlayerName, player.avatar, player.id_player, team_relation.*",
                `team_relation JOIN team ON team_relation.id_team_2 = team.id_team JOIN 
             player ON player.id_player = team.id_leader`, "team_relation.id_team_1 = ?", [idTeam]
                );

    }

    static async getTeamInv(idTeam) {
        return await Elkaisar.DB.ASelectFrom(
                "team_inv.*, player.name As PlayerName, player.avatar, player.id_player",
                "team_inv JOIN player ON team_inv.id_player = player.id_player", "team_inv.id_team = ?", [idTeam]
                );
    }
    static async getTeamReq(idTeam) {
        return await Elkaisar.DB.ASelectFrom(
                "team_req.*, player.name As PlayerName, player.avatar, player.id_player",
                "team_req JOIN player ON team_req.id_player = player.id_player", "team_req.id_team = ?", [idTeam]
                );
    }

    static async addPlayer(idTeam, idPlayer, Rank) {

        const Player = await Elkaisar.DB.AInsert(
                "id_team = ?, id_player = ?, time_join = ?, rank = ?",
                "team_member", [idTeam, idPlayer, Date.now() / 1000, Rank]
                );
        Elkaisar.Lib.ALTeam.refreshTeamData(idTeam);
        Elkaisar.DB.Delete("team_req", "id_player = ?", [idPlayer]);
        Elkaisar.DB.Delete("team_inv", "id_player = ?", [idPlayer]);
        return Player;
    }
    
    static async refreshTeamData(idTeam){
        Elkaisar.DB.Update(
                `mem_num = (SELECT COUNT(*) FROM team_member WHERE id_team = ?),
                 prestige = (SELECT SUM(prestige) FROM player JOIN team_member ON team_member.id_player = player.id_player WHERE team_member.id_team = ?),
                 honor = (SELECT SUM(honor) FROM player JOIN team_member ON team_member.id_player = player.id_player WHERE team_member.id_team = ?)`,
        "team", "id_team = ?", [idTeam, idTeam, idTeam, idTeam]);
    }

}

module.exports = ALTeam;