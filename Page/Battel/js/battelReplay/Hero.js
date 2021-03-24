
class BRHero {

    Scene;
    HeroIndex;
    HeroCells;
    static HeroRoundList;
    static isFirstRound = true;
    Hero;
    constructor(Hero, Index) {
        this.Scene = BattelReplayScene.Scene;
        this.HeroIndex = Index;
        this.HeroCells = this.Scene.add.group();
        this.Hero = Hero;
        
        if(Round.InFieldHeros[Index]){
            for(var ii in Round.InFieldHeros[Index].Blocks){
                if(Round.InFieldHeros[Index].ArmyBlocks && Round.InFieldHeros[Index].ArmyBlocks[ii] && Round.InFieldHeros[Index].Blocks.ArmyAmount )
                    Round.InFieldHeros[Index].Blocks.ArmyAmount.setText(Round.InFieldHeros[Index].ArmyBlocks[ii].unit);
            }
            return ;
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
        var tx = (x - y) * 300;
        var ty = (x + y) * 150;
        var Center = {x: tx + window.innerWidth / 3, y: ty + window.innerHeight / 6};
        if (this.HeroIndex < 3) {
            Center = {x: tx + window.innerWidth / 2.5, y: ty + window.innerHeight / 7};
        }


        Round.InFieldHeros[`${this.HeroIndex}`] = this.Hero;
        Round.InFieldHeros[`${this.HeroIndex}`].Blocks = {};
        this.addBlock(0, Center, "f_1");
        this.addBlock(1, Center, "f_2");
        this.addBlock(2, Center, "f_3");
        this.addBlock(3, Center, "b_1");
        this.addBlock(4, Center, "b_2");
        this.addBlock(5, Center, "b_3");
    }

    addBlock(BlockIndex, Center, AcPlace) {
        var x = BlockIndex % 3;
        var y = Math.floor(BlockIndex / 3);
        
        var tx = (x - y) * 90;
        var ty = (x + y) * 45;  
        if(this.HeroIndex < 3){
            tx = (x + y) * 90;
            ty = (x - y) * 45; 
        }
        
        
        if(Number(this.Hero.ArmyBlocks[BlockIndex].unit) <= 0)
            return ;
        var ArmyBlock = 
                (new BRHeroCell(
                this.Scene,
        {x: tx + Center.x, y: ty + Center.y, BlockPaddingX: tx, BlockPaddingY: ty},
        {HeroIndex: this.HeroIndex, ArmyType: this.Hero.ArmyBlocks[BlockIndex].armyType, BlockIndex: BlockIndex, ArmyCount: this.Hero.ArmyBlocks[BlockIndex].unit, side: this.Hero.side}
                ))
                .addLargeBlock();
        Round.InFieldHeros[this.HeroIndex].Blocks[`${BlockIndex}`] = ArmyBlock;
        this.HeroCells.add(ArmyBlock.ArmyBlock);
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

    constructor(Scene, Center, CellData) {
        this.Center = Center;
        this.HeroIndex = CellData.HeroIndex;
        this.Scene = Scene;
        this.ArmyType = CellData.ArmyType;
        this.BlockIndex = CellData.BlockIndex;
        BRHeroCell.BlockPlaces[`${CellData.HeroIndex}.${CellData.BlockIndex}`] = this;
        this.ArmyCount = CellData.ArmyCount;
        this.ArmySide = CellData.side;

    }
    blockCountColor(){
        if(this.ArmyCount <= 999)
            return "#ffffff";
        if(this.ArmyCount <= 9999)
            return "#a7e328";
        if(this.ArmyCount <= 99999)
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
            delay: function (target, targetKey, value, targetIndex, totalTargets, tween) {
                return This.HeroIndex*1500;
            }
        });
    }
    addLargeBlock() {
        
        if(Number(this.ArmyCount) === 0)
            return this;
       
        var mapWidth = 5;
        var mapHeight = 4;

        var tileWidthHalf = 15;
        var tileHeightHalf = 7;
        var ArmyType = this.ArmyType;

        if (ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyB
                || ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyE
                || ArmyType === Elkaisar.BattelReplay.ArmyTyps.ArmyF) {
            mapWidth = 4;
            mapHeight = 3;
            tileWidthHalf = 15;
            tileHeightHalf = 7;

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
                Target = blocks.create(tx + This.Scene.scale.width / 2, This.Scene.scale.height + 50, Elkaisar.BattelReplay.TroopsAnimation[this.ArmyType], this.ArmySide*4).setDepth(this.Center.y + ty);
                
                this.tweenBlock(Target, tx, ty);

            }
        }

        
        Phaser.Actions.Call(blocks.getChildren(), function (One){
           if(One.type == "Sprite")
                One.play(`Idle.${This.ArmyType}.Side.${This.ArmySide}`);

        });

