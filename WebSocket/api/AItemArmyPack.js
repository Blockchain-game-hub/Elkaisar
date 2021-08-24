class AItemArmyPack {

    Parm;
    idPlayer;
    constructor(idPlayer, Url) {
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }

    async useArmyPackMini() {

        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_all_1", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_all_1", amount)))
            return {state: "error_0"};
        const army_a = Elkaisar.Base.rand(200, 300) * amount;
        const army_d = Elkaisar.Base.rand(100, 200) * amount;
        const spies = Elkaisar.Base.rand(30, 50) * amount;
        await Elkaisar.DB.AUpdate("army_a = army_a + ? ,  army_d = army_d + ?  , spies = spies + ? ", "city", "id_city = ?  AND id_player = ?", [army_a, army_d, spies, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackMedium() {

        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_all_2", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_all_2", amount)))
            return {state: "error_0"};
        const army_b = Elkaisar.Base.rand(30, 50) * amount;
        const army_c = Elkaisar.Base.rand(10, 20) * amount;
        const army_d = Elkaisar.Base.rand(200, 300) * amount;
        const army_e = Elkaisar.Base.rand(100, 200) * amount;
        await Elkaisar.DB.AUpdate("army_b = army_b + ?, army_c = army_c + ?, army_d = army_d + ?, army_e = army_e + ?",
                "city", "id_city = ?  AND   id_player = ?",
                [army_b, army_c, army_d, army_e, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackLarge() {

        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);

        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_all_3", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_all_3", amount)))
            return {state: "error_0"};
        const army_b = Elkaisar.Base.rand(100, 200) * amount;
        const army_c = Elkaisar.Base.rand(30, 50) * amount;
        const army_e = Elkaisar.Base.rand(200, 300) * amount;
        const army_f = Elkaisar.Base.rand(10, 20) * amount;
        await Elkaisar.DB.AUpdate("army_b = army_b + ?, army_c = army_c + ?, army_e = army_e + ?, army_f = army_f + ?",
                "city", "id_city = ?  AND   id_player = ?",
                [army_b, army_c, army_e, army_f, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackA100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_a_100", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_a_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_a = army_a + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackB100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_b_100", amount)))
            return {state: "error_0"}
            ;
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_b_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_b = army_b + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackC100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_c_100", amount)))
            return {state: "error_0"}
            ;
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_c_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_c = army_c + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackD100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_d_100", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_d_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_d = army_d + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackE100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_e_100", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_e_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_e = army_e + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackF100() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_f_100", amount)))
            return {state: "error_0"}
            ;
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_f_100", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_f = army_f + ? ", "city", "id_city = ?  AND id_player = ?", [100 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackA1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_a_1000", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_a_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_a = army_a + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackB1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_b_1000", amount)))
            return {state: "error_0"}
            ;
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_b_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_b = army_b + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackC1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_c_1000", amount)))
            return {state: "error_0"}
            ;
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_c_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_c = army_c + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackD1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_d_1000", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_d_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_d = army_d + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackE1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_e_1000", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_e_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_e = army_e + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

    async useArmyPackF1000() {
        const Item = Elkaisar.Base.validateGameNames(this.Parm["Item"]);
        const amount = Elkaisar.Base.validateId(this.Parm["amount"]);
        const idCity = Elkaisar.Base.validateId(this.Parm["idCity"]);
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, "army_f_1000", amount)))
            return {state: "error_0"};
        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, "army_f_1000", amount)))
            return {state: "error_0"};
        await Elkaisar.DB.AUpdate("army_f = army_f + ? ", "city", "id_city = ?  AND id_player = ?", [1000 * amount, idCity, this.idPlayer]);
        Elkaisar.Lib.LSaveState.saveCityState(idCity);
        Elkaisar.Lib.LSaveState.foodOutState(idCity);
        return {
            "state": "ok",
            "City": (await Elkaisar.DB.ASelectFrom("*", "city", "id_city = ?", [idCity]))[0]
        };
    }

}

module.exports = AItemArmyPack;