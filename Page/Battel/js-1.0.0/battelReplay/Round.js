
class Round {

    RoundData;
    static InFieldHeros = {};
    static CurrentRound;
    AttackStarted = false;
    static currentRoundIndex = 0;
    static HeroShowDelay = 0;
    callBackCalled = false;
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
        if (!this.RoundData)
            return;
        Round.CurrentRound = this;
        Round.currentRoundIndex = RoundIndex;
        Round.HeroShowDelay = 0;
        this.startNewRound();
    }

    startNewRound() {

        var HeroIndexs = Object.keys(this.RoundData.Heros).reverse();

        for (var HeroIndex in this.RoundData.Heros) {

            var delay = HeroIndexs.length - HeroIndex - 1;

            if (HeroIndex == HeroIndexs[0]) {

                new BRHero(this.RoundData.Heros[HeroIndex], HeroIndex, HeroIndex * Elkaisar.Config.Delay.HeroShow, function () {
                    Round.CurrentRound.startRoundAttacks();
                });
            } else {
                new BRHero(this.RoundData.Heros[HeroIndex], HeroIndex, HeroIndex * Elkaisar.Config.Delay.HeroShow);
            }
        }

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

        Phaser.Actions.Call(Round.InFieldHeros[HeroIndex].Profiles.getChildren(), function (Unit) {
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

    static SweapHeros(callBack) {

        var HeroEmpty = true;
        var Heros = Object.values(Round.CurrentRound.RoundData.Heros);
        Heros.forEach(function (Hero, HeroIndex) {
            HeroEmpty = true;
            for (var ii in Hero.ArmyBlocks) {
                if (Hero.ArmyBlocks[ii].unit > 0) {
                    if (Hero.ArmyBlocks[ii].dead >= Hero.ArmyBlocks[ii].unit)
                        Round.RemoveHeroBlock(Hero.HeroIndex, ii);
                    else {
                        HeroEmpty = false;
                    }
                }

            }

            if (HeroEmpty)
                Round.InFieldHeros[Hero.HeroIndex] = null;

        });
    }

    startRoundAttacks() {
        console.log("Attack is starting")
        if (this.AttackStarted)
            return;
        this.AttackStarted = true;
        var Attacks = this.RoundData.Attacks;
        var callBack = false;
        var This = this;
        console.log("Attack is started")
        Attacks.forEach(function (Attack, Index) {

            if (Index == Attacks.length - 1)
                callBack = function () {
                    This.startNextRound();
                };

            var AttackParm = Attack.split(".");
            Round.InFieldHeros[AttackParm[Round.AttParmIndex.HeroAttackIndex]]
                    .Blocks[AttackParm[Round.AttParmIndex.HeroBlockAttackIndex]]
                    .startAttack(
                            AttackParm[Round.AttParmIndex.HeroDefenceIndex],
                            AttackParm[Round.AttParmIndex.HeroBlockDefenceIndex],
                            Index,
                            {attackType: AttackParm[Round.AttParmIndex.AttackType], AmountDead: AttackParm[Round.AttParmIndex.AmountDead]}, callBack);
        });


    }

    startNextRound() {

        Round.SweapHeros();
        this.AttackStarted = false;
        this.callBackCalled = false;
        BattelReplayScene.Scene.time.delayedCall(2000, function () {
            new Round(Round.currentRoundIndex + 1);
        });

    }

}