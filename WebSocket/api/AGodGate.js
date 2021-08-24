class AGodGate {

        Parm;
        idPlayer;
        constructor(idPlayer, Url) {
                this.Parm = Url;
                this.idPlayer = idPlayer;
        }


        async getRankEffect() {
            return Elkaisar.Lib.LPlayer.RANK_POINT_PLUSE;
        }

}

module.exports = AGodGate;