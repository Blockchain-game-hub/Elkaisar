class LFightRound {

    Battel;
    constructor(Battel) {
        this.Battel = Battel;
        this.scaneRoundHeros();
    }

    scaneRoundHeros() {
        for (var ii in this.Battel.Fight.RoundHeros) {
            if (this.Battel.Fight.checkHeroSweped(this.Battel.Fight.RoundHeros[ii]))
                this.Battel.Fight.RoundHeros[ii] = false;
        }
    }


    hasHerosToFight() {
        if ((this.Battel.Fight.RoundHeros[0] || this.Battel.Fight.RoundHeros[1] || this.Battel.Fight.RoundHeros[2])
                && (this.Battel.Fight.RoundHeros[3] || this.Battel.Fight.RoundHeros[4] || this.Battel.Fight.RoundHeros[5]))
            return true;

        return false;
    }

    addHeroToRound(Hero) {
     
        for (var ii in this.Battel.Fight.RoundHeros) {

            if (this.Battel.Fight.RoundHeros[ii] != false)
                continue;
            if(this.checkIfHeroInRound(Hero.idHero))
                return true;
            if (Hero.side == Elkaisar.Config.BATTEL_SIDE_DEF) {
                if(ii< 3){
                    Hero.Index = ii;
                    this.Battel.Fight.RoundHeros[ii] = Hero;
                }
                return true;
            }

            if (Hero.side == Elkaisar.Config.BATTEL_SIDE_ATT) {
                if(ii >= 3){
                    Hero.Index = ii;
                    this.Battel.Fight.RoundHeros[ii] = Hero;
                }else{
                    continue;
                }
                return true;
            }
        }

        return false;
    }

    checkIfHeroInRound(idHero) {
        for (var iii in this.Battel.Fight.RoundHeros) {
            if(!this.Battel.Fight.RoundHeros[iii])
                continue;
            if (idHero == this.Battel.Fight.RoundHeros[iii].idHero) 
                return true;
            
        }
        return false;
    }
    
    
    startRoundFight(){
        
        this.Battel.Fight.FightRecord.addRound(this.Battel.Fight.RoundHeros);
        
        this.startHeroFight(3);
        this.startHeroFight(4);
        this.startHeroFight(5);
        this.startHeroFight(0);
        this.startHeroFight(1);
        this.startHeroFight(2);
        
       
        this.Battel.Fight.scanHero(this.Battel.Fight.RoundHeros);
        
    }
    
    startHeroFight(HeroIndex){
        
        if(!this.Battel.Fight.RoundHeros[HeroIndex])
            return ;
        
        
        
        this.startHeroCellFight(HeroIndex, 0);
        this.startHeroCellFight(HeroIndex, 1);
        this.startHeroCellFight(HeroIndex, 2);
        this.startHeroCellFight(HeroIndex, 3);
        this.startHeroCellFight(HeroIndex, 4);
        this.startHeroCellFight(HeroIndex, 5);
        
    }
    
    startHeroCellFight(HeroAttIndex, CellAttIndex){
        
        
        var HeroAttack =  this.Battel.Fight.RoundHeros[HeroAttIndex];
        
        if(HeroAttack == false)
            return ;
                
        var CellAttacks = this.cellsToAttack(HeroAttIndex, CellAttIndex);
        var HI;
        var CI;
        var attackedTimes = 0;
        var ShouldAttack = 0;
        var NotBlindSpots = [];
        for( HI in CellAttacks){
            if(!this.Battel.Fight.RoundHeros[HI])
                continue;
            
            NotBlindSpots = [];
            for( CI in CellAttacks[HI]){
                if(!this.Battel.Fight.RoundHeros[HI].real_eff[CellAttacks[HI][CI]])
                    continue;
                
                if(this.cellAttack(HeroAttIndex, CellAttIndex, HI, CellAttacks[HI][CI])){
                    NotBlindSpots.push(CellAttacks[HI][CI]);
                }
            }
            
            if(NotBlindSpots.length > 0)
                continue;
            attackedTimes = 0;
            ShouldAttack = CellAttacks[HI].length;
            
            for(var ii = 0; ii < 7; ii ++){
                
                if(attackedTimes >= ShouldAttack )
                    break;
                if(NotBlindSpots.includes(ii))
                    continue;
                if(CellAttacks[HI].includes(ii))
                    continue;
                if(this.cellAttack(HeroAttIndex, CellAttIndex, HI, ii))
                    attackedTimes ++;
            }
        }
    }
    
    
    cellAttack(HeroAttIndex, CellAttIndex, HeroDefIndex, CellDefIndex){
        
        var HeroDef = this.Battel.Fight.RoundHeros[HeroDefIndex];
        var HeroAtt = this.Battel.Fight.RoundHeros[HeroAttIndex];
        
        if(!HeroDef || !HeroAtt)
            return false;
        var CellAttack  = HeroAtt.real_eff[CellAttIndex];
        var CellDefence = HeroDef.real_eff[CellDefIndex];
        
        if(!CellAttack || !CellDefence )
            return false;
        if(CellDefence.unit <= 0 || CellDefence.unit <= CellDefence.dead_unit)
            return false;
        if(CellAttack.unit <= 0)
            return false;
        
        
        
        
        
        var OneSolDamage = 1;
        var StrikePers = CellAttack.strike - CellDefence.immunity;
        var BreakPers = CellAttack.break  - CellDefence.anti_break;
        var AttackType = Elkaisar.Config.AttackTypeNorm;
        
        if(StrikePers > 0 && Math.random()*100 <  StrikePers){
            OneSolDamage *= 1.5;
            AttackType = Elkaisar.Config.AttackTypeStrike;
        }
            

        if(BreakPers > 0 && Math.random()*100 <  BreakPers){
            if(AttackType == Elkaisar.Config.AttackTypeStrike)
                AttackType = Elkaisar.Config.AttackTypeStrikeAndBreak;
            else 
                AttackType = Elkaisar.Config.AttackTypeBreak ;
            
            OneSolDamage *= 1.5;
        }
            
        
        
        var unDefSol   = Math.ceil(CellAttack["unit"] * CellAttack.attack / CellDefence.def);
        var DeadUnits  = Math.ceil(CellAttack["unit"] * CellAttack["dam"] / CellDefence.vit);
        var totalDead = Math.min(unDefSol, DeadUnits) * OneSolDamage;

        /* this condetion will check  if the two cells have not fight done*/



        CellDefence ["dead_unit"]    += totalDead;
        var amountDead                = Elkaisar.Config.CArmy.ArmyCap[CellDefence["armyType"]] * totalDead;
        CellAttack  ["troopsKills"]  += amountDead;
        CellDefence ["troopsKilled"] += amountDead;
        CellAttack  ["honor"]        += Math.ceil(totalDead / CellDefence["def"]);
        CellAttack  ["points"]       += Math.ceil(totalDead);
        
     
        
        /*idHeroAttack.CellAttPlace.idHeroDefence.CellDefPlace.AttackType.KillAmount*/
        this.Battel.Fight.FightRecord.addAttack(`${HeroAttIndex}.${CellAttIndex}.${HeroDefIndex}.${CellDefIndex}.${AttackType}.${totalDead}`);
       
        return true;
    }
    
    cellsToAttack(HeroIndex, CellIndex){
        
        var Attacks  = {};
        var CellRow  = Math.floor(CellIndex/3);
        var Side     = this.Battel.Fight.RoundHeros[HeroIndex].side;
        var ArmyType = this.Battel.Fight.RoundHeros[HeroIndex].real_eff[CellIndex].armyType;
        
       
       
        if(ArmyType == Elkaisar.Config.ARMY_A){
            
            /*Attacks[Other Hero Index]*/
            Attacks[(1-Side)*3 + 0] = [CellIndex, CellIndex + 3];
            Attacks[(1-Side)*3 + 1] = [CellIndex, CellIndex + 3];
            Attacks[(1-Side)*3 + 2] = [CellIndex, CellIndex + 3];
            Attacks[(1-Side)*3 + HeroIndex%3] = [CellIndex, CellIndex + 3, 0 + 3*CellRow, 1+ 3*CellRow, 2+ 3*CellRow];
        }
        
        if(ArmyType == Elkaisar.Config.ARMY_B){
            Attacks[(1-Side)*3 + 0] = [0, 2, 3, 5];
            Attacks[(1-Side)*3 + 1] = [0, 2, 3, 5];
            Attacks[(1-Side)*3 + 2] = [0, 2, 3, 5];
            Attacks[(1-Side)*3 + HeroIndex%3].push(CellIndex, CellIndex + 3);
        }
        
        if(ArmyType == Elkaisar.Config.ARMY_C){
            Attacks[(1-Side)*3 + 0] = [CellIndex, CellIndex + 3, 0, 1, 2];
            Attacks[(1-Side)*3 + 1] = [CellIndex, CellIndex + 3, 0, 1, 2];
            Attacks[(1-Side)*3 + 2] = [CellIndex, CellIndex + 3, 0, 1, 2];
            Attacks[(1-Side)*3 + HeroIndex%3].push(3, 4, 5);
        }
        
        if(ArmyType == Elkaisar.Config.ARMY_D){
            var Hero = this.Battel.Fight.RoundHeros[HeroIndex];
            
            Attacks[(1-Side)*3 + 0] = [CellIndex, CellIndex + 3];
            Attacks[(1-Side)*3 + 1] = [CellIndex, CellIndex + 3];
            Attacks[(1-Side)*3 + 2] = [CellIndex, CellIndex + 3];
            
            if(Hero.EquipSpAt[Elkaisar.Config.EquipSpAtArrowRainA]){
                Attacks[(1-Side)*3 + 0] = [0, 1, 2];
                Attacks[(1-Side)*3 + 1] = [0, 1, 2];
                Attacks[(1-Side)*3 + 2] = [0, 1, 2];
            }
            if(Hero.EquipSpAt[Elkaisar.Config.EquipSpAtArrowRainB]){
                Attacks[(1-Side)*3 + 0] = [3, 4, 5];
                Attacks[(1-Side)*3 + 1] = [3, 4, 5];
                Attacks[(1-Side)*3 + 2] = [3, 4, 5];
            }
            
            if(Hero.EquipSpAt[Elkaisar.Config.EquipSpAtArrowRainB] && Hero.EquipSpAt[Elkaisar.Config.EquipSpAtArrowRainA]){
                Attacks[(1-Side)*3 + 0] = [0, 1, 2, 3, 4, 5];
                Attacks[(1-Side)*3 + 1] = [0, 1, 2, 3, 4, 5];
                Attacks[(1-Side)*3 + 2] = [0, 1, 2, 3, 4, 5];
            }
        }
        
        if(ArmyType == Elkaisar.Config.ARMY_E){
            Attacks[(1-Side)*3 + 0] = [3, 4, 5];
            Attacks[(1-Side)*3 + 1] = [3, 4, 5];
            Attacks[(1-Side)*3 + 2] = [3, 4, 5];
            Attacks[(1-Side)*3 + HeroIndex%3].push(CellIndex, CellIndex + 3);
        }
        
        if(ArmyType == Elkaisar.Config.ARMY_F){
            Attacks[(1-Side)*3 + 0] = [CellIndex, CellIndex + 3, 3, 4, 5];
            Attacks[(1-Side)*3 + 1] = [CellIndex, CellIndex + 3, 3, 4, 5];
            Attacks[(1-Side)*3 + 2] = [CellIndex, CellIndex + 3, 3, 4, 5];
            Attacks[(1-Side)*3 + HeroIndex%3].push(0, 1, 2);
        }
        
        for(var HI in Attacks){
            Attacks[HI] = [...new Set(Attacks[HI])];
            Attacks[HI].sort(function(a, b){return a - b;});
        }
    
        return Attacks;
            
    }

}


module.exports = LFightRound;


