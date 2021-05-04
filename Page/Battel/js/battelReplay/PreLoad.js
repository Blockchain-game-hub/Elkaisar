
//Crafty.init();



Elkaisar.BattelReplay.ArmyTyps = {
    ArmyA: 1,
    ArmyB: 2,
    ArmyC: 3,
    ArmyD: 4,
    ArmyE: 5,
    ArmyF: 6
};

Elkaisar.BattelReplay.ArmyTypsWeapons = {
    [Elkaisar.BattelReplay.ArmyTyps.ArmyA]: 1,
    [Elkaisar.BattelReplay.ArmyTyps.ArmyB]: 5,
    [Elkaisar.BattelReplay.ArmyTyps.ArmyC]: 2,
    [Elkaisar.BattelReplay.ArmyTyps.ArmyD]: 0,
    [Elkaisar.BattelReplay.ArmyTyps.ArmyE]: 2,
    [Elkaisar.BattelReplay.ArmyTyps.ArmyF]: 3
};


Elkaisar.BattelReplay.DeadSol = {
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyA}`]: 1,
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyB}`]: 6,
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyC}`]: 4,
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyD}`]: 2,
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyE}`]: 8,
    [`0.${Elkaisar.BattelReplay.ArmyTyps.ArmyF}`]: 9,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyA}`]: 0,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyB}`]: 7,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyC}`]: 5,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyD}`]: 3,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyE}`]: 9,
    [`1.${Elkaisar.BattelReplay.ArmyTyps.ArmyF}`]: 11
};


Elkaisar.BattelReplay.AttackSides = {
    Defence: 0,
    Attack: 1
};


Elkaisar.BattelReplay.TroopsAnimation = {
    [Elkaisar.BattelReplay.ArmyTyps.ArmyA]: "TroopArmyA",
    [Elkaisar.BattelReplay.ArmyTyps.ArmyB]: "TroopArmyB",
    [Elkaisar.BattelReplay.ArmyTyps.ArmyC]: "TroopArmyC",
    [Elkaisar.BattelReplay.ArmyTyps.ArmyD]: "TroopArmyD",
    [Elkaisar.BattelReplay.ArmyTyps.ArmyE]: "TroopArmyE",
    [Elkaisar.BattelReplay.ArmyTyps.ArmyF]: "TroopArmyF"
};


Elkaisar.BattelReplay.TroopsFrams = {
    "Idle": {
        [Elkaisar.BattelReplay.AttackSides.Defence]: [[0, 0], [1, 0], [2, 0], [3, 0]],
        [Elkaisar.BattelReplay.AttackSides.Attack]: [[4, 0], [5, 0], [6, 0], [7, 0]]
    },
    "Walk": {
        [Elkaisar.BattelReplay.AttackSides.Defence]: [[0, 1], [1, 1], [2, 1], [3, 1], [4, 1], [5, 1], [6, 1], [7, 1]],
        [Elkaisar.BattelReplay.AttackSides.Attack]: [[0, 2], [1, 2], [2, 2], [3, 2], [4, 2], [5, 2], [6, 2], [7, 2]]
    },

    "Melee": {
        [Elkaisar.BattelReplay.AttackSides.Defence]: [[0, 3], [1, 3], [2, 3], [3, 3], [4, 3], [5, 3], [6, 3], [7, 3], [8, 3], [9, 3]],
        [Elkaisar.BattelReplay.AttackSides.Attack]: [[0, 4], [1, 4], [2, 4], [3, 4], [4, 4], [5, 4], [6, 4], [7, 4], [8, 4], [9, 4]]
    }
};

Elkaisar.BattelReplay.Loading = function () {};

Elkaisar.BattelReplay.Loaded = function () {
    Crafty.enterScene("BattelGround");
};

Elkaisar.BattelReplay.LoadingErr = function () {};

/*Crafty.load(
 Elkaisar.BattelReplay.ResToLoad,
 Elkaisar.BattelReplay.Loaded,
 Elkaisar.BattelReplay.Loading,
 Elkaisar.BattelReplay.LoadingErr
 );*/



class BattelReplayScene extends Phaser.Scene
{
    static Scene;
    constructor()
    {
        super();
        BattelReplayScene.Scene = this;


    }

