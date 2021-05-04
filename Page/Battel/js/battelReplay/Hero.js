
class BRHero {

    Scene;
    HeroIndex;
    HeroCells;
    static HeroRoundList;
    static isFirstRound = true;
    Hero;
    callBack;
    ShowDelay;

    constructor(Hero, Index, ShowDelay, callBack) {
        this.Scene = BattelReplayScene.Scene;
        this.HeroIndex = Index;
        this.HeroCells = this.Scene.add.group();
        this.Hero = Hero;
        this.callBack = callBack;
        this.ShowDelay = ShowDelay;

        if (Round.InFieldHeros[Index]) {
            console.log(`Hero Of Place ${Index} Is In Field`)
            for (var ii in Round.InFieldHeros[Index].Blocks) {
                if (Round.InFieldHeros[Index].ArmyBlocks && Round.InFieldHeros[Index].ArmyBlocks[ii] && Round.InFieldHeros[Index].Blocks.ArmyAmount)
                    Round.InFieldHeros[Index].Blocks.ArmyAmount.setText(Round.InFieldHeros[Index].ArmyBlocks[ii].unit);
            }

            if (callBack) {
                console.log("Draw one")
                callBack();
                Round.CurrentRound.callBackCalled = true;
            }
            return;
        }



        /*,
         onComplete: function () {
         var Heros = Round.InFieldHeros;
         
         
         }*/

        this.addHero();
    }

    addHero() {
        var x = this.HeroIndex % 3;
        var y = Math.floor(this.HeroIndex / 3);
        var tx = (x - y) * 250;
        var ty = (x + y) * 125;

        var Center = {x: tx + this.Scene.scale.width / 3, y: ty + this.Scene.scale.height / 6};
        if (this.HeroIndex < 3) {
            Center = {x: tx + this.Scene.scale.width / 2.5, y: ty + this.Scene.scale.height / 7};
        }


        Round.InFieldHeros[`${this.HeroIndex}`] = this.Hero;
        Round.InFieldHeros[`${this.HeroIndex}`].Blocks = {};
        Round.InFieldHeros[`${this.HeroIndex}`].Profiles = this.Scene.add.group();
        this.addBlock(0, Center, "f_1");
        this.addBlock(1, Center, "f_2");
        this.addBlock(2, Center, "f_3");
        this.addBlock(3, Center, "b_1");
        this.addBlock(4, Center, "b_2");
        this.addBlock(5, Center, "b_3");
        this.addHeroProf(Center);
        this.addPlayerProf(Center);
    }

    addBlock(BlockIndex, Center, AcPlace) {
        var x = BlockIndex % 3;
        var y = Math.floor(BlockIndex / 3);
        var xBadding = 64;
        var yBadding = 32;
        var tx = (x - y) * xBadding;
        var ty = (x + y) * yBadding;
        if (this.HeroIndex < 3) {
            tx = (x + y) * xBadding;
            ty = (x - y) * yBadding;
        }


        if (Number(this.Hero.ArmyBlocks[BlockIndex].unit) <= 0)
            return;
        var ArmyBlock =
                (new BRHeroCell(
                        this.Scene,
                        {x: tx + Center.x, y: ty + Center.y, BlockPaddingX: tx, BlockPaddingY: ty},
                        {HeroIndex: this.HeroIndex, ArmyType: this.Hero.ArmyBlocks[BlockIndex].armyType, BlockIndex: BlockIndex, ArmyCount: this.Hero.ArmyBlocks[BlockIndex].unit, side: this.Hero.side}
                , this))
                .addLargeBlock();
        Round.InFieldHeros[this.HeroIndex].Blocks[`${BlockIndex}`] = ArmyBlock;
        this.HeroCells.add(ArmyBlock.ArmyBlock);
    }

