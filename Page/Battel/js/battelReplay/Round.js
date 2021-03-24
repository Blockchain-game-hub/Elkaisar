
class Round {

    RoundData;
    static InFieldHeros = {};
    static CurrentRound;
    AttackStarted = false;
    static currentRoundIndex = 0;

    static AttParmIndex = {
        HeroAttackIndex: 0,
        HeroBlockAttackIndex: 1,
        HeroDefenceIndex: 2,
        HeroBlockDefenceIndex: 3,
        AttackType: 4,
        AmountDead: 5
    };

    constructor(RoundIndex) {
        this.RoundData = BattelReplay.BattelRecord.Rounds[RoundIndex];
        if(!this.RoundData)
            return ;
        Round.CurrentRound = this;
        Round.currentRoundIndex = RoundIndex;
        this.startNewRound();
    }

    startNewRound() {
        
        var InDeadHeros = 0;
        for (var HeroIndex in this.RoundData.Heros) {
            if(Round.InFieldHeros[HeroIndex])
                InDeadHeros++;
            new BRHero(this.RoundData.Heros[HeroIndex], HeroIndex);
        }
        
        var Heros = this.RoundData.Heros;
        setTimeout(function () {
            Round.CurrentRound.startRoundAttacks();
            console.log("Attack Started")
        }, (Object.keys(Heros).length - InDeadHeros)*Elkaisar.Config.Delay.PosHero);
        console.log((Object.keys(Heros).length - InDeadHeros)*Elkaisar.Config.Delay.PosHero)
    }


    static RemoveHeroBlock(HeroIndex, BlockIndex) {
        
        Phaser.Actions.Call(Round.InFieldHeros[HeroIndex].Blocks[BlockIndex].ArmyBlock.getChildren(), function (Unit) {
            BattelReplayScene.Scene.tweens.add({
                targets: Unit,
                alpha: 0,
                ease: 'linear',
                duration: Math.random() * 2000,
                onComplete: function (e) {
                    Unit.destroy();
                }
            });
        });



    }

    static SweapHeros() {

        var HeroEmpty = true;
        Object.values(Round.CurrentRound.RoundData.Heros).forEach(function (Hero, HeroIndex){
            HeroEmpty = true;
            for (var ii in Hero.ArmyBlocks) {
                if (Hero.ArmyBlocks[ii].dead >= Hero.ArmyBlocks[ii].unit && Hero.ArmyBlocks[ii].unit > 0)
                    Round.RemoveHeroBlock(Hero.HeroIndex, ii);
                else {
                    HeroEmpty = false;
                }
            }
            
            if(HeroEmpty)
                Round.InFieldHeros[Hero.HeroIndex] = null;
        });
    }

    startRoundAttacks() {

        if (this.AttackStarted)
            return;
        this.AttackStarted = true;
        var Attacks = this.RoundData.Attacks;
        Attacks.forEach(function (Attack, Index) {

            var AttackParm = Attack.split(".");
            Round.InFieldHeros[AttackParm[Round.AttParmIndex.HeroAttackIndex]]
                    .Blocks[AttackParm[Round.AttParmIndex.HeroBlockAttackIndex]]
                    .startAttack(
                            AttackParm[Round.AttParmIndex.HeroDefenceIndex],
                            AttackParm[Round.AttParmIndex.HeroBlockDefenceIndex],
                            Index, {attackType: AttackParm[Round.AttParmIndex.AttackType], AmountDead: AttackParm[Round.AttParmIndex.AmountDead]});
        });

        setTimeout(this.startNextRound, (Attacks.length - 1) * Elkaisar.Config.Delay.Attack);
    }

    startNextRound() {
        Round.SweapHeros();
        new Round(Round.currentRoundIndex + 1);
    }

}