/* global Elkaisar */

class ATeam {

    Parm;
    idPlayer;
    constructor(idPlayer, Url) {
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }

    async create() {

        const guildName = Elkaisar.Base.validatePlayerWord(this.Parm["guildName"]);
        const slogTop = Elkaisar.Base.validateId(this.Parm["slogTop"]);
        const slogMiddle = Elkaisar.Base.validateId(this.Parm["slogMiddle"]);
        const slogBottom = Elkaisar.Base.validateId(this.Parm["slogBottom"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        const guildWithSameName = await Elkaisar.DB.ASelectFrom("COUNT(*) AS c", "team", "name = ?", [guildName]);
        const PlayerGuildMem = await Elkaisar.DB.ASelectFrom("COUNT(*) AS C", "team_member", "id_player = ?", [this.idPlayer]);

        if (guildName.length > 15)
            return {"state": "error_0"};
        if (guildWithSameName[0]["c"] > 0)
            return {"state": "error_1"};
        if (PlayerGuildMem[0]["C"] > 0)
            return {"state": "error_2"};
        if (!Elkaisar.Lib.ALCity.isResourceTaken(this.idPlayer, idCity, {"coin": 1e5}))
            return {"state": "error_3"};
        const idTeam = await Elkaisar.DB.AInsert(
                "id_leader = ?, name = ?, slog_top = ?, slog_cnt = ?, slog_btm = ?",
                "team", [this.idPlayer, guildName, slogTop, slogMiddle, slogBottom]
                );
        const TeamData = await Elkaisar.DB.ASelectFrom("*", "team", "id_leader = ?", [this.idPlayer]);
        if (!TeamData.length)
            return {"state": "error_4"};
        Elkaisar.Lib.ALTeam.addPlayer(TeamData[0].id_team, this.idPlayer, Elkaisar.Config.TEAM_R_LEADER);
        Elkaisar.DB.Insert("id_team = ?", "team_donation", [TeamData[0].id_team]);
        Elkaisar.DB.Insert("arena_team_challange.id_team = ?, rank = ?", 'arena_team_challange', [TeamData[0].id_team, (await Elkaisar.DB.ASelectFrom('COUNT(*) AS c', 'arena_team_challange', 1))[0]["c"] + 1]);
        return {
            "state": "ok",
            Team: TeamData[0]
        };
    }

    async getPlayerTeam() {

        const idPlayer = Elkaisar.Base.validateId(this.idPlayer);
        const TeamMember = await Elkaisar.DB.ASelectFrom("*", "team_member", "id_player = ?", [idPlayer]);
        const Arr = [];
        if (TeamMember.length == 0)
            return {
                Team: false,
                TeamInv: await Elkaisar.DB.ASelectFrom("*", "team_inv JOIN team ON team.id_team = team_inv.id_team", "team_inv.id_player = ?", [idPlayer]),
                TeamReq: await Elkaisar.DB.ASelectFrom("*", "team_req JOIN team ON team.id_team = team_req.id_team", "team_req.id_player = ?", [idPlayer])
            };
        const idTeam = TeamMember[0].id_team;
        return {
            Team: (await Elkaisar.Lib.ALTeam.getTeamBaseData(idTeam))[0],
            TeamMember: await Elkaisar.Lib.ALTeam.getTeamMamber(idTeam),
            TeamDonation: (await Elkaisar.DB.ASelectFrom("*", "team_donation", "id_team = ?", [idTeam]))[0],
            TeamRelation: await Elkaisar.Lib.ALTeam.getTeamRelation(idTeam),
            TeamInv: await Elkaisar.Lib.ALTeam.getTeamInv(idTeam),
            TeamReq: await Elkaisar.Lib.ALTeam.getTeamReq(idTeam)
        };
    }

    async getTeamRank() {

        const offset = Elkaisar.Base.validateId(this.Parm.offset);
        return Elkaisar.DB.ASelectFrom(
                "team.*, player.name AS PlayerName, player.avatar, player.id_player",
                "team JOIN player ON player.id_player = team.id_leader",
                "1 ORDER BY prestige DESC LIMIT 10"
                );
    }

    async modifyTeamWord() {

        const newWord = Elkaisar.Base.validatePlayerWord(this.Parm["newWord"]);
        const TeamMember = await Elkaisar.DB.ASelectFrom("*", "team_member", "id_player = ?", [this.idPlayer]);

        if (!TeamMember.length)
            return {"state": "error_0"};
        if (TeamMember[0]["rank"] < Elkaisar.Config.TEAM_R_LEADER)
            return {"state": "error_1"};
        if (newWord.length > 512)
            return {"state": "error_2"};

        await Elkaisar.DB.AUpdate("word = ?", "team", "id_team = ?", [newWord, TeamMember[0]["id_team"]]);

        return {
            "state": "ok",
            "Team": (await Elkaisar.Lib.ALTeam.getTeamBaseData(TeamMember[0]["id_team"]))[0]
        };
    }

    async searchTeamByName() {
        const SearchVal = Elkaisar.Base.validatePlayerWord(this.Parm.SearchVal);

        return await Elkaisar.DB.ASelectFrom("*", "team", `name LIKE ? ORDER BY prestige DESC LIMIT 10`, ['%' + SearchVal + '%']);
    }

    async changeTeamRelation() {
        const idTeam = Elkaisar.Base.validateId(this.Parm.idTeam);
        const Relation = Elkaisar.Base.validateId(this.Parm.relation);
        const TeamMember = await Elkaisar.DB.ASelectFrom(
                "team_member.*, player.name AS PlayerName",
                "team_member JOIN player ON player.id_player = team_member.id_player",
                "team_member.id_player = ?", [this.idPlayer]);

        if (!TeamMember.length)
            return {"state": "error_0"};
        if (TeamMember[0]["rank"] < Elkaisar.Config.TEAM_R_LEADER)
            return {"state": "error_1"};
        if (Relation > 2 || Relation < 0)
            return {"state": "error_2"};

        const TeamRelation = await Elkaisar.DB.ASelectFrom("*", "team_relation", "id_team_1 = ? AND id_team_2 = ?", [TeamMember[0].id_team, idTeam]);

        if (!TeamRelation.length)
            Elkaisar.DB.Insert("id_team_1 = ?, id_team_2 = ?, relation = ?", "team_relation", [TeamMember[0].id_team, idTeam, Relation]);
        else
            Elkaisar.DB.Update("relation = ?", "team_relation", "id_team_1 = ? AND id_guild_2 = ?", [Relation, TeamMember[0].id_team, idTeam]);

        Elkaisar.Base.broadcast(JSON.stringify({
            "classPath": "Team.announceRelation",
            "PlayerName": TeamMember[0].PlayerName,
            "TeamNameOne": (await Elkaisar.DB.ASelectFrom("name", "team", "id_team = ?", [TeamMember[0].id_team]))[0].name,
            "TeamNameTwo": (await Elkaisar.DB.ASelectFrom("name", "team", "id_team = ?", [idTeam]))[0].name,
            "idTeamOne": TeamMember[0].id_team,
            "idTeamTwo": idTeam,
            "relation": Relation
        }));

        return {
            state: "ok"
        };
    }

    async showTeamReview() {

        const idTeam = Elkaisar.Base.validateId(this.Parm.idTeam);

        const Team = await Elkaisar.DB.ASelectFrom(
                "col.*, player.name AS LeaderName",
                `(SELECT team.*, @row:=@row+1 as 'rank' FROM team,(SELECT @row:=0) r ORDER BY team.prestige DESC ) AS col 
                        JOIN player ON col.id_leader = player.id_player`,
                "col.id_team = ?", [idTeam]);

        if (Team[0])
            return Team[0];
        else
            return {};

    }

    async playerTeamLeave() {
        const idTeam = Elkaisar.Base.validateId(this.Parm.idTeam);
        const Team = await Elkaisar.DB.ASelectFrom("*", "team", 'id_team = ?', [idTeam]);
        const TeamMem = await Elkaisar.DB.ASelectFrom("id_player", "team_member", "id_team = ?", [idTeam]);
        const TeamPlayer = await Elkaisar.DB.ASelectFrom("*", "team_member", "id_team = ? AND id_player = ?", [idTeam, this.idPlayer]);

        if (!TeamPlayer.length)
            return {state: "error_0"};
        if (TeamPlayer[0].rank > Elkaisar.Config.TEAM_R_MEMBER)
            return {state: "error_1"};
        if (TeamMem.length <= 1)
            return {state: "error_2"};

        await Elkaisar.DB.ADelete("team_member", "id_team = ? AND id_player = ?", [idTeam, this.idPlayer]);

        Elkaisar.DB.Update("mem_num = (SELECT COUNT(*) FROM team_member WHERE id_team = ?)", "team", "id_team = ?", [idTeam, idTeam]);
        const Msg = JSON.stringify({
            classPath: "Team.playerTeamLeave",
            idPlayer: this.idPlayer,
            PlayerName: (await Elkaisar.DB.ASelectFrom("name", "player", "id_player = ?", [this.id_player]))[0].name,
            TeamName: Team[0].name
        });
        TeamMem.forEach(function (P) {
            Elkaisar.Base.sendMsgToPlayer(P.id_player, Msg);
        });


        return {
            state: 'ok'
        };

    }

    async playerTeamResign() {

        const idTeam = Elkaisar.Base.validateId(this.Parm.idTeam);
        const Team = await Elkaisar.DB.ASelectFrom("*", "team", 'id_team = ?', [idTeam]);
        const TeamMem = await Elkaisar.DB.ASelectFrom("id_player", "team_member", "id_team = ?", [idTeam]);
        const TeamPlayer = await Elkaisar.DB.ASelectFrom("*", "team_member", "id_team = ? AND id_player = ?", [idTeam, this.idPlayer]);

        if (!TeamPlayer.length)
            return {state: "error_0"};
        if (TeamPlayer[0].rank == Elkaisar.Config.TEAM_R_LEADER)
            return {state: "error_1"};
        if (TeamMem.length <= 1)
            return {state: "error_2"};

        Elkaisar.DB.Update("rank = ?", "team_member", "id_team = ? AND id_player = ?", [Elkaisar.Config.TEAM_R_MEMBER, idTeam, this.idPlayer]);
        const Msg = JSON.stringify({
            classPath: "Team.playerTeamResign",
            idPlayer: this.idPlayer,
            PlayerName: (await Elkaisar.DB.ASelectFrom("name", "player", "id_player = ?", [this.id_player]))[0].name,
            TeamName: Team[0].name
        });
        TeamMem.forEach(function (P) {
            Elkaisar.Base.sendMsgToPlayer(P.id_player, Msg);
        });


        return {
            state: 'ok'
        };

    }

    async playerOnline() {

        const idPeer = Elkaisar.Base.validateGameNames(this.Parm.idPeer);
        const PlayerTeam = Elkaisar.DB.ASelectFrom("*", "team_member", "id_player = ?", [this.idPlayer]);
        
        if(PlayerTeam.length < 1)
            return { state : "error_0" };
        
        await Elkaisar.DB.AQueryExc(`INSERT INTO player_peer_id (id_player, id_peer) values (? , ?) ON DUPLICATE KEY UPDATE id_peer = ?`, [this.idPlayer, idPeer, idPeer]);
        const AllTeamPeers = await Elkaisar.DB.ASelectFrom(
                "player_peer_id.*",
                "player_peer_id JOIN team_member ON team_member.id_player = player_peer_id.id_player",
                "team_member.id_team = ?", [PlayerTeam[0].id_team]);
        return {
            state : "ok",
            TeamPeers : AllTeamPeers
        };
    }
    
    async  disbandTeam()
    {
        const TeamMember  = await  Elkaisar.DB.ASelectFrom("team_member.*, team.id_leader, team.name", "team_member JOIN team ON team.id_team = team_member.id_team", "team_member.id_player = ?", [this.idPlayer]);
        if(TeamMember.length < 1)
            return {"state" : "error_0"};
        if(TeamMember[0]["rank"] < Elkaisar.Config.TEAM_R_LEADER)
            return {"state" : "error_1"};
        if(TeamMember[0]["id_leader"] != this.idPlayer)
            return {"state" : "error_2"};
        
        await Elkaisar.DB.ADelete("team_member", "id_team = ?", [TeamMember[0]["id_team"]]);
        await Elkaisar.DB.ADelete("team", "id_leader = ?", [this.idPlayer]);
        
        Elkaisar.Base.broadcast(JSON.stringify({
                                    classPath: "Team.TeamDisbanded",
                                    TeamName: TeamMember[0].name
                                }));
        
        return {
            "state" : "ok"
        };
    }
    
    
    async fireTeamMember(){
        
        const idMember = Elkaisar.Base.validateId(this.Parm.idMember);
        const TeamLeader  = await  Elkaisar.DB.ASelectFrom("team_member.*, team.id_leader, team.name", "team_member JOIN team ON team.id_team = team_member.id_team", "team_member.id_player = ?", [this.idPlayer]);
        const TeamMember = await  Elkaisar.DB.ASelectFrom("team_member.*, player.name", "team_member JOIN player ON player.id_player = team_member.id_player", "team_member.id_player = ?", [idMember]);
        
        if(TeamMember.length < 1)
            return {"state" : "error_0"};
        if(TeamLeader.length < 1)
            return {"state" : "error_1"};
        if(TeamLeader[0]["rank"] < Elkaisar.Config.TEAM_R_LEADER)
            return {"state" : "error_2"};
        if(TeamLeader[0]["id_leader"] != this.idPlayer || TeamLeader[0]["id_leader"] == idMember )
            return {"state" : "error_3"};
            
        const TeamMem = await Elkaisar.DB.ASelectFrom("id_player", "team_member", "id_team  = ?", [TeamLeader[0].id_team]) 
        await Elkaisar.DB.ADelete("team_member", "id_player = ?", [idMember]);
        Elkaisar.Lib.ALTeam.refreshTeamData(TeamLeader[0].id_team);
        var Msg = JSON.stringify({
                    classPath: "Team.TeamMemberFired",
                    TeamName: TeamLeader[0].name,
                    FiredName: TeamMember[0].name
                });
        TeamMem.forEach(function (Player){
            Elkaisar.Base.sendMsgToPlayer(Player.id_player,Msg);
        });
        
        return {
            "state" : "ok"
        };
    }

}

module.exports = ATeam;