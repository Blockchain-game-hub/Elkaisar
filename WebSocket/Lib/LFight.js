
class LFight
{

    sideWin;
    Heros = [];
    Unit;
    roundNum = 0;
    FightRecord;
    FightReplayId;
    RoundHeros = {"0": false, "1": false, "2": false, "3": false, "4": false, "5": false};
    TotalHonor = {
        [Elkaisar.Config.BATTEL_SIDE_DEF]: 0,
        [Elkaisar.Config.BATTEL_SIDE_ATT]: 0
    };
    Battel;
    TotalKill = {
        [Elkaisar.Config.BATTEL_SIDE_DEF]: {
            "killed": 0,
            "kills": 0
        },
        [Elkaisar.Config.BATTEL_SIDE_ATT]: {
            "killed": 0,
            "kills": 0
        }
    };
    constructor(Battel) {

        this.Battel = Battel;
        this.Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        this.FightRecord = new Elkaisar.Lib.LFightRecord(this);
        this.FightReplayId = Elkaisar.Lib.LBase.MakeStringId(32);
    }
    prepareFight() {
        Elkaisar.Lib.LBattel.getHeros(this.Battel, this.Heros);
    }

    startFight() {
        
        var check_loop_overflow = 0;
        var HerosQue = this.Battel.HeroReadyList;
        var Hero;
        var Round;
        
        
        do {
            
            Round = new Elkaisar.Lib.LFightRound(this.Battel);
            
            for (var iii in HerosQue) {
                Hero = HerosQue[iii];
                
                if (this.checkHeroSweped(Hero))
                    continue;
                
                Round.addHeroToRound(Hero);
             
            }
            

            /*
             * condetion    to check if arrays contains data
             */
            if (Round.hasHerosToFight()) {

                /* icrement round number*/
                this.roundNum++;
                /* start new round*/
                Round.startRoundFight();
            } else
                break;

            check_loop_overflow++;

        } while (check_loop_overflow < 1500);
        
        this.endFight(this.Battel.HeroReadyList);
        return this.Battel.HeroReadyList;

    }


    scanHero(hero_arr)
    {
        var HeroBlocks = {};
        for (var iii in hero_arr) {
            
            var Hero = hero_arr[iii];
            if(Hero == false)
                continue;
            Hero["standTillRound"]++;
            HeroBlocks = {};
            for (var ii in Hero.real_eff)
            {

                var Cell = Hero.real_eff[ii];
                HeroBlocks[ii] = {
                    unit: Math.floor(Cell.unit),
                    dead: Math.ceil(Cell.dead_unit),
                    armyType: Cell.armyType
                };
                
                Hero["honor"] += Cell["honor"];      //  calculate the gained honor after round
                Hero["points"] += Cell["points"];      //  calculate the gained honor after round
                Hero["troopsKilled"] += Cell["troopsKilled"];
                Hero["troopsKills"] += Cell["troopsKills"];

                if (this.Battel.Players[Hero["id_player"]])
                {
                    this.Battel.Players[Hero["id_player"]]["Kills"] += Cell["troopsKills"];
                    this.Battel.Players[Hero["id_player"]]["Killed"] += Cell["troopsKilled"];
                    this.Battel.Players[Hero["id_player"]]["Honor"] += Cell["honor"];
                    this.Battel.Players[Hero["id_player"]]["RemainTroops"][Cell["armyType"]] -= Cell["dead_unit"];
                }


                this.TotalKill[Hero["side"]]["killed"] += Cell["troopsKilled"];
                this.TotalKill[Hero["side"]]["kills"] += Cell["troopsKills"];

                Cell["troopsKilled"] = 0;
                Cell["troopsKills"] = 0;

                Cell["unit"] = Math.max(0, (Cell["unit"] - Cell["dead_unit"]));
                Cell["dead_unit"] = 0;
                Cell["honor"] = 0;
                Cell["points"] = 0;
                Cell["armyType"] = Cell["unit"] > 0 ? Cell["armyType"] : 0;

            }
            
            this.FightRecord.addHeroToRound(Hero, HeroBlocks, iii);
        }
    }

    checkHeroSweped(Hero)
    {
        if(!Hero)
            return true;
        for (var ii in Hero.real_eff)
        {
            if (Hero.real_eff[ii].unit > 0)
                return false;
        }

        return true;
    }

    endFight(Heros)
    {
        var total_unit = {
            [Elkaisar.Config.BATTEL_SIDE_ATT]: 0,
            [Elkaisar.Config.BATTEL_SIDE_DEF]: 0
        };


        for (var iii in Heros)
        {
            var Hero = Heros[iii];
            if (Hero["real_eff"][0]) {
                Hero["post"]["f_1"] = Hero["pre"]["f_1"] - Hero["real_eff"][0]["unit"]; // set post value of cell
                total_unit[Hero["side"]] += Hero["real_eff"][0]["unit"];// increment  value to get the winner hero and dead one
            }

            if (Hero["real_eff"][1]) {
                Hero["post"]["f_2"] = Hero["pre"]["f_2"] - Hero["real_eff"][1]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][1]["unit"];
            }

            if (Hero["real_eff"][2]) {
                Hero["post"]["f_3"] = Hero["pre"]["f_3"] - Hero["real_eff"][2]["unit"];

                total_unit[Hero["side"]] += Hero["real_eff"][2]["unit"];
            }
            if (Hero["real_eff"][3]) {
                Hero["post"]["b_1"] = Hero["pre"]["b_1"] - Hero["real_eff"][3]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][3]["unit"];
            }

            if (Hero["real_eff"][4]) {
                Hero["post"]["b_2"] = Hero["pre"]["b_2"] - Hero["real_eff"][4]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][4]["unit"];
            }

            if (Hero["real_eff"][5]) {
                Hero["post"]["b_3"] = Hero["pre"]["b_3"] - Hero["real_eff"][5]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][5]["unit"];
            }
        }

        if (total_unit[Elkaisar.Config.BATTEL_SIDE_ATT] > 0)
            this.sideWin = Elkaisar.Config.BATTEL_SIDE_ATT;
        else
            this.sideWin = Elkaisar.Config.BATTEL_SIDE_DEF;


    }

    killHeroArmy()
    {

        for (var iii in this.Battel.HeroReadyList)
        {
            var Hero = this.Battel.HeroReadyList[iii];
            Elkaisar.Lib.LArmy.killHeroArmy(Hero, this.Battel);
            if (Hero["side"] !== this.sideWin && Hero.id_hero > 0) {
                Elkaisar.DB.Update("loyal = GREATEST(loyal - 1, 0)", "hero", "id_hero = ?", [this.Battel.HeroReadyList[iii].id_hero]);
            }

        }

    }

}


module.exports = LFight;