        var ArmyCount = this.Scene.add.text(
                This.Scene.scale.width / 2,
        This.Scene.scale.height + 60, this.ArmyCount, {color: this.blockCountColor(), stroke: '#000000', strokeThickness: 3, fontStyle: "bold", align: "center", fontSize: 14}).setDepth(500 + ty);
        blocks.add(ArmyCount);
        this.tweenBlock(ArmyCount, 0, 10);
        this.ArmyBlock = blocks;
        this.ArmyAmount = ArmyCount;
        return this;
    }
    kill(Amount){
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
            duration: 2000,
            onComplete: function (e) {
                Text.destroy();
                This.kill(Amount);
            }
        });

        return this;
    }

    killWithBreak(Amount) {
        var Text = this.Scene.add.bitmapText(this.Center.x, this.Center.y + 25, 'desyrel', "#150", 20).setDepth(1000);
        var This = this;
        this.Scene.tweens.add({
            targets: Text,
            y: this.Center.y - 50,
            scale: 2,
            ease: 'linear',
            duration: 2000,
            onComplete: function (e) {
                Text.destroy();
                This.kill(Amount);
            }
        });

        return this;
    }

    killWithStrike(Amount) {
        var Text = this.Scene.add.bitmapText(this.Center.x, this.Center.y + 25, 'desyrel-pink', "*150", 20).setDepth(1000);
        var This = this;
        this.Scene.tweens.add({
            targets: Text,
            y: this.Center.y - 50,
            scale: 2,
            ease: 'linear',
            duration: 2000,
            onComplete: function (e) {
                Text.destroy();
                This.kill(Amount);
            }
        });
        return this;
    }

    startAttack(HeroIndex, CellIndex, AttackOrderIndex, AttackData) {
        
        if(!Round.InFieldHeros[HeroIndex])
            return console.log("Error Hero Index" + HeroIndex)
        if(!Round.InFieldHeros[HeroIndex].Blocks[CellIndex])
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
                return AttackOrderIndex * 1500;
            },
            onComplete: function (e) {
                Attack.destroy();
                
                Block.BlockDead(AttackData.AmountDead);
                if(AttackData.attackType == Elkaisar.Config.AttackTypeNorm)
                    Block.killFromCell(AttackData.AmountDead);
                else if(AttackData.attackType == Elkaisar.Config.AttackTypeStrike)
                    Block.killWithStrike(AttackData.AmountDead);
                else if(AttackData.attackType == Elkaisar.Config.AttackTypeBreak)
                    Block.killWithBreak(AttackData.AmountDead);
            },
            onStart: function (){
                Attack.setVisible(true);
                Phaser.Actions.Call(This.ArmyBlock.getChildren(), function (One){
                    if(One.type == "Sprite")
                         One.play(`Attack.${This.ArmyType}.Side.${This.ArmySide}`);
                         One.on('animationcomplete', function(currentAnim, currentFramee, sprite){
                             One.play(`Idle.${This.ArmyType}.Side.${This.ArmySide}`)
                         });

                 });
                
            }
        });

    }

    BlockDead() {
        if(this.ArmyCount === 0)
            return this;
        var This = this;
        var Sols = this.ArmyBlock.getChildren();
        var Sol;
        Phaser.Actions.Call(Sols, function (Sol){
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





