Elkaisar.BattelReplay.BattelGround = {};


Elkaisar.BattelReplay.Events = {};



Elkaisar.BattelReplay.Events.mouseDownFn = function () {};
Elkaisar.BattelReplay.Events.mouseUpFn = function () {};
Elkaisar.BattelReplay.Events.mouseMoveFn = function () {};
/*
Crafty.defineScene("BattelGround", function () {

    Crafty._floor = "world";
    Crafty.onDragClickable = true;

    // Crafty.bind("ViewportScroll", Elkaisar.World.Map.Scroll);

    Crafty.widthInTile = 35;
    Crafty.heightIntile = 35;






    Crafty.addEvent(this, Crafty.stage.elem, "mousedown", Elkaisar.BattelReplay.Events.mouseDownFn);
    Crafty.addEvent(this, Crafty.stage.elem, "mouseup", Elkaisar.BattelReplay.Events.mouseDownFn);
    Crafty.addEvent(this, Crafty.stage.elem, "mousemove", Elkaisar.BattelReplay.Events.mouseDownFn);





    UnitFloor = Crafty.e("2D, Canvas, BattelGroundSprite").attr({w: Crafty.stage.elem.getBoundingClientRect().width, h: Crafty.stage.elem.getBoundingClientRect().height});
    UnitFloor = Crafty.e("2D, Canvas, WindowFrame").attr({w: Crafty.stage.elem.getBoundingClientRect().width, h: Crafty.stage.elem.getBoundingClientRect().height});


    var Blocks = [];
    var Block;
    var XOffset = 0;
    var Side = Elkaisar.BattelReplay.AttackSides.Defence;
    for (var yCoord = 0; yCoord < 2; yCoord++) {
        for (var xCoord = 0; xCoord < 9; xCoord++) {

            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xCoord][yCoord].Block = Elkaisar.BattelReplay.TroopsBlock.BlockLarge(xCoord % 6 + 1, Side);
            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xCoord][yCoord].ArmyType = xCoord % 6 + 1;
            
            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xCoord][yCoord].Block.tween({
                x: Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xCoord][yCoord].Pos.x,
                y: Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xCoord][yCoord].Pos.y
            }, 2000, "smootherStep");

            //Block.x = Crafty.stage.elem.getBoundingClientRect().width / 3 + xCoord * 90 + yCoord * 90 + 2 * XOffset;
            //Block.y = xCoord * 45 + 150 - yCoord * 45 + XOffset;
        }
    }
    
    

    var BBlock;
    var Side = Elkaisar.BattelReplay.AttackSides.Attack;
    for (var xxCoord = 0; xxCoord < 9; xxCoord++) {
        for (var yyCoord = 0; yyCoord < 2; yyCoord++) {
            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xxCoord][yyCoord].Block = Elkaisar.BattelReplay.TroopsBlock.BlockLarge(xxCoord % 6 + 1, Side);
            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xxCoord][yyCoord].ArmyType = xxCoord % 6 + 1;
            
            Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xxCoord][yyCoord].Block.tween({
                x: Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xxCoord][yyCoord].Pos.x,
                y: Elkaisar.BattelReplay.TroopsBlock.Blocks[Side][xxCoord][yyCoord].Pos.y
            }, 2000, "smootherStep");
        }

    }

}, function () {
    Crafty.removeEvent(this, Crafty.stage.elem, "mousedown", Elkaisar.BattelReplay.Events.mouseDownFn);
    Crafty.removeEvent(this, Crafty.stage.elem, "mouseup", Elkaisar.BattelReplay.Events.mouseDownFn);
    Crafty.removeEvent(this, Crafty.stage.elem, "mousemove", Elkaisar.BattelReplay.Events.mouseDownFn);
    //Crafty.unbind("ViewportScroll", Elkaisar.World.Map.Scroll);
});

*/

