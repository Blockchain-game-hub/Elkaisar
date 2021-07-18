class LHero {

    static prepareHeroEquipPower(Hero, Battel)
    {
        var Unit = Elkaisar.World.getUnit(Battel.Battel.x_coord, Battel.Battel.y_coord);

        if (!Elkaisar.Lib.LWorldUnit.isEquipEfeective(Unit.ut))
            return Hero;

        for (var iii in Hero.Equip)
        {
            Hero.Equip[iii].lvl = Math.max(Hero.Equip[iii].lvl, 1);
            var EquipEff = Elkaisar.Equip.EquipPower[`${Hero.Equip[iii].type}.${Hero.Equip[iii].part}.${Hero.Equip[iii].lvl}`];
            if (!EquipEff)
                return console.log(`This Equip Has No Power ${Hero.Equip[iii].type}.${Hero.Equip[iii].part}.${Hero.Equip[iii].lvl}`);
            Hero.EquipSpAt[EquipEff.sp_attr] = true;
            for (var ii in Hero.real_eff) {

                Hero.real_eff[ii].attack += EquipEff.attack;
                Hero.real_eff[ii].def += EquipEff.defence;
                Hero.real_eff[ii].vit += EquipEff.vitality;
                Hero.real_eff[ii].dam += EquipEff.damage;
                Hero.real_eff[ii].break += EquipEff.break;
                Hero.real_eff[ii].anti_break += EquipEff.anti_break;
                Hero.real_eff[ii].strike += EquipEff.strike;
                Hero.real_eff[ii].immunity += EquipEff.immunity;
            }


        }
        return Hero;
    }
    static  studyEffectOnForces(armyType, Study) {
        if (armyType === Elkaisar.Config.ARMY_A || armyType === Elkaisar.Config.ARMY_C || armyType === Elkaisar.Config.ARMY_D) {
            return 0.03 * Study["infantry"];
        }
        if (armyType === Elkaisar.Config.ARMY_B) {
            return 0.03 * Study["riding"];
        }
        if (armyType === Elkaisar.Config.ARMY_E || armyType === Elkaisar.Config.ARMY_F) {
            return 0.03 * Study["army"];
        }
        return 0;
    }
    static prepareHeroPowerBattel(Hero, Battel)
    {
        if (Hero.id_player <= 0)
            return Hero;
        if (!Battel.Players)
            return Hero;
        if (!Battel.Players[Hero.id_player])
            return Hero;
        var BattelPlayer = Battel.Players[Hero.id_player];

        if (!BattelPlayer.Study || !BattelPlayer.State || !BattelPlayer.GodGate)
            return Hero;

        var attackPercent = 0;
        var defencePercent = 0;

        if (Battel.Players[Hero.id_player].State && Battel.Players[Hero.id_player].State.attack_10 > Date.now() / 1000)
            attackPercent = 0.1;
        if (Battel.Players[Hero.id_player].State && Battel.Players[Hero.id_player].State.defence_10 > Date.now() / 1000)
            defencePercent = 0.1;

        for (var iii in Hero.real_eff)
        {

            Hero.real_eff[iii].attack += Hero.point_atk;
            Hero.real_eff[iii].attack += Hero.real_eff[iii].attack * LHero.studyEffectOnForces(Hero.real_eff[iii].armyType, Battel.Players[Hero.id_player].Study) || 0;
            if (!LHero.studyEffectOnForces(Hero.real_eff[iii].armyType, Battel.Players[Hero.id_player].Study) && LHero.studyEffectOnForces(Hero.real_eff[iii].armyType, Battel.Players[Hero.id_player].Study) != 0)
                console.log("Error Player Study", Battel.Players[Hero.id_player]);
            Hero.real_eff[iii].attack += attackPercent * Hero.real_eff[iii].attack || 0;
            Hero.real_eff[iii].attack += Battel.Players[Hero.id_player].GodGate.attack || 0;

            Hero.real_eff[iii].def += Hero.point_def;
            Hero.real_eff[iii].def += Hero.real_eff[iii].def * Battel.Players[Hero.id_player].Study.safe * 0.03 || 0;
            Hero.real_eff[iii].def += defencePercent * Hero.real_eff[iii].def;
            Hero.real_eff[iii].def += Battel.Players[Hero.id_player].GodGate.defence || 0;

            Hero.real_eff[iii].vit += Hero.real_eff[iii].vit * Battel.Players[Hero.id_player].Study.medicine * 0.03 || 0;
            Hero.real_eff[iii].vit += Battel.Players[Hero.id_player].GodGate.vit || 0;

            Hero.real_eff[iii].dam += Battel.Players[Hero.id_player].GodGate.damage || 0;

        }

        return Hero;
    }

    static  prepareForBattel(Hero, Battel) {

        var now = Math.floor(Date.now() / 1000);

        var leoMedalEff = Hero.Hero.medal_leo > now ? 0.25 : 0;
        var denMedalEff = Hero.Hero.medal_den > now ? 0.25 : 0;
        var pointAttack = Hero.Hero.point_b + Hero.Hero.point_b * denMedalEff + Hero.Hero.point_b_plus;
        var pointDeffence = Hero.Hero.point_c + Hero.Hero.point_c * denMedalEff + Hero.Hero.point_c_plus;

        var HeroArmy = LHero.HeroArmyBattel(Hero);

        var HeroPower = {
            "idHero": Hero.Hero.id_hero,
            "id_hero": Hero.Hero.id_hero,
            "id_player": Hero.Hero.id_player,
            "id_city": Hero.Hero.id_city,
            "x_coord": Hero.Hero.x,
            "y_coord": Hero.Hero.y,
            "Hero": Hero.Hero,
            "side": Hero.side,
            "type": HeroArmy.type,
            "pre": HeroArmy.pre,
            "post": HeroArmy.post,
            "real_eff": {
                "0": Elkaisar.Config.CHero.EmptyBattelHeroEff(0, HeroArmy.type.f_1), "1": Elkaisar.Config.CHero.EmptyBattelHeroEff(1, HeroArmy.type.f_2),
                "2": Elkaisar.Config.CHero.EmptyBattelHeroEff(2, HeroArmy.type.f_3), "3": Elkaisar.Config.CHero.EmptyBattelHeroEff(3, HeroArmy.type.b_1),
                "4": Elkaisar.Config.CHero.EmptyBattelHeroEff(4, HeroArmy.type.b_2), "5": Elkaisar.Config.CHero.EmptyBattelHeroEff(5, HeroArmy.type.b_3)
            },
            "Equip": Hero.Equip,
            "EquipSpAt": {},
            "is_garrsion": Hero.isGarrison,
            "point_atk": pointAttack,
            "point_def": pointDeffence,
            "honor": 0,
            "points": 0,
            "resource_capacity": 0,
            "gainXp": 0,
            "standTillRound": 0,
            "troopsKilled": 0,
            "troopsKills": 0
        };

        LHero.prepareHeroPowerBattel(LHero.prepareHeroEquipPower(Elkaisar.Lib.LArmy.prepareHeroBattel(HeroPower), Battel), Battel);

        var Index = Battel.HeroReadyList.push(HeroPower);

    }

    static  HeroArmyBattel(Hero) {
        if (!Hero.Army)
            console.log("Hero Has No Army", Hero);
        return {
            "pre": {
                "f_1": Hero.Army.f_1_num,
                "f_2": Hero.Army.f_2_num,
                "f_3": Hero.Army.f_3_num,
                "b_1": Hero.Army.b_1_num,
                "b_2": Hero.Army.b_2_num,
                "b_3": Hero.Army.b_3_num
            },
            "type": {
                "f_1": Hero.Army.f_1_type,
                "f_2": Hero.Army.f_2_type,
                "f_3": Hero.Army.f_3_type,
                "b_1": Hero.Army.b_1_type,
                "b_2": Hero.Army.b_2_type,
                "b_3": Hero.Army.b_3_type
            },
            "post": {
                "f_1": 0,
                "f_2": 0,
                "f_3": 0,
                "b_1": 0,
                "b_2": 0,
                "b_3": 0
            }
        };
    }

    static getHeroEquip(Hero, callBack)
    {
        Elkaisar.DB.SelectFrom(`*`, `equip`, `id_hero = ?`, [Hero.idHero],
                function (Res) {
                    Hero.Equip = Res;

                    if (callBack)
                        callBack(Hero);
                });
    }
    static  getHeroArmy(Hero, callBack) {

        if (Hero.AttackTask == Elkaisar.Config.BATTEL_TASK_CHALLANGE) {

            Elkaisar.DB.SelectFrom("*", "arena_player_challange_hero", "id_hero = ?", [Hero.idHero], function (Army) {

                if (!Army.length)
                    return console.log("Error Adding Hero Army", Army, Hero);
                Hero.Army = Army[0];

                if (callBack)
                    callBack(Hero);
            });


        } else {
            Elkaisar.DB.SelectFrom(`*`, `hero_army`, `id_hero = ?`, [Hero.idHero],
                    function (Res) {

                        var Army = Res [0];
                        if (!Army)
                            return;

                        Hero.Army = Army;

                        if (callBack)
                            callBack(Hero);
                    });
        }

    }

    static getReturningSlowestSpeed(Hero)
    {
        var SlowestSpeed = Elkaisar.Config.CArmy.AmySpeed[2];
        var Entered = false;



        for (var iii in Hero.real_eff)
        {
            if (Hero.real_eff[iii].unit <= 0 || Hero.real_eff[iii].armyType === 0)
                continue;
            Entered = true;

            if (SlowestSpeed > Elkaisar.Config.CArmy.AmySpeed[Hero.real_eff[iii].armyType])
                SlowestSpeed = Elkaisar.Config.CArmy.AmySpeed[Hero.real_eff[iii].armyType];
        }

        if (!Entered)
            return Elkaisar.Config.CArmy.AmySpeed[0];

        return SlowestSpeed;

    }

    static reOrderHero(idCity, callBack) {

        Elkaisar.DB.SelectFrom("id_hero", "hero", "id_city = ? ORDER BY ord ASC", [idCity], function (Heros) {

            Heros.forEach(function (Hero, Index) {
                Elkaisar.DB.Update("ord = ?", "hero", "id_hero = ?", [Index, Hero.id_hero])
            });
            if (callBack)
                callBack();
        });

    }

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
        return (await LHero.heroFullCap(idPlayer, idHero)) - (await LHero.filledPlacesSize(idHero));
    }
}

module.exports = LHero;