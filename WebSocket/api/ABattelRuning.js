class ABattel {

    Parm;
    idPlayer;
    constructor(idPlayer, Url) {
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }


    async refreshBattelData() {

        const BattelRaw = Elkaisar.DB.ASelectFrom("*", "battel", "id_battel = ?", [this.Parm.idBattel]);
        if (!BattelRaw.legnth)
            return {};
        return BattelRaw;

    }

}

module.exports = AHeroArmy;