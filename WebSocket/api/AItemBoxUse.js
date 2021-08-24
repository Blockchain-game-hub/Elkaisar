class AItemBoxUse {

    Parm;
    idPlayer;
    constructor(idPlayer, Url) {
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }

    async useGiftBox() {
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "gift_box", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "gift_box", amount)))
            return {state: "error_0"};

        var lux_1 = 0, lux_2 = 0, lux_3 = 0, ii = 0;
        for (; ii < amount; ii++) {
            lux_1 += Elkaisar.Base.rand(0, 4);
            lux_2 += Elkaisar.Base.rand(0, 4);
            lux_3 += Elkaisar.Base.rand(0, 4);
        }

        if (lux_1 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_1", lux_1);
        if (lux_2 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_2", lux_2);
        if (lux_3 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_3", lux_3);
        return {
            "state": "ok",
            "Item": [
                {"Item": "luxury_1", "amount": lux_1},
                {"Item": "luxury_2", "amount": lux_2},
                {"Item": "luxury_3", "amount": lux_3}
            ]
        };
    }

    async useWoodBox() {
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "wood_box", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "wood_box", amount)))
            return {state: "error_0"};

        var lux_1 = 0, lux_2 = 0, lux_3 = 0, ii = 0;
        for (; ii < amount; ii++) {
            lux_1 += Elkaisar.Base.rand(0, 4);
            lux_2 += Elkaisar.Base.rand(0, 4);
            lux_3 += Elkaisar.Base.rand(0, 4);
        }

        if (lux_1 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_4", lux_1);
        if (lux_2 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_5", lux_2);
        if (lux_3 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_6", lux_3);
        return {
            "state": "ok",
            "Item": [
                {"Item": "luxury_4", "amount": lux_1},
                {"Item": "luxury_5", "amount": lux_2},
                {"Item": "luxury_6", "amount": lux_3}
            ]
        };

    }
    async useGoldenBox() {
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "golden_box", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "golden_box", amount)))
            return {state: "error_0"};

        var lux_1 = 0, lux_2 = 0, lux_3 = 0, ii = 0;
        for (; ii < amount; ii++) {
            lux_1 += Elkaisar.Base.rand(0, 4);
            lux_2 += Elkaisar.Base.rand(0, 4);
            lux_3 += Elkaisar.Base.rand(0, 4);
        }

        if (lux_1 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_7", lux_1);
        if (lux_2 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_8", lux_2);
        if (lux_3 > 0)
            Elkaisar.Lib.LItem.addItem(this.idPlayer, "luxury_9", lux_3);
        return {
            "state": "ok",
            "Item": [
                {"Item": "luxury_7", "amount": lux_1},
                {"Item": "luxury_8", "amount": lux_2},
                {"Item": "luxury_9", "amount": lux_3}
            ]
        };

    }

    async useBeginnerPack() {
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        var idPlayer = this.idPlayer;
        var Items = [];
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, Item, amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, Item, amount)))
            return {state: "error_0"};

        if (Item == "beginner_back_1")
            Items = [
                {"Item": "expan_plan", "amount": amount}, {"Item": "army_a_100", "amount": amount * 2},
                {"Item": "army_b_100", "amount": amount}, {"Item": "beginner_back_2", "amount": 1},
            ];
        else if (Item == "beginner_back_2")
            Items = [
                {"Item": "archit_a", "amount": amount}, {"Item": "army_d_100", "amount": amount},
                {"Item": "army_e_100", "amount": amount}, {"Item": "army_f_100", "amount": amount},
                {"Item": "beginner_back_3", "amount": 1}
            ];
        else if (Item == "beginner_back_3")
            Items = [
                {"Item": "archit_a", "amount": amount * 3}, {"Item": "exp_hero_8", "amount": amount * 3},
                {"Item": "army_a_1000", "amount": amount}, {"Item": "army_b_1000", "amount": amount},
                {"Item": "beginner_back_4", "amount": 1}
            ];
        else if (Item == "beginner_back_4")
            Items = [
                {"Item": "shopping_car", "amount": amount * 5}, {"Item": "bread", "amount": amount * 5},
                {"Item": "army_d_1000", "amount": amount}, {"Item": "army_e_1000", "amount": amount},
                {"Item": "beginner_back_5", "amount": 1}
            ];
        else if (Item == "beginner_back_5")
            Items = [
                {"Item": "motiv_60", "amount": amount}, {"Item": "rec_letter", "amount": amount * 5},
                {"Item": "retreat_point", "amount": amount * 10}, {"Item": "wood_box", "amount": amount},
                {"Item": "medal_silver", "amount": amount * 10}
            ];

        Items.forEach(function (OneItem) {
            Elkaisar.Lib.addItem(idPlayer, OneItem.Item, OneItem.amount)
        });

        return {
            "state": "ok",
            "Item": Items
        };
    }

    async useHiddenBox() {

        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "hidden_box", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "hidden_box", amount)))
            return {state: "error_0"};
        var ItemList = [], ii = 0;
        for (ii = 0; ii < amount; ii++) {
            const Prize = await Elkaisar.DB.ASelectFrom("*", "item", "1 ORDER BY RAND() LIMIT 1");
            Elkaisar.Lib.LItem.addItem(this.idPlayer, Prize[0]["id_item"], 1);
            ItemList.push({
                "Item": Prize[0]["id_item"],
                "amount": 1
            });
        }


        return {
            "state": "ok",
            "Item": ItemList
        };
    }

    async useArmyBox() {

        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);

        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_box", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_box", amount)))
            return {state: "error_0"};

        const army_b = 2000 * amount;
        const army_e = 1000 * amount;
        const army_f = 300 * amount;
        await Elkaisar.DB.AUpdate("army_b = army_b + ? ,  army_e = army_e + ?  , army_f = army_f + ? ",
                "city", "id_city = ?  AND id_player = ?", [army_b, army_e, army_f, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useHeroPacks() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);

        if (!Elkaisar.Config.CHero.HeroPacksPoints[Item])
            return {"state": "error_1"};
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, Item, amount)))
            return {"state": "error_0"};

        var Theater = await Elkaisar.Lib.LCityBuilding.buildingWithHeighestLvl(idCity, Elkaisar.Config.CITY_BUILDING_THEATER);
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, Item, amount)))
            return {state: "error_0"};
        for (var iii = 0; iii < amount; iii++) {
            const avatar = Elkaisar.Base.rand(0, 19);
            const name = Elkaisar.Config.CHero.HeroNames[Elkaisar.Base.rand(0, Elkaisar.Config.CHero.HeroNames.length - 1)];
            const lvl = Elkaisar.Base.rand(1, Math.max(Theater["Lvl"] * 5, 5));

            Elkaisar.Lib.ALHero.addNew(this.idPlayer, idCity, lvl, avatar, name, Elkaisar.Config.CHero.HeroPacksPoints[Item]);
        }

        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.coinOutState(idCity);

        return {
            "state": "ok"
        };
    }

    async useResourcePacks() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);

        if (!Elkaisar.Config.CItem.ItemResource[Item])
            return {"state": "error_1"};
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, Item, amount)))
            return {"state": "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, Item, amount)))
            return {state: "error_0"};
        Elkaisar.Lib.LSaveState.saveCityState(idCity);

        await Elkaisar.DB.AUpdate(
                `${Elkaisar.Config.CItem.ItemResource[Item]["for"]} = ${Elkaisar.Config.CItem.ItemResource[Item]["for"]} + ?`,
                "city", "id_city = ? AND id_player = ?", [Elkaisar.Config.CItem.ItemResource[Item]["a"] * amount, idCity, this.idPlayer]);

        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };

    }

}

module.exports = AItemBoxUse;



