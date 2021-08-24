class CPEquip {

    Parm;
    idPlayer;
    constructor(Url) {
        this.Parm = Url;
    }


    async changeEquipPower() {
        console.log(this.Parm)
        const idEquip = Elkaisar.Base.validateGameNames(this.Parm.idEquip);
        const attack = Elkaisar.Base.validateId(this.Parm.attack);
        const defence = Elkaisar.Base.validateId(this.Parm.defence);
        const vitality = Elkaisar.Base.validateId(this.Parm.vitality);
        const damage = Elkaisar.Base.validateId(this.Parm.damage);
        const anti_break = Elkaisar.Base.validateId(this.Parm.anti_break);
        const _break = Elkaisar.Base.validateId(this.Parm.break);
        const strike = Elkaisar.Base.validateId(this.Parm.strike);
        const immunity = Elkaisar.Base.validateId(this.Parm.immunity);
        const sp_attr = Elkaisar.Base.validateId(this.Parm.sp_attr);
        const Equip = idEquip.split("_");

        Elkaisar.DB.Update(
            `attack = ${attack}, defence = ${defence}, vitality = ${vitality}, 
             damage = ${damage}, break = ${_break}, anti_break = ${anti_break}, 
             strike = ${strike}, immunity = ${immunity}, sp_attr = ${sp_attr}`,
            "equip_power", "equip = ? AND part = ? AND lvl = ?", [Equip[0], Equip[1], Equip[2]]);

        Elkaisar.World.getEquipPower();
        return {state : "ok"}
    }

}

module.exports = CPEquip;