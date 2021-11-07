class CPGodGate {

    Parm;
    idPlayer;
    constructor(Url) {
        this.Parm = Url;
    }
    
    
    async getGodGateGeneralData(){
        
        return {
            Req: await Elkaisar.DB.ASelectFrom("*", "god_gate_req", "1"),
            Max:  await Elkaisar.DB.ASelectFrom("*", "god_gate_max_val", "1")
        };
        
    }
    
    async changeGodGateGeneralData(){
        
        await Elkaisar.DB.AUpdate(`god_gate_1_points = ${this.Parm.Gate_1_Points}, god_gate_1_porm = ${this.Parm.Gate_1_Porm},
                                    god_gate_2_points = ${this.Parm.Gate_2_Points}, god_gate_2_porm = ${this.Parm.Gate_2_Porm},
                                    god_gate_3_points = ${this.Parm.Gate_3_Points}, god_gate_3_porm = ${this.Parm.Gate_3_Porm}, 
                                    god_gate_4_points = ${this.Parm.Gate_4_Points}, god_gate_4_porm = ${this.Parm.Gate_4_Porm}`,"god_gate_req", "1");
         return {state : "ok"};
    }
    
    async changeGodGateMaxVal(){
        
        await Elkaisar.DB.AUpdate(`vit = ${this.Parm.vit},        attack = ${this.Parm.attack}, 
                                    damage = ${this.Parm.damage}, defence = ${this.Parm.defence}, 
                                    break = ${this.Parm.break},   anti_break = ${this.Parm.anti_break}, 
                                    strike = ${this.Parm.strike}, immunity = ${this.Parm.immunity}`,"god_gate_max_val", "1");
        return {state : "ok"};
        
    }
    
    
    async getGateRankEffect(){
        
        return await Elkaisar.DB.ASelectFrom("*", "`"+this.Parm.Gate+"`", "1");
    }
    
    
    async changeRankEff(){
        
        const Gate  = Elkaisar.Base.validateGameNames(this.Parm.Gate);
        const Rank  = Elkaisar.Base.validateGameNames(this.Parm.Rank);
        const Point = Elkaisar.Base.validateGameNames(this.Parm.Point);
        const Val   = Elkaisar.Base.validateId(this.Parm.Val)
        
        
        await Elkaisar.DB.AUpdate(`${Point} = ${Val}`, Gate, "rank = ?", [Number(Rank) + 1]);
        Elkaisar.Lib.LPlayer.getRankPointPluse();
        return {state: "ok"}
        
    }
    
    

}

module.exports = CPGodGate;