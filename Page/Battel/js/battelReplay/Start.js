

$(document).ready(function () {
    Elkaisar.Config.Game = {
        type: Phaser.AUTO,
        width: 1000,
        height: 600,
        scale: {
            mode: Phaser.Scale.NONE ,
            autoCenter: Phaser.Scale.CENTER_BOTH
        },
        physics: {
            default: 'arcade',
            arcade: {
                gravity: {y: 200}
            }
        },
        scene: [BattelReplayScene]
    };
    ElkaisarBR.Game = new Phaser.Game(Elkaisar.Config.Game);
});

