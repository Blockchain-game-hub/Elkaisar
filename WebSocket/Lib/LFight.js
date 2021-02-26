
class LFight
{

    sideWin;
    Heros = [];
    Unit;
    roundNum = 0;
    FightRecord;
    FightReplayId;
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
        var Heros = this.Battel.HeroReadyList;
        var def_heros = [];
        var attack_heros = [];
        do {
            def_heros = [];
            attack_heros = [];

            for (var iii in Heros) {
                var hero = Heros[iii];
                if (this.checkHeroSweped(hero))
                    continue;
                if (hero["side"] == Elkaisar.Config.BATTEL_SIDE_DEF && def_heros.length < 3)
                    def_heros.push(hero);
                else if (hero["side"] == Elkaisar.Config.BATTEL_SIDE_ATT && attack_heros.length < 3) {
                    attack_heros.push(hero);
                }

                if (attack_heros.length === 3 && def_heros.length === 3)
                    break;
            }

            /*
             * condetion    to check if arrays contains data
             */
            if (attack_heros.length > 0 && def_heros.length > 0) {

                /* icrement round number*/
                this.roundNum++;
                /* start new round*/
                this.roundFight(attack_heros, def_heros);
            } else
                break;

            check_loop_overflow++;

        } while (check_loop_overflow < 2500);
        this.endFight(this.Battel.HeroReadyList);

