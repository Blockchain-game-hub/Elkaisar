class LBattelReport
{

    Battel;
    idReport;

    constructor(Battel)
    {
        this.Battel = Battel;
    }

    static  getHeroXp(Unit) {

        if (Elkaisar.Lib.LWorldUnit.isAsianSquads(Unit["ut"])) {

            var arr_xp = {
                [Elkaisar.Config.WUT_FRONT_SQUAD]: Elkaisar.Base.rand(416500, 426500),
                [Elkaisar.Config.WUT_FRONT_BAND]: Elkaisar.Base.rand(610256, 620256),
                [Elkaisar.Config.WUT_FRONT_SQUADRON]: Elkaisar.Base.rand(810256, 820256),
                [Elkaisar.Config.WUT_FRONT_DIVISION]: Elkaisar.Base.rand(1010256, 1020256),
                [Elkaisar.Config.WUT_ARMY_LIGHT_SQUAD]: Elkaisar.Base.rand(1320256, 1420256),
                [Elkaisar.Config.WUT_ARMY_LIGHT_BAND]: Elkaisar.Base.rand(1510256, 1610256),
                [Elkaisar.Config.WUT_ARMY_LIGHT_SQUADRON]: Elkaisar.Base.rand(2150256, 2250256),
                [Elkaisar.Config.WUT_ARMY_LIGHT_DIVISION]: Elkaisar.Base.rand(2250256, 2350256),
                [Elkaisar.Config.WUT_ARMY_HEAVY_SQUAD]: Elkaisar.Base.rand(2250256, 2450256),
                [Elkaisar.Config.WUT_ARMY_HEAVY_BAND]: Elkaisar.Base.rand(2350256, 2650256),
                [Elkaisar.Config.WUT_ARMY_HEAVY_SQUADRON]: Elkaisar.Base.rand(2400265, 2800265),
                [Elkaisar.Config.WUT_ARMY_HEAVY_DIVISION]: Elkaisar.Base.rand(2950265, 3050265),
                [Elkaisar.Config.WUT_GUARD_SQUAD]: Elkaisar.Base.rand(3050265, 3450265),
                [Elkaisar.Config.WUT_GUARD_BAND]: Elkaisar.Base.rand(3550265, 3750265),
                [Elkaisar.Config.WUT_GUARD_SQUADRON]: Elkaisar.Base.rand(4000128, 4100128),
                [Elkaisar.Config.WUT_GUARD_DIVISION]: Elkaisar.Base.rand(4200128, 4400128),
                [Elkaisar.Config.WUT_BRAVE_THUNDER]: Elkaisar.Base.rand(10400128, 11000128)};

            return arr_xp[Unit["ut"]];

        } else if (Elkaisar.Lib.LWorldUnit.isBarrary(Unit["ut"])) {
            var arr_xp = {
                1: Elkaisar.Base.rand(50, 100), 2: Elkaisar.Base.rand(80, 150), 3: Elkaisar.Base.rand(300, 400),
                4: Elkaisar.Base.rand(400, 500), 5: Elkaisar.Base.rand(1300, 1400), 6: Elkaisar.Base.rand(3200, 3800),
                7: Elkaisar.Base.rand(7000, 8500), 8: Elkaisar.Base.rand(32000, 48000), 9: Elkaisar.Base.rand(63522, 80000),
                10: Elkaisar.Base.rand(120000, 180000)
            };
            return arr_xp[Unit["l"]];

        } else if (Elkaisar.Lib.LWorldUnit.isCamp(Unit["ut"]) || Elkaisar.Lib.LWorldUnit.isMonawrat(Unit["ut"])) {

            return Elkaisar.Base.rand((Math.pow(Unit["l"], 2) * 100), (Math.pow(Unit["l"], 2) * 110));

        } else if (Elkaisar.Lib.LWorldUnit.isGangStar(Unit["ut"])) {
            return Elkaisar.Base.rand(500, 650) * Unit["l"];
        } else if (Elkaisar.Lib.LWorldUnit.isCarthagianArmies(Unit["ut"])) {

            var arr_xp = {
                [Elkaisar.Config.WUT_CARTHAGE_GANG]: [Elkaisar.Base.rand(9000, 10000), Elkaisar.Base.rand(65000, 70000), Elkaisar.Base.rand(12000, 14000), Elkaisar.Base.rand(85000, 90000)],
                [Elkaisar.Config.WUT_CARTHAGE_TEAMS]: [Elkaisar.Base.rand(28000, 30000), Elkaisar.Base.rand(180000, 190000), Elkaisar.Base.rand(42000, 44000), Elkaisar.Base.rand(350000, 360000)],
                [Elkaisar.Config.WUT_CARTHAGE_REBELS]: [Elkaisar.Base.rand(650000, 660000), Elkaisar.Base.rand(1900000, 2000000), Elkaisar.Base.rand(950000, 1000000), Elkaisar.Base.rand(2800000, 2900000)],
                [Elkaisar.Config.WUT_CARTHAGE_FORCES]: [Elkaisar.Base.rand(9500000, 1000000), Elkaisar.Base.rand(2800000, 3000000), Elkaisar.Base.rand(9500000, 1000000), Elkaisar.Base.rand(3000000, 3200000)],
                [Elkaisar.Config.WUT_CARTHAGE_CAPITAL]: [Elkaisar.Base.rand(2800000, 3000000), Elkaisar.Base.rand(8200000, 8500000), Elkaisar.Base.rand(4100000, 4300000), Elkaisar.Base.rand(16200000, 16500000)]
            };


            return arr_xp[Unit["ut"]][Unit["l"] < 5 ? 0 : (Unit["l"] == 5 ? 1 : (Unit["l"] == 10 ? 3 : 2))];

        }
        return 10000;

    }

    addReport(callBack)
    {
        var Unit = Elkaisar.World.getUnit(this.Battel.Battel.x_coord, this.Battel.Battel.y_coord);
        var This = this;
        Elkaisar.DB.Insert(
                `x = ${Unit.x} , y = ${Unit.y} , time_stamp = ${Date.now() / 1000},
                side_win = ${this.Battel.Fight.sideWin} , attacker = ${this.Battel.Battel.id_player} ,
                 round_num = ${this.Battel.Fight.roundNum} , task = ${this.Battel.Battel.task}, lvl =${this.Battel.WinLvl}`, "report_battel", [], function (Res) {
            This.idReport = Res.insertId;
            if (callBack)
                callBack(Res);

            Object.values(This.Battel.Players).forEach(function (Player, Index) {
                This.addPlayer(Player);
            });
            This.Battel.HeroReadyList.forEach(function (Hero, Index) {
                Hero["gainXp"] = 0;
                if (Hero.side == This.Battel.Fight.sideWin)
                    Hero["gainXp"] = This.Battel.Fight.TotalKill[Elkaisar.Config.BATTEL_SIDE_ATT]["kills"] > 0 ? Elkaisar.Lib.LBattelReport.getHeroXp(Unit) * Hero["troopsKills"] / This.Battel.Fight.TotalKill[Elkaisar.Config.BATTEL_SIDE_ATT]["kills"] : 0;

                Hero["ord"] = Index;
                
                Elkaisar.DB.Update("exp = exp + ?", "hero", "id_hero = ? AND lvl < 255", [Hero["gainXp"] || 0, Hero["id_hero"]]);
                This.addHero(Hero);
                
            });
        });

    }

    addPlayer(Player)
    {
        if (Player["idPlayer"] <= 0)
            return;
        Elkaisar.DB.Insert(`id_player= ${Player.idPlayer}, id_report= ${this.idReport} , side = ${Player.side}, honor = ${Player.Honor}, time_stamp = ${Date.now() / 1000}`, "report_player", [], function (Res) {});
        
        //$this->addPrize($Player);
    }
    addHero(Hero) {
        var Query = `id_hero   = ?, id_player = ?, id_report = ?, 
                 f_1_pre   = ?, f_2_pre   = ?, f_3_pre  = ?, 
                 b_1_pre   = ?, b_2_pre   = ?, b_3_pre  = ?, 
                 f_1_post  = ?, f_2_post  = ?, f_3_post = ?, 
                 b_1_post  = ?, b_2_post  = ?, b_3_post = ?, 
                 f_1_type  = ?, f_2_type  = ?, f_3_type = ?, 
                 b_1_type  = ?, b_2_type  = ?, b_3_type = ?, 
                 side      = ?, ord       = ?, xp       = ?`;



        Elkaisar.DB.Insert(Query, "report_hero", [
            Hero["id_hero"], Hero["id_player"], this.idReport,
            Hero["pre"]["f_1"] , Hero["pre"]["f_2"] , Hero["pre"]["f_3"] ,
            Hero["pre"]["b_1"] , Hero["pre"]["b_2"] , Hero["pre"]["b_3"] ,
            Hero["post"]["f_1"] , Hero["post"]["f_2"] , Hero["post"]["f_3"] ,
            Hero["post"]["b_1"] , Hero["post"]["b_2"] , Hero["post"]["b_3"] ,
            Hero["type"]["f_1"] , Hero["type"]["f_2"] , Hero["type"]["f_3"] ,
            Hero["type"]["b_1"] , Hero["type"]["b_2"] , Hero["type"]["b_3"] ,
            Hero["side"], Hero["ord"], Hero["gainXp"] || 0
        ]);
        this.Battel.Fight.FightRecord.addHero(Hero);
    }

    addPrize(Player)
    {

        var This = this;
        Player["ItemPrize"].forEach(function (Prize, Index) {
            Elkaisar.DB.Insert(`id_report = ${This.idReport}, id_player = ${Player.idPlayer}, prize = '${Prize["Item"]}', amount = ${Prize.amount}`, "report_mat_prize", [])
        });



        var Sum = Object.values(Player["ResourcePrize"]).reduce(function (a, b) {
            return a + b;
        }, 0);
        if (Sum <= 0)
            return;
        Elkaisar.DB.Insert(`id_report = ${this.idReport}, id_player = ${Player.idPlayer}, 
                             food  = ${Player["ResourcePrize"]["food"]  || 0}, 
                             wood  = ${Player["ResourcePrize"]["wood"]  || 0}, 
                             stone = ${Player["ResourcePrize"]["stone"] || 0}, 
                             metal = ${Player["ResourcePrize"]["metal"] || 0}, 
                             coin  = ${Player["ResourcePrize"]["coin"]  || 0} `, "report_res_prize");

        this.Battel.Fight.FightRecord.addPlayer(Player);
    }

}



module.exports = LBattelReport;