    configAnims() {
        this.anims.create({
            key: `Idle.1.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyA', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.2.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyB', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.3.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyC', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.4.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyD', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.5.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyE', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.6.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyF', {frames: [0, 1, 2, 3]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.1.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyA', {frames: [4, 5, 6, 7]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.2.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyB', {frames: [4, 5, 6, 7]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.3.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyC', {frames: [4, 5, 6, 7]}),
            frameRate: 5, repeat: -1
        });
        this.anims.create({
            key: `Idle.4.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyD', {frames: [4, 5, 6, 7]}),
            frameRate: 5
        });
        this.anims.create({
            key: `Idle.5.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyE', {frames: [4, 5, 6, 7]}),
            frameRate: 5
        });
        this.anims.create({
            key: `Idle.6.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyF', {frames: [4, 5, 6, 7]}),
            frameRate: 5
        });
        this.anims.create({
            key: `Attack.1.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyA', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.2.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyB', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.3.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyC', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.4.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyD', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.5.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyE', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.6.Side.${Elkaisar.Config.SideDefence}`,
            frames: this.anims.generateFrameNumbers('TroopArmyF', {frames: [30, 31, 32, 33, 34, 35, 36, 37, 38, 39]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.1.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyA', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.2.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyB', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.3.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyC', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.4.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyD', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 0
        });
        this.anims.create({
            key: `Attack.5.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyE', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 10
        });
        this.anims.create({
            key: `Attack.6.Side.${Elkaisar.Config.SideAttack}`,
            frames: this.anims.generateFrameNumbers('TroopArmyF', {frames: [40, 41, 42, 43, 44, 45, 46, 47, 48, 49]}),
            frameRate: 10
        });
    }

    preload() {



        var width = this.cameras.main.width;
        var height = this.cameras.main.height;


        var percentText = this.make.text({
            x: width / 2,
            y: height / 2 + 50,
            text: '0%',
            style: {
                font: '32px monospace',
                fill: '#ffffff',
                fontWeight: "bold"
            }
        });
        percentText.setOrigin(0.5, 0.5);



        this.load.on('progress', function (value) {
            percentText.setText(parseInt(value * 100) + '%');
        });


        var This = this;
        var ImageBg;
        this.load.image('LoadingBg', BASE_ASSET_BATH + '/images/BattelReplay/bg_en.png').on("complete", function () {
            ImageBg = This.add.image(This.scale.width / 2, 300, "LoadingBg").setDisplaySize(720, 325);
        });

        this.load.image('BattelGroundSprite', BASE_ASSET_BATH + '/images/BattelReplay/field.jpg');
        this.load.image('windowFrame', BASE_ASSET_BATH + '/images/BattelReplay/windowFrame.png');
        this.load.image('windowFrame', BASE_ASSET_BATH + '/images/BattelReplay/windowFrame.png');
        
        this.load.image('HeroFaceA1', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA1.png');
        this.load.image('HeroFaceA2', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA2.png');
        this.load.image('HeroFaceA3', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA3.png');
        this.load.image('HeroFaceA4', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA4.png');
        this.load.image('HeroFaceA5', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA5.png');
        this.load.image('HeroFaceA6', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA6.png');
        this.load.image('HeroFaceA7', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA7.png');
        this.load.image('HeroFaceA8', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA8.png');
        this.load.image('HeroFaceA9', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA9.png');
        this.load.image('HeroFaceA10', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceA10.png');
        this.load.image('HeroFaceB1', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB1.png');
        this.load.image('HeroFaceB2', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB2.png');
        this.load.image('HeroFaceB3', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB3.png');
        this.load.image('HeroFaceB4', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB4.png');
        this.load.image('HeroFaceB5', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB5.png');
        this.load.image('HeroFaceB6', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB6.png');
        this.load.image('HeroFaceB7', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB7.png');
        this.load.image('HeroFaceB8', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB8.png');
        this.load.image('HeroFaceB9', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB9.png');
        this.load.image('HeroFaceB10', BASE_ASSET_BATH + '/images/BattelReplay/BattelReport/faceB10.png');
        
        this.load.spritesheet('TroopArmyA', BASE_ASSET_BATH + '/images/BattelReplay/troop_triarii.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('TroopArmyB', BASE_ASSET_BATH + '/images/BattelReplay/troop_cavalry.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('TroopArmyC', BASE_ASSET_BATH + '/images/BattelReplay/troop_cohort.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('TroopArmyD', BASE_ASSET_BATH + '/images/BattelReplay/troop_archers.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('TroopArmyE', BASE_ASSET_BATH + '/images/BattelReplay/troop_ballistas.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('TroopArmyF', BASE_ASSET_BATH + '/images/BattelReplay/troop_onagers.png', {frameWidth: 100, frameHeight: 100});
        this.load.bitmapFont('desyrel', BASE_ASSET_BATH + '/assets/fonts/bitmap/desyrel.png', BASE_ASSET_BATH + '/assets/fonts/bitmap/desyrel.xml');
        this.load.bitmapFont('desyrel-pink', BASE_ASSET_BATH + '/assets/fonts/bitmap/desyrel-pink.png', BASE_ASSET_BATH + '/assets/fonts/bitmap/desyrel-pink.xml');
        this.load.bitmapFont('shortStack', BASE_ASSET_BATH + '/assets/fonts/bitmap/shortStack.png', BASE_ASSET_BATH + '/assets/fonts/bitmap/shortStack.xml');
        this.load.spritesheet('ArmyWeapons', BASE_ASSET_BATH + '/images/BattelReplay/others_weapon.png', {frameWidth: 100, frameHeight: 100});
        this.load.spritesheet('DeadSol', BASE_ASSET_BATH + '/images/BattelReplay/others_corpse.png', {frameWidth: 100, frameHeight: 100});
        var This = this;
        this.load.on('complete', function () {
            percentText.destroy();
            ImageBg.destroy();
            Elkaisar.BattelReplay.drawControllPanel();
            This.configAnims();
            (new BattelReplay(BattelReplayData)).startBattelShow();
        });


    }
    create() {
        this.add.image(0, 0, "BattelGroundSprite").setOrigin(0, 0).setDisplaySize(this.scale.width, this.scale.height);
        this.add.image(0, 0, "windowFrame").setOrigin(0, 0).setDisplaySize(this.scale.width, this.scale.height).setDepth(1000);
    }
    update()
    {
    }
    
    
}



Elkaisar.BattelReplay.drawControllPanel = function (){
   
   var Win = $("#BattelReplayCanvas canvas");
    $("#PlayControlBoard").width(Win.width());
    $("#PlayControlBoard").css({top: Win.height() - $("#PlayControlBoard").height(), left: Win.offset().left, display: "flex"});
    
    $("#BloodBar").css({display: "flex", left: Win.offset().left});
    $("#BloodBar").width(Win.width());
    $("#RightSideData").css({right: Win.offset().left + 3, display: "block"});
    
};