        return this.Battel.HeroReadyList;

    }

    roundFight(Attack, Defence) {
        var This = this;
        
        this.FightRecord.addRound(Attack, Defence);
        
        Attack.forEach(function (Hero, Place) {
            if (Defence[Place])
                This.heroFight(Hero, Defence[Place], {Def: Defence[Place].id_hero, Att: Hero.id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_ATT});
            else if (Defence[Place + 1])
                This.heroFight(Hero, Defence[Place + 1], {Def: Defence[Place + 1].id_hero, Att: Hero.id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_ATT});
            else if (Defence[Place + 2])
                This.heroFight(Hero, Defence[Place + 2], {Def: Defence[Place + 2].id_hero, Att: Hero.id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_ATT});
            else if (Defence[Place - 1])
                This.heroFight(Hero, Defence[Place - 1], {Def: Defence[Place - 1].id_hero, Att: Hero.id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_ATT});
            else if (Defence[Place - 2])
                This.heroFight(Hero, Defence[Place - 2], {Def: Defence[Place - 2].id_hero, Att: Hero.id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_ATT});
        });

        Defence.forEach(function (Hero, Place) {

            if (Attack[Place]) {
                This.heroFight(Hero, Attack[Place], {Def: Hero.id_hero,     Att: Attack[Place].id_hero,      Sid: Elkaisar.Config.BATTEL_SIDE_DEF});
            } else if (Attack[Place + 1]) {
                This.heroFight(Hero, Attack[Place + 1], {Def: Hero.id_hero, Att: Attack[Place + 1].id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_DEF});
            } else if (Attack[Place + 2]) {
                This.heroFight(Hero, Attack[Place + 2], {Def: Hero.id_hero, Att: Attack[Place + 2].id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_DEF});
            } else if (Attack[Place - 1])
                This.heroFight(Hero, Attack[Place - 1], {Def: Hero.id_hero, Att: Attack[Place - 1].id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_DEF});
            else if (Attack[Place - 2])
                This.heroFight(Hero, Attack[Place - 2], {Def: Hero.id_hero, Att: Attack[Place - 2].id_hero, Sid: Elkaisar.Config.BATTEL_SIDE_DEF});
        });

        this.scanHero(Defence);
        this.scanHero(Attack);
    }

    cellFight(cell_attack, cell_def, Places)
    {

        /* trimnate condetion*/
        if (cell_attack["unit"] <= 0)
            return;

        var attack = cell_attack["attack"];
        var def = cell_def["def"];
        var vitality = cell_def["vit"];
        //  first calculate  how many unit there defence has been broken
        var undefence_unit = Math.ceil(cell_attack["unit"] * attack / def);
        //then calculate how many unit  die
        var deadUnits = Math.ceil(cell_attack["unit"] * cell_attack["dam"] / vitality);
        // get the min number
        var total_dead = Math.min(undefence_unit, deadUnits);
        /* this condetion will check  if the two cells have not fight done*/



        cell_def["dead_unit"] += total_dead;
        var amountDead = Elkaisar.Config.CArmy.ArmyCap[cell_def["armyType"]] * total_dead;
        cell_attack ["troopsKills"] += amountDead;
        cell_def    ["troopsKilled"] += amountDead;
        cell_attack ["honor"] += Math.ceil(total_dead / cell_def["def"]);
        cell_attack ["points"] += Math.ceil(total_dead);
        
        /*idHeroAttack.CellAttPlace.idHeroDefence.CellDefPlace.AttackType.KillAmount*/
        this.FightRecord.addAttack(`${Places.Att}.${cell_attack.CellIndex}.${Places.Def}.${cell_def.CellIndex}.0.${amountDead}`);
    }

    heroFight(hero_attck, hero_def, Places)
    {
        /* لو البطل ششايل اكتر من ثلاثة خانات كدة انا هخلى كل خانة تضرب الخانة الى قصادها 
         والخانة الى وراها         */

        for (var place in hero_attck["real_eff"])
        {
            var Cell = hero_attck["real_eff"][place];
            if (Cell["unit"] <= 0)
                continue;
            place = Number(place);

            /* first if i loop throght all cells */
            if (hero_def["real_eff"][place]
                    && hero_def["real_eff"][place]["unit"] > hero_def["real_eff"][place]["dead_unit"])// fight FIRST row                                     ATTACK SIDE
                //                                              
                this.cellFight(Cell, hero_def["real_eff"][place], Places);                                       //                       +------------------------+------------------------+-------------------------+
            //                       |                        |                        |                         |                
            else if (hero_def["real_eff"][place + 3]                                                     // this will fight the back cell to him
                    && hero_def["real_eff"][place + 3]["unit"] > hero_def["real_eff"][place + 3]["dead_unit"])//                  |            4           |            5           |             6           |                
                //                       |                        |                        |                         |                
                this.cellFight(Cell, hero_def["real_eff"][place + 3 ], Places);                                  //                       |________________________|________________________|_________________________|
            //                       |                        |                        |                         |               
            else if (hero_def["real_eff"][place + 1]
                    && hero_def["real_eff"][place + 1]["unit"] > hero_def["real_eff"][place + 1]["dead_unit"])  //                     |            1           |            2           |              3          |                
                //                       |                        |                        |                         |                
                this.cellFight(Cell, hero_def["real_eff"][place + 1 ], Places);                                  //                       +------------------------+------------------------+-------------------------+                       
            //
            else if (hero_def["real_eff"][place + 4]
                    && hero_def["real_eff"][place + 4]["unit"] > hero_def["real_eff"][place + 4]["dead_unit"])  //
                //
                this.cellFight(Cell, hero_def["real_eff"][place + 4 ], Places);                                  //
            //
            else if (hero_def["real_eff"][place + 2]
                    && hero_def["real_eff"][place + 2]["unit"] > hero_def["real_eff"][place + 2]["dead_unit"])  //
                //_____________________________________________________________________________________________________________________________
                this.cellFight(Cell, hero_def["real_eff"][place + 2 ], Places);                                  //
            //
            else if (hero_def["real_eff"][place + 5]
                    && hero_def["real_eff"][place + 5]["unit"] > hero_def["real_eff"][place + 5]["dead_unit"])  //
                //
                this.cellFight(Cell, hero_def["real_eff"][place + 5 ], Places);                                  //
            //                       +------------------------+------------------------+-------------------------+
            else if (hero_def["real_eff"][place - 1]
                    && hero_def["real_eff"][place - 1]["unit"] > hero_def["real_eff"][place - 1]["dead_unit"])  //                     |                        |                        |                         |
                //                       |           1            |           2            |            3            |
                this.cellFight(Cell, hero_def["real_eff"][place - 1 ], Places);                                  //                       |                        |                        |                         |
            //                       |________________________|________________________|_________________________|
            else if (hero_def["real_eff"][place - 4]
                    && hero_def["real_eff"][place - 4]["unit"] > hero_def["real_eff"][place - 4]["dead_unit"]) //                     |                        |                        |                         |
                //                       |           4            |            5           |            6            |
                this.cellFight(Cell, hero_def["real_eff"][place - 4 ], Places);                                  //                       |                        |                        |                         |
            //                       +------------------------+------------------------+-------------------------+
            else if (hero_def["real_eff"][place - 5]
                    && hero_def["real_eff"][place - 5]["unit"] > hero_def["real_eff"][place - 5]["dead_unit"])  //
                this.cellFight(Cell, hero_def["real_eff"][place - 5 ], Places);

            if (hero_attck["real_eff"].length <= 3) {

                if (hero_def["real_eff"][place + 3] && hero_def["real_eff"][place]
                        && hero_def["real_eff"][place + 3]["unit"] > hero_def["real_eff"][place + 3]["dead_unit"]) {                            //

                    this.cellFight(Cell, hero_def["real_eff"][place + 3 ], Places);


                }

            }
        }

    }

    scanHero(hero_arr)
    {
        for (var iii in hero_arr) {
            var Hero = hero_arr[iii];

            Hero["standTillRound"]++;
            for (var ii in Hero.real_eff)
            {

                var Cell = Hero.real_eff[ii];
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
        }
    }

    checkHeroSweped(Hero)
    {
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
            if (Hero["real_eff"][1]) {
                Hero["post"]["f_1"] = Hero["pre"]["f_1"] - Hero["real_eff"][1]["unit"]; // set post value of cell
                total_unit[Hero["side"]] += Hero["real_eff"][1]["unit"];// increment  value to get the winner hero and dead one
            }

            if (Hero["real_eff"][2]) {
                Hero["post"]["f_2"] = Hero["pre"]["f_2"] - Hero["real_eff"][2]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][2]["unit"];
            }

            if (Hero["real_eff"][3]) {
                Hero["post"]["f_3"] = Hero["pre"]["f_3"] - Hero["real_eff"][3]["unit"];

                total_unit[Hero["side"]] += Hero["real_eff"][3]["unit"];
            }
            if (Hero["real_eff"][4]) {
                Hero["post"]["b_1"] = Hero["pre"]["b_1"] - Hero["real_eff"][4]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][4]["unit"];
            }

            if (Hero["real_eff"][5]) {
                Hero["post"]["b_2"] = Hero["pre"]["b_2"] - Hero["real_eff"][5]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][5]["unit"];
            }

            if (Hero["real_eff"][6]) {
                Hero["post"]["b_3"] = Hero["pre"]["b_3"] - Hero["real_eff"][6]["unit"];
                total_unit[Hero["side"]] += Hero["real_eff"][6]["unit"];
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