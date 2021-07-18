class AHeroArmy{
    
    Parm;
    idPlayer;
    constructor(idPlayer, Url){
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }
    
    
    async transArmyFromHeroToCity(){
        
        const idHero    = Elkaisar.Base.validateId(this.Parm["idHero"]);
        const ArmyPlace = Elkaisar.Base.validateGameNames(this.Parm["ArmyPlace"]);
        const amount    = Elkaisar.Base.validateId(this.Parm["amount"]);
        
        const Hero = await  Elkaisar.DB.ASelectFrom("hero.id_city, hero.in_city, hero_army.*", "hero Join hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = ? AND hero.id_player = ?", [idHero,this.idPlayer]);
        
        if(!Array.isArray(Hero) || Hero.length < 1)
            return {"state": "error_0"};
        if(!Hero[0][`${ArmyPlace}_num`])
            return {"state": "error_1"};
        if(Hero[0][`${ArmyPlace}_type`] < 1 || Hero[0][`${ArmyPlace}_type`] > 6)
            return {"state" :"error_2"};
        if(amount <= 0)
            return {"state": "error_3"};
        if(Hero[0][`${ArmyPlace}_num`] < amount)
            return {"state": "error_4"};
        if(Hero[0]["in_city"] != Elkaisar.Config.HERO_IN_CITY)
            return {"state": "error_5"};
        const CityType = Elkaisar.Config.CArmy.ArmyCityPlace[Hero[0][`${ArmyPlace}_type`]];
        await Elkaisar.DB.AUpdate("`"+CityType+"` = `"+CityType+"` + ?",  "city",  "id_city = ? AND id_player = ?", [amount, Hero[0]["id_city"], this.idPlayer]);
        
        if(Hero[0][`${ArmyPlace}_num`] <= amount)
            await Elkaisar.DB.AUpdate("`"+ArmyPlace+"_type` = 0, `"+ArmyPlace+"_num` = 0",  "hero_army",  "id_hero = ?", [idHero]);
        else 
           await Elkaisar.DB.AUpdate("`"+ArmyPlace+"_num` = `"+ArmyPlace+"_num` - ?",  "hero_army",  "id_hero = ?", [amount, idHero]);
        
        Elkaisar.Lib.LSaveState.saveCityState(Hero[0]["id_city"]);
        
        return {
            "state" : "ok",
            "HeroArmy": (await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHero]))[0],
            "City" : (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [Hero[0]["id_city"]]))[0]
        };
    }
    
    async  transArmyFromCityToHero()
    {
        const idHero    = Elkaisar.Base.validateId(this.Parm["idHero"]);
        const ArmyPlace = Elkaisar.Base.validateGameNames(this.Parm["ArmyPlace"]);
        const ArmyType  = Elkaisar.Base.validateGameNames(this.Parm["ArmyType"]);
        const amount    = Elkaisar.Base.validateId(this.Parm["amount"]);
        
        const Hero = await  Elkaisar.DB.ASelectFrom("hero.id_city, hero.in_city, hero_army.*", "hero Join hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = ? AND hero.id_player = ?", [idHero, this.idPlayer]);
        const CityArmy = (await  (Elkaisar.DB.ASelectFrom(ArmyType, "city", "id_city = ?", [ Hero[0]["id_city"]])))[0][ArmyType];
        const EmptyPlaces = await  Elkaisar.Lib.LHero.emptyPlacesSize(this.idPlayer, idHero);
        const OnArmyUnitSize =  Elkaisar.Config.CArmy.ArmyCap[Elkaisar.Config.CArmy.ArmyCityToArmyHero[ArmyType]];
        
        if(!Array.isArray(Hero) || Hero.length < 1)
            return {"state": "error_0"};
        if(!Hero[0].hasOwnProperty(`${ArmyPlace}_num`))
            return {"state": "error_1"};
        if(Hero[0][`${ArmyPlace}_type`] != 0 && ArmyType != Elkaisar.Config.CArmy.ArmyCityPlace[Hero[0][`${ArmyPlace}_type`]])
            return {"state" :"error_2"};
        if(amount <= 0)
            return {"state": "error_3"};
        if(EmptyPlaces < amount*OnArmyUnitSize)
            return {"state": "error_4"};
        if(Hero[0]["in_city"] != Elkaisar.Config.HERO_IN_CITY)
            return {"state": "error_5"};
         if(CityArmy < amount)
            return {"state": "error_6"};
        
        await  Elkaisar.DB.AUpdate(`${ArmyType} = ${ArmyType} - ?`, "city", "id_city = ? AND id_player = ?", [amount, Hero[0]["id_city"],this.idPlayer]);
        await Elkaisar.DB.AUpdate(`${ArmyPlace}_type = ?, ${ArmyPlace}_num = ${ArmyPlace}_num + ?`, "hero_army", "id_hero = ? ", [Elkaisar.Config.CArmy.ArmyCityToArmyHero[ArmyType], amount, idHero ]);
        
        Elkaisar.Lib.LSaveState.saveCityState(Hero[0]["id_city"]);
        
        return {
            "state" : "ok",
            "HeroArmy": (await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHero]))[0],
            "City" : (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [Hero[0]["id_city"]]))[0]
        };
    }
    
    
    async  transArmyFromHeroToHero()
    {
        
        const idHeroFrom    = Elkaisar.Base.validateId(this.Parm["idHeroFrom"]);
        const idHeroTo      = Elkaisar.Base.validateId(this.Parm["idHeroTo"]);
        const ArmyPlaceTo   = Elkaisar.Base.validateGameNames(this.Parm["ArmyPlaceTo"]);
        const ArmyPlaceFrom = Elkaisar.Base.validateGameNames(this.Parm["ArmyPlaceFrom"]);
        const amount        = Elkaisar.Base.validateId(this.Parm["amount"]);
        const HeroFrom      = await Elkaisar.DB.ASelectFrom("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = ? AND hero.id_player = ?", [ idHeroFrom, this.idPlayer]);
        const HeroTo        = await Elkaisar.DB.ASelectFrom("hero.id_city, hero.in_city, hero_army.*", "hero JOIN hero_army ON hero.id_hero = hero_army.id_hero", "hero.id_hero = ? AND hero.id_player = ?", [ idHeroTo, this.idPlayer]);
       
       
       if(!Array.isArray(HeroFrom) || !Array.isArray(HeroTo) || HeroTo.length < 1 || HeroFrom.length < 1)
           return {"state": "error_0"};
       if(!HeroTo[0].hasOwnProperty(ArmyPlaceTo+"_num") || !HeroFrom[0].hasOwnProperty(ArmyPlaceFrom+"_num"))
            return {"state": "error_1"};
        if(HeroTo[0][(ArmyPlaceTo+"_type")] != 0 && HeroFrom[0][(ArmyPlaceFrom+"_type")] != HeroTo[0][(ArmyPlaceTo+"_type")])
            return {"state": "error_2"};
        if(amount <= 0 || amount > HeroFrom[0][(ArmyPlaceFrom+"_num")])
            return {"state": "error_3"};
        if(Elkaisar.Lib.LHero.emptyPlacesSize(this.idPlayer, idHeroTo) < amount*Elkaisar.Config.CArmy.ArmyCap[HeroFrom[0][(ArmyPlaceFrom+"_type")]])
            return {"state": "error_4"};
        if(HeroFrom[0]["in_city"] != Elkaisar.Config.HERO_IN_CITY || HeroTo[0]["in_city"] != Elkaisar.Config.HERO_IN_CITY)
            return {"state": "error_5"};
        if(HeroFrom[0]["id_city"] != HeroTo[0]["id_city"])
            return {"state": "error_6"};
        
        if(HeroFrom[0][(ArmyPlaceFrom+"_num")] == amount)
            await Elkaisar.DB.AUpdate("`"+ArmyPlaceFrom+"_type` = ?, `"+ArmyPlaceFrom+"_num` = `"+ArmyPlaceFrom+"_num` - ?", "hero_army", "id_hero = ?", [0, amount, idHeroFrom]);
        else 
            await Elkaisar.DB.AUpdate("`"+ArmyPlaceFrom+"_type` = ?, `"+ArmyPlaceFrom+"_num` = `"+ArmyPlaceFrom+"_num` - ?", "hero_army", "id_hero = ?", [HeroFrom[0][(ArmyPlaceFrom+"_type")], amount, idHeroFrom]);
        
       await Elkaisar.DB.AUpdate ("`"+ArmyPlaceTo+"_type`   = ?, `"+ArmyPlaceTo+"_num`   = `"+ArmyPlaceTo+"_num`   + ?", "hero_army", "id_hero = ?", [HeroFrom[0][(ArmyPlaceFrom+"_type")], amount, idHeroTo]);
        return {
            "state"        : "ok",
            "HeroArmyFrom" : (await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHeroFrom]))[0],
            "HeroArmyTo"   : (await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHeroTo]))[0]
        };
    }
    
}

module.exports = AHeroArmy;