    addHeroProf(Center) {
        var This = this;
        var ty = 25;
        var tx = 75;
        if (this.HeroIndex < 3) {
            tx = -15;
            ty = 90;
        }
        var T = this.Scene.add.text(
                This.Scene.scale.width / 2, This.Scene.scale.height + 50,
                Elkaisar.getHeroData(This.Hero.idHero).Hero.HeroName,
                {color: '#FFFFFF', stroke: '#000000', strokeThickness: 3, fontStyle: "bold", align: "center", fontSize: 14, fixedWidth: 100}).setDepth(1000);

        var I = this.Scene.add.image(This.Scene.scale.width / 2, This.Scene.scale.height + 50, Elkaisar.HeroAvatarBR[Elkaisar.getHeroData(This.Hero.idHero).Hero.avatar]).setDepth(1000);

        this.Scene.tweens.add({
            targets: [T],
            x: tx + Center.x,
            y: ty + Center.y,
            ease: 'Power1',
            duration: Elkaisar.Config.Delay.PosHero,
            delay: This.ShowDelay
        });
        this.Scene.tweens.add({
            targets: [I],
            x: tx + Center.x + 50,
            y: ty + Center.y - 20,
            ease: 'Power1',
            duration: Elkaisar.Config.Delay.PosHero,
            delay: This.ShowDelay
        });
        Round.InFieldHeros[`${this.HeroIndex}`].Profiles.add(T);
        Round.InFieldHeros[`${this.HeroIndex}`].Profiles.add(I);
    }
    addPlayerProf(Center) {

        var This = this;

        var ty = 125;
        var tx = -75;
        if (this.HeroIndex < 3) {
            tx = 125;
            ty = -15;
        }

        var HHH = Elkaisar.getHeroData(this.Hero.idHero);
        var Player = Elkaisar.getPlayerData(HHH.Hero.idPlayer);
        var PlayerName = "النظام";
        var PlayerAvatar = false;

        if (Player) {
            PlayerName = Player.Player.PlayerName;
            PlayerAvatar = Elkaisar.HeroAvatarBR[Player.Player.PlayerAvatar];
        }

        var T = this.Scene.add.text(
                This.Scene.scale.width / 2, This.Scene.scale.height + 50,
                PlayerName,
                {color: '#FFFFFF', stroke: '#000000', strokeThickness: 3, fontStyle: "bold", align: "center", fontSize: 14, fixedWidth: 100}).setDepth(1000);

        if (PlayerAvatar)
            var I = this.Scene.add.image(This.Scene.scale.width / 2, This.Scene.scale.height + 50, PlayerAvatar).setDepth(1000);

        this.Scene.tweens.add({
            targets: [T],
            x: tx + Center.x,
            y: ty + Center.y,
            ease: 'Power1',
            duration: Elkaisar.Config.Delay.PosHero,
            delay: This.ShowDelay
        });
        if (PlayerAvatar)
            this.Scene.tweens.add({
                targets: [I],
                x: tx + Center.x + 50,
                y: ty + Center.y - 20,
                ease: 'Power1',
                duration: Elkaisar.Config.Delay.PosHero,
                delay: This.ShowDelay
            });
        Round.InFieldHeros[`${this.HeroIndex}`].Profiles.add(T);
        if (PlayerAvatar)
            Round.InFieldHeros[`${this.HeroIndex}`].Profiles.add(I);

    }

}
;


var Count = 0;
class BRHeroCell {

    HeroIndex;
    BlockIndex;
    Center;
    Scene;
    ArmyType;
    ArmyBlock;
    ArmyCount;
    ArmyAmount;
    ArmySide;
    static BlockPlaces = {};
    Hero;

