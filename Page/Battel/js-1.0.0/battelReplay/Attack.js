Elkaisar.Attack = {};

Count = 0;
Elkaisar.Attack.Start = function (CellPlace, ArmyType) {
    for (var Side in Elkaisar.BattelReplay.TroopsBlock.Blocks) {
        for (var XCoord in Elkaisar.BattelReplay.TroopsBlock.Blocks[Side]) {
            for (var yCoord in Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][XCoord]) {
                 var Cell = Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][XCoord][yCoord];
                 
                Elkaisar.BattelReplay
                        .TroopsBlock.Blocks[Side][XCoord][yCoord].Block
                        .attach(Crafty.e(`2D, Canvas, ${Elkaisar.BattelReplay.ArmyTypsWeapons[Cell.ArmyType]}, Tween, Collision, WiredHitBox`)
                        .collision([74,16, 3,58, 43,78, 115,37])
                        .attr({x: Cell.Pos.x + 50, y: Cell.Pos.y + 0, z: 150})
                        .tween({
                            x: Math.random()*500,
                            y: Math.random()*500
                        }, 2000, "smootherStep").bind("TweenEnd", function (){
                            console.log(this.destroy());
                        }));
                ;
            }
        }
    }
};




Elkaisar.Attack.WriteAttackEffect = function (CellPlace, Effect, AttackType){
    for (var Side in Elkaisar.BattelReplay.TroopsBlock.Blocks) {
        for (var XCoord in Elkaisar.BattelReplay.TroopsBlock.Blocks[Side]) {
            for (var yCoord in Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][XCoord]) {
                var Cell = Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][XCoord][yCoord];
                Crafty.e(`2D, DOM, Text, Tween, AttackEffect`)
                        .text("100").textAlign("center").textFont({weight: 'bold', size: "0.5em"}).textColor("red")
                       .attr({x: Cell.Pos.x + 60, y: Cell.Pos.y + 40 , z: 150, h: 30, w: 100})
                       .tween({
                           y: Cell.Pos.y - 10,
                           x: Cell.Pos.x + 75,
                           alpha: 0.5
                       }, 2000, "smootherStep").bind("TweenEnd", function (){
                           this.destroy();
                       }).bind("UpdateFrame", function (){
                           var TaxtSize = parseFloat(this._textFont.size);
                           this.css({"font-size" : (TaxtSize+.015) + "em"});
                       });
                ;
            }
        }
    }
};



/*
setInterval(function () {
Elkaisar.Attack.WriteAttackEffect()
}, 2000);*/