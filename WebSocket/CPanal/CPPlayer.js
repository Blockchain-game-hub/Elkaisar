class CPPlayer {

    Parm;
    idPlayer;
    constructor(Url) {
        this.Parm = Url;
    }

    async getPlayerTitle() {

        const idPlayer = Elkaisar.Base.validateId(this.Parm.idPlayer);
        return await Elkaisar.DB.ASelectFrom("*", "player_title", "id_player = ?", [idPlayer]);
    }

    async changePlayerTitles() {

        const idPlayer = Elkaisar.Base.validateId(this.Parm.idPlayer);
        const Player = await Elkaisar.DB.ASelectFrom("name", "player", "id_player = ?", [idPlayer]);
        const Title_1 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_1);
        const Title_2 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_2);
        const Title_3 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_3);
        const Title_4 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_4);
        const Title_5 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_5);
        const Title_6 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_6);
        const Title_7 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_7);
        const Title_8 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_8);
        const Title_9 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_9);
        const Title_10 = Elkaisar.Base.validatePlayerWord(this.Parm.Title_10);
        var Titles = [];
        if (Title_1 != "" && Title_1 != " ")
            Titles.push(`title_1 = '${Title_1}'`);
        else
            Titles.push(`title_1 = NULL`);

        if (Title_2 != "" && Title_2 != " ")
            Titles.push(`title_2 = '${Title_2}'`);
        else
            Titles.push(`title_2 = NULL`);
        if (Title_3 != "" && Title_3 != " ")
            Titles.push(`title_3 = '${Title_3}'`);
        else
            Titles.push(`title_3 = NULL`);
        if (Title_4 != "" && Title_4 != " ")
            Titles.push(`title_4 = '${Title_4}'`);
        else
            Titles.push(`title_4 = NULL`);
        if (Title_5 != "" && Title_5 != " ")
            Titles.push(`title_5 = '${Title_5}'`);
        else
            Titles.push(`title_5 = NULL`);
        if (Title_6 != "" && Title_6 != " ")
            Titles.push(`title_6 = '${Title_6}'`);
        else
            Titles.push(`title_6 = NULL`);
        if (Title_7 != "" && Title_7 != " ")
            Titles.push(`title_7 = '${Title_7}'`);
        else
            Titles.push(`title_7 = NULL`);
        if (Title_8 != "" && Title_8 != " ")
            Titles.push(`title_8 = '${Title_8}'`);
        else
            Titles.push(`title_8 = NULL`);
        if (Title_9 != "" && Title_9 != " ")
            Titles.push(`title_9 = '${Title_9}'`);
        else
            Titles.push(`title_9 = NULL`);
        if (Title_10 != "" && Title_10 != " ")
            Titles.push(`title_10 = '${Title_10}'`);
        else
            Titles.push(`title_10 = NULL`);

        await Elkaisar.DB.AUpdate(Titles.join(", "), "player_title", "id_player = ?", [idPlayer]);
        const PlayerTitle = await Elkaisar.DB.ASelectFrom("*", "player_title", "id_player = ?", [idPlayer]);
        if (Elkaisar.Arr.Players[idPlayer])
            Elkaisar.Arr.Players[idPlayer].playerTitles = [
                PlayerTitle[0].title_1, PlayerTitle[0].title_2, PlayerTitle[0].title_3,
                PlayerTitle[0].title_4, PlayerTitle[0].title_5, PlayerTitle[0].title_6,
                PlayerTitle[0].title_5, PlayerTitle[0].title_8, PlayerTitle[0].title_7,
                PlayerTitle[0].title_2
            ];
        
        Elkaisar.Base.broadcast(JSON.stringify({
           classPath: "Chat.PlayerTitleChanged",
           PlayerName: Player[0].name
        }))

        return {state: "ok"};


    }

}

module.exports = CPPlayer;