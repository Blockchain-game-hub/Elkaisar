exports.WorldUnit = {
    
    isBarrary:function (type){
        
        if((type <= 16 || (type >= 21 && type <= 29)) && Number(type) !== 0  ){
            return true;
        }
        return false;
        
    },
    isRiver:function (type){
        
        if(type <= 16  && Number(type) !== 0  ){
            return true;
        }
        return false;
        
    },
    isEmpty: function (type){
        
        if(Number(type) === 0 ){
            return true;
        }
        return false;
        
    },
    isCity: function (type){
        
        if(type >=17 && type <=20 && Number(type) !== 0){
            
            return true;
            
        }
        return false;
        
    },
    isMountain: function (type){
        
       if(type >=21 && type <=23 && Number(type) !== 0){
            
            return true;
            
        }
        return false; 
        
    },
    isDesert: function (type){
        
       if(type >=24 && type <=26 && Number(type) !== 0){
            
            return true;
            
        }
        return false; 
        
    },
    isWood: function (type){
        
       if(type >=27 && type <=29 &&  Number(type) !== 0){
            
            return true;
            
        }
        return false; 
        
    },
    isMonawrat: function (type){
        return (Number(type)  === 30 );
        
    },
    isCamp:function (type){
        
         return (Number(type)  === 31 );
        
    },
    isFrontSquad:function (type){
        return (Number(type)  === 32 );
    },
    isFrontBand:function (type){
        return (Number(type)  === 33 );
    },
    isFrontSquadron:function (type){
        return (Number(type)  === 34 );
    },
    isFrontDivision:function (type){
        return (Number(type)  === 35 );
    },
    isLightSquad:function (type){
         return (Number(type)  === 36 );
    },
    isSeaCity: function (unitType){
         return (Number(unitType) >= Elkaisar.Config.WUT_SEA_CITY_1 && unitType <= Elkaisar.Config.WUT_SEA_CITY_6);
    },
    isLightBand:function (type){
        if(Number(type)  === 37 ){
            
            return true;
            
        }
        return false;
    },
    isLightSquadron:function (type){
        if(Number(type)  === 38 ){
            
            return true;
            
        }
        return false;
    },
    isLightDivision:function (type){
        if(Number(type)  === 39 ){
            
            return true;
            
        }
        return false;
    },
    isHeavySquad:function (type){
        if(Number(type)  === 40){
            
            return true;
            
        }
        return false;
    },
    isHeavyBand:function (type){
        if(Number(type)  === 41 ){
            
            return true;
            
        }
        return false;
    },
    isHeavySquadron:function (type){
        if(Number(type)  === 42 ){
            
            return true;
            
        }
        return false;
    },
    isHeavyDivision:function (type){
        if(Number(type)  === 43 ){
            
            return true;
            
        }
        return false;
    },
    isGuardSquad:function (type){
        if(Number(type)  === 44 ){
            
            return true;
            
        }
        return false;
    },
    isGuardBand:function (type){
        if(Number(type)  === 45 ){
            
            return true;
            
        }
        return false;
    },
    isGuardSquadron:function (type){
        if(Number(type)  === 46 ){
            
            return true;
            
        }
        return false;
    },
    isGuardDivision:function (type){
        if(Number(type)  === 47){
            
            return true;
            
        }
        return false;
    },
    isBraveThunder:function (type){
        if(Number(type)  === 48){
            
            return true;
            
        }
        return false;
    },
    isAsianSquads:function (type){
        if(Number(type) >= 32 && Number(type) <= 48){
            return true;
        }
        return false;
    },
    isGangs: function (type){
        return(Number(type) === 49);
    },
    isMuggers: function (type){
        return (Number(type) === 50);
    },
    isThiefs: function (type){
        return (Number(type) === 51);
    },
    isGangStar: function (type){
        return (Number(type) === 49 || Number(type) === 50 || Number(type) === 51);
    },
    
    isCarthageGang: function (type){
        return Number(type) === 52 ;
    },
    isCarthageTeams: function (type){
        return Number(type) === 53 ;
    },
    isCarthageRebals: function (type){
        return Number(type) === 54 ;
    },
    isCarthageForces: function (type){
        return Number(type) === 55 ;
    },
    isCarthageCapital: function (type){
        return Number(type) === 56 ;
    },
    isCarthagianArmies: function (type){
        return (Number(type) >= 52 && Number(type) <= 56);
    },
    isArmyCapital: function (type){
        return (Number(type) >= 100 && Number(type) <= 105);
    },
    isArmyCapitalA: function (type){
        return (Number(type) === 100);
    },
    isArmyCapitalB: function (type){
        return (Number(type) === 101);
    },
    isArmyCapitalC: function (type){
        return (Number(type) === 102);
    },
    isArmyCapitalD: function (type){
        return (Number(type) === 103);
    },
    isArmyCapitalE: function (type){
        return (Number(type) === 104);
    },
    isArmyCapitalF: function (type){
        return (Number(type) === 105);
    },
    isArena: function (type){
        return (Number(type) >= 125 && Number(type) <= 127);
    },
    isArenaChallange: function (type){
        return (Number(type) === 125);
    },
    isArenaDeath: function (type){
        return (Number(type) === 126);
    },
    isArenaGuild: function (type){
        return (Number(type) === 127);
    },
    isQueenCity: function (type){
        return (Number(type) === 130 || Number(type) === 131 || Number(type) === 132 );
    },
    isQueenCityS: function (type){
        return (Number(type) === 130);
    },
    isQueenCityM: function (type){
        return (Number(type) === 131);
    },
    isQueenCityH: function (type){
        return (Number(type) === 132);
    },
    isRepelCastle: function (type){
        return (Number(type) === 134 || Number(type) === 135 || Number(type) === 136 );
    },
    isRepelCastleS: function (type){
        return (Number(type) === 134);
    },
    isRepelCastleM: function (type){
        return ( Number(type) === 135 );
    },
    isRepelCastleH: function (type){
        return ( Number(type) === 136 );
    },
    
    isStatueWar: function (type){
        return (Number(type) === 150 || Number(type) === 151 || Number(type) === 152 );
    },
    isStatueWarS: function (type){
        return (Number(type) === 150);
    },
    isStatueWarM: function (type){
        return ( Number(type) === 151 );
    },
    isStatueWarH: function (type){
        return ( Number(type) === 152 );
    },
    isStatueWalf: function (type){
        return (Number(type) === 153 || Number(type) === 154 || Number(type) === 155 );
    },
    isStatueWalfS: function (type){
        return (Number(type) === 153);
    },
    isStatueWalfM: function (type){
        return ( Number(type) === 154 );
    },
    isStatueWalfH: function (type){
        return ( Number(type) === 155 );
    },
    
  
    
    getCampFlage(x , y){

       x = Number(x);
       y = Number(y);
       var $flag = null;
       if(x === 136 && y === 160){
           $flag = "flag_france";
       }else  if(x=== 407 && y === 66){
           $flag = "flag_magul";
       }else  if(x === 106 && y === 19){
           $flag = "flag_england";
       }else  if(x === 392 && y === 213){
           $flag = "flag_macdoni";
       }else  if(x === 266 && y === 245){
           $flag = "flag_roma";
       }else  if(x === 78 && y === 300){
           $flag = "flag_spain";
       }else  if(x === 427 && y === 337){
             $flag = "flag_greek";
       }else  if(x === 316 && y === 450){
           $flag = "flag_egypt";
       }else  if(x === 88 && y === 444){
           $flag = "flag_cartaga";
       }else  if(x === 246 && y === 111){
           $flag = "flag_germany";
       } 

           return $flag;

   }
    
    
};