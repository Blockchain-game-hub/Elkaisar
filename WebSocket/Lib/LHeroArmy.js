class LHeroArmy {

    HArmy;
    static async  filledPlacesSize(idHero)
    {
        const HeroArmy = await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHero]);
        if (!Array.isArray(HeroArmy) || HeroArmy.length < 1)
            return 0;
        let filledPlaces = 0;
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["f_1_type"]] * HeroArmy[0]["f_1_num"];
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["f_2_type"]] * HeroArmy[0]["f_2_num"];
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["f_3_type"]] * HeroArmy[0]["f_3_num"];
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["b_1_type"]] * HeroArmy[0]["b_1_num"];
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["b_2_type"]] * HeroArmy[0]["b_2_num"];
        filledPlaces += Elkaisar.Config.CArmy.ArmyCap[HeroArmy[0]["b_3_type"]] * HeroArmy[0]["b_3_num"];

        return filledPlaces;
    }

    static async  heroFullCap(idPlayer, idHero)
    {


        const Hero = await Elkaisar.DB.ASelectFrom("hero.point_a, hero.point_a_plus, hero_medal.*, hero.id_city", "hero JOIN hero_medal ON hero.id_hero = hero_medal.id_hero", "hero.id_hero = ?", [idHero]);
        const PlayerEdu = await Elkaisar.DB.ASelectFrom("leader", "player_edu", "id_player = ?", [idPlayer]);
        const AcadmyLvl = (await Elkaisar.Lib.LCityBuilding.buildingWithHeighestLvl(Hero[0]["id_city"], Elkaisar.Config.CITY_BUILDING_ACADEMY))["Lvl"];

        if (!Array.isArray(Hero) || Hero.length < 1)
            return 0;
        if (!Array.isArray(PlayerEdu) || PlayerEdu.length < 1)
            PlayerEdu = [{"leader": 0}];
        const now = Date.now() / 1000;

        const baseCap = Elkaisar.Config.HERO_SWAY_POINT_EFF * (Number(Hero[0]["point_a"]) + Number(Hero[0]["point_a_plus"])) + Elkaisar.Config.HERO_BASE_CAP;
        const afterEduEff = Math.min(AcadmyLvl, PlayerEdu[0]["leader"]) * baseCap * Elkaisar.Config.HERO_EDU_LVL_EFF_CAP;
        const afterCiceroEff = Hero[0]["medal_ceasro"] > now ? baseCap * Elkaisar.Config.HERO_MEDAL_EFF_CAP : 0;
        const afterCeaserMarqueEff = Hero[0]["ceaser_eagle"] > now ? baseCap * Elkaisar.Config.HERO_EAGLE_EFF_CAP : 0;
        
        

        return  baseCap + afterEduEff + afterCiceroEff + afterCeaserMarqueEff;
    }

    static async  emptyPlacesSize(idPlayer, idHero) {
        return (await LHeroArmy.heroFullCap(idPlayer, idHero)) - (await LHeroArmy.filledPlacesSize(idHero));
    }


    async isCarringArmy(idHero)
    {
        const Army = await Elkaisar.DB.ASelectFrom("*", "hero_army", "id_hero = ?", [idHero]);
        if(!Array.isArray(Army) || !Army[0])
            return false;
        this.HArmy = Army
        if(
               Army[0]["f_1_num"] > 0 || Army[0]["f_2_num"] > 0 || Army[0]["f_3_num"] > 0 
            || Army[0]["b_1_num"] > 0 || Army[0]["b_2_num"] > 0 || Army[0]["b_3_num"] > 0  
            )
        return Army[0];
        
        return false;
        
    }

    isTheSame(arymType) {
        
        if (!this.HArmy[0]) return false;
        if (this.HArmy[0]["f_1_type"] != 0 && this.HArmy[0]["f_1_type"] != arymType)      return false;
        else if (this.HArmy[0]["f_2_type"] != 0 && this.HArmy[0]["f_2_type"] != arymType) return false;
        else if (this.HArmy[0]["f_3_type"] != 0 && this.HArmy[0]["f_3_type"] != arymType) return false;
        else if (this.HArmy[0]["b_1_type"] != 0 && this.HArmy[0]["b_1_type"] != arymType) return false;
        else if (this.HArmy[0]["b_2_type"] != 0 && this.HArmy[0]["b_2_type"] != arymType) return false;
        else if (this.HArmy[0]["b_3_type"] != 0 && this.HArmy[0]["b_3_type"] != arymType) return false;
        return true;
    }

    heroCanAttack(unitType) {

        if (Elkaisar.Lib.LWorldUnit.isArmyCapital(unitType)) {

            const capitalArmy = {
               [Elkaisar.Config.WUT_ARMY_CAPITAL_A] : Elkaisar.Config.ARMY_A,
               [Elkaisar.Config.WUT_ARMY_CAPITAL_B] : Elkaisar.Config.ARMY_B,
               [Elkaisar.Config.WUT_ARMY_CAPITAL_C] : Elkaisar.Config.ARMY_C,
               [Elkaisar.Config.WUT_ARMY_CAPITAL_D] : Elkaisar.Config.ARMY_D,
               [Elkaisar.Config.WUT_ARMY_CAPITAL_E] : Elkaisar.Config.ARMY_E,
               [Elkaisar.Config.WUT_ARMY_CAPITAL_F] : Elkaisar.Config.ARMY_F
            };
            return this.isTheSame(capitalArmy[unitType]);
        }

        return true;
    }

    getSlowestSpeed()
    {
        
        
        if(!this.HArmy)
            return Elkaisar.Config.CArmy.AmySpeed[0];
        
        var Slowest = Elkaisar.Config.CArmy.AmySpeed[2];
        var Cell;
        const ArmyCells = ["f_1", "f_2", "f_3", "b_1", "b_2", "b_3"];
        var iii;
        for(iii in ArmyCells){
            Cell = this.HArmy[0][ArmyCells[iii]+"_type"];
            if(Cell == 0)
                continue;
            if(Slowest > Elkaisar.Config.CArmy.AmySpeed[Cell])
                Slowest = Elkaisar.Config.CArmy.AmySpeed[Cell];
        }
            
        return Slowest;
        
    }

}

module.exports = LHeroArmy;