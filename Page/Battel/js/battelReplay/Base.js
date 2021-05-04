var Elkaisar = {};
Carfty = {};
Elkaisar.Config = {};
var ElkaisarBR = {};
Elkaisar.Config.SideAttack = 1;
Elkaisar.Config.SideDefence = 0;

Elkaisar.Config.AttackTypeNorm = 0;
Elkaisar.Config.AttackTypeBreak = 1;
Elkaisar.Config.AttackTypeStrike = 2;
Elkaisar.Config.AttackTypeStrikeAndBreak = 3;



Elkaisar.Config.Delay = {};
Elkaisar.Config.Delay.Attack      = 1000;
Elkaisar.Config.Delay.PosHero     = 2000;
Elkaisar.Config.Delay.Base        = 2000;
Elkaisar.Config.Delay.HeroShow    = 500;
Elkaisar.BattelReplay = {};


Elkaisar.getHeroData = function (idHero){
    for(var iii in BattelReplayData.Heros){
        if(BattelReplayData.Heros[iii].Hero.idHero == idHero)
            return BattelReplayData.Heros[iii];
    }
    
    return false;
};

Elkaisar.getPlayerData = function (idPlayer){
    for(var iii in BattelReplayData.Players){
        if(BattelReplayData.Players[iii].Player.idPlayer == idPlayer)
            return BattelReplayData.Players[iii];
    }
    
    return false;
};