    constructor(Scene, Center, CellData, Hero) {
        this.Center = Center;
        this.HeroIndex = CellData.HeroIndex;
        this.Scene = Scene;
        this.ArmyType = CellData.ArmyType;
        this.BlockIndex = CellData.BlockIndex;
        BRHeroCell.BlockPlaces[`${CellData.HeroIndex}.${CellData.BlockIndex}`] = this;
        this.ArmyCount = CellData.ArmyCount;
        this.ArmySide = CellData.side;
        this.Hero = Hero;
    }
    blockCountColor() {
        if (this.ArmyCount <= 999)
            return "#ffffff";
        if (this.ArmyCount <= 9999)
            return "#a7e328";
        if (this.ArmyCount <= 99999)
            return "#00ffff";

        return "#aa00aa";

    }
    tweenBlock(Block, tx, ty) {
        var This = this;


        this.Scene.tweens.add({
            targets: Block,
            x: {
                getEnd: function () {
                    return tx + This.Center.x;
                },
                getStart: function () {
                    return  tx + This.Scene.scale.width / 2 + This.Center.BlockPaddingX;
                }
            },
            y: {
                getEnd: function () {
                    return  ty + This.Center.y;
                },
                getStart: function () {
                    return  ty + This.Scene.scale.height + 50 + This.Center.BlockPaddingY;
                }
            },
            ease: 'Power1',
            duration: Elkaisar.Config.Delay.PosHero,
            delay: This.Hero.ShowDelay,
            onComplete: function (e) {

                if (Round.CurrentRound.callBackCalled)
                    return console.log("called");
                if (!This.Hero.callBack)
                    return console.log("no call back");
                This.Hero.callBack();
                Round.CurrentRound.callBackCalled = true;
                console.log("Start Attack is Going " + This.HeroIndex)
            }
        });
    }
    addLargeBlock() {

        if (Number(this.ArmyCount) === 0)
            return this;

        var mapWidth = 5;
        var mapHeight = 4;

        var tileWidthHalf = 10;
        var tileHeightHalf = 5;
        var ArmyType = this.ArmyType;

        if (ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyB
                || ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyE
                || ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyF) {
            mapWidth = 4;
            mapHeight = 3;

        }

        var centerX = (mapWidth / 2) * tileWidthHalf;
        var centerY = -100;



        var This = this;
        var blocks = this.Scene.add.group();
        var Target;
        for (var y = 0; y < mapHeight; y++)
        {
            for (var x = 0; x < mapWidth; x++)
            {
                var tx = (x - y) * tileWidthHalf;
                var ty = (x + y) * tileHeightHalf;
                Target = blocks.create(tx + This.Scene.scale.width / 2, This.Scene.scale.height + 50, Elkaisar.BattelReplay.TroopsAnimation[this.ArmyType], this.ArmySide * 4).setDepth(this.Center.y + ty);

                this.tweenBlock(Target, tx, ty);

            }
        }


        Phaser.Actions.Call(blocks.getChildren(), function (One) {
            if (One.type == "Sprite")
                One.play(`Idle.${This.ArmyType}.Side.${This.ArmySide}`);

        });

        var ArmyCount = this.Scene.add.text(
                This.Scene.scale.width / 2,
                This.Scene.scale.height + 60,
                this.ArmyCount,
                {color: this.blockCountColor(),
                    stroke: '#000000',
                    strokeThickness: 3,
                    fontStyle: "bold",
                    align: "center",
                    fontSize: 14}).setDepth(650 + ty);


        blocks.add(ArmyCount);
        this.tweenBlock(ArmyCount, 0, 10);
        this.ArmyBlock = blocks;
        this.ArmyAmount = ArmyCount;
        return this;
    }
    kill(Amount) {
        this.ArmyCount -= Amount;
        this.ArmyAmount.setText(this.ArmyCount);
    }

    killFromCell(Amount) {

        var Text = this.Scene.add.text(this.Center.x, this.Center.y + 25, Amount, {color: '#ff0000', stroke: '#000000', strokeThickness: 3, fontStyle: "bold", align: "center", fontSize: 14}).setDepth(1000);
        var This = this;
        this.Scene.tweens.add({
            targets: Text,
            y: this.Center.y - 25,
            alpha: 0.2,
            ease: 'Power1',
            duration: Elkaisar.Config.Delay.Base,
            onComplete: function (e) {
                Text.destroy();

            },
            onStart: function () {
                This.kill(Amount);
            }
        });

        return this;
    }

    killWithBreak(Amount) {
        var Text = this.Scene.add.bitmapText(this.Center.x, this.Center.y + 25, 'desyrel', "#" + Amount, 20).setDepth(1000);
        var This = this;
        this.Scene.tweens.add({
            targets: Text,
            y: this.Center.y - 50,
            scale: 2,
            ease: 'linear',
            duration: 2000,
            onComplete: function (e) {
                Text.destroy();
            },
            onStart: function () {
                This.kill(Amount);
            }
        });

        return this;
    }

    killWithStrike(Amount) {
        var Text = this.Scene.add.bitmapText(this.Center.x, this.Center.y + 25, 'desyrel-pink', "*" + Amount, 20).setDepth(1000);
        var This = this;
        this.Scene.tweens.add({
            targets: Text,
            y: this.Center.y - 50,
            scale: 2,
            ease: 'linear',
            duration: 2000,
            onComplete: function (e) {
                Text.destroy();
            },
            onStart: function () {
                This.kill(Amount);
            }
        });
        return this;
    }

    startAttack(HeroIndex, CellIndex, AttackOrderIndex, AttackData, callBack) {

        if (!Round.InFieldHeros[HeroIndex])
            return console.log("Error Hero Index" + HeroIndex)
        if (!Round.InFieldHeros[HeroIndex].Blocks[CellIndex])
            return console.log("Error Block  Index " + CellIndex, Round.InFieldHeros[HeroIndex].Blocks)
        var Block = Round.InFieldHeros[HeroIndex].Blocks[CellIndex];

        var This = this;

        var Attack = this.Scene.add.sprite(this.Center.x, this.Center.y, 'ArmyWeapons', Elkaisar.BattelReplay.ArmyTypsWeapons[this.ArmyType]).setDepth(1000).setVisible(false);




        this.Scene.tweens.add({
            targets: Attack,
            x: Block.Center.x,
            y: Block.Center.y,
            ease: 'linear',
            duration: Elkaisar.Config.Delay.Attack,
            delay: function (target, targetKey, value, targetIndex, totalTargets, tween) {
                return AttackOrderIndex * Elkaisar.Config.Delay.Attack;
            },
            onComplete: function (e) {
                Attack.destroy();

                Block.BlockDead(AttackData.AmountDead);
                if (AttackData.attackType == Elkaisar.Config.AttackTypeNorm)
                    Block.killFromCell(AttackData.AmountDead);
                else if (AttackData.attackType == Elkaisar.Config.AttackTypeStrike)
                    Block.killWithStrike(AttackData.AmountDead);
                else if (AttackData.attackType == Elkaisar.Config.AttackTypeBreak)
                    Block.killWithBreak(AttackData.AmountDead);

                if (callBack) {
                    console.log("Am Here Babe Too");
                    BattelReplayScene.Scene.time.delayedCall(1000, callBack);
                }
            },
            onStart: function () {
                Attack.setVisible(true);
                Phaser.Actions.Call(This.ArmyBlock.getChildren(), function (One) {
                    if (One.type == "Sprite")
                        One.play(`Attack.${This.ArmyType}.Side.${This.ArmySide}`);
                    One.on('animationcomplete', function (currentAnim, currentFramee, sprite) {
                        One.play(`Idle.${This.ArmyType}.Side.${This.ArmySide}`)
                    });

                });

            }
        });

    }

    BlockDead() {
        if (this.ArmyCount === 0)
            return this;
        var This = this;
        var Sols = this.ArmyBlock.getChildren();
        var Sol;
        Phaser.Actions.Call(Sols, function (Sol) {
            if (Sol.type === "Sprite") {
                var Rand = Math.random() * 100;

                if (Rand < 10) {
                    Sol.stop();
                    Sol.setTexture("DeadSol", Elkaisar.BattelReplay.DeadSol[`${Math.floor(This.HeroIndex / 3)}.${This.ArmyType}`]);
                }

            }
        });
    }

}


var count = 0;





