class AItem {

    Parm;
    idPlayer;
    constructor(idPlayer, Url) {
        this.Parm = Url;
        this.idPlayer = idPlayer;
    }

    async buyItem() {
        const idItem = Elkaisar.Base.validateGameNames(this.Parm.item);
        const amount = Elkaisar.Base.validateId(this.Parm.amount);
        const Item = Elkaisar.Lib.LItem.ItemList[idItem];
        if (amount <= 0)
            return {"state": "error_0"};
        if (!Item)
            return {"state": "error_1"};
        if (Item.gold <= 0)
            return {"state": "error_2"};
        if (!(await Elkaisar.Lib.LPlayer.takePlayerGold(this.idPlayer, Item.gold * amount)))
            return {"state": "error_3"};
        Elkaisar.Lib.LItem.addItem(this.idPlayer, idItem, amount);
        return {
            state: "ok",
            "PlayerItem": await Elkaisar.DB.ASelectFrom("*", "player_item", "id_player = ?", [this.idPlayer])
        }


    }

    async openItemBox() {

        const idItem = Elkaisar.Base.validateGameNames(this.Parm["idItem"]);
        const Item = await Elkaisar.DB.ASelectFrom("*", "item", "id_item = ?", [idItem]);

        if (!Item.length)
            return {"state": "error_0"};
        if(!(await Elkaisar.Lib.LItem.isEnough(this.idPlayer, idItem, 1)))
            return {"state": "error_1"};

        if (!(await Elkaisar.Lib.LItem.useItem(this.idPlayer, idItem, 1)))
            return {state: "error_0"};
        const itemOpenPrize = await Elkaisar.DB.ASelectFrom("*", "item_box_open", "id_item = ? ORDER BY RAND()", [idItem]);
        var PlayerPrize = [];
        var ii = 0, amount, Equip;
        for (; ii < itemOpenPrize.length; ii++) {
            const one = itemOpenPrize[ii];
            if (PlayerPrize.length >= Item[0].prizeLimit)
                break;
            const luck = Elkaisar.Base.rand(0, 1000);
            if (luck > one["win_rate"])
                continue;

            amount = Elkaisar.Base.rand(one["amount_min"], one["amount_max"]);

            if (one["prize_type"] == "I") {
                Elkaisar.Lib.LItem.addItem(this.idPlayer, one["id_item_prize"], amount);
            } else if (one["prize_type"] == "E") {
                for (var iii = 0; iii < amount; iii++)
                    Elkaisar.Lib.LItem.addEquip(this.idPlayer, one["id_item_prize"]);
            }

            PlayerPrize.push({
                "Item": one["id_item_prize"],
                "amount": amount,
                "prizeType": one["prize_type"]
            })
        }
        ;

        return {
            "state": "ok",
            "Item": PlayerPrize
        }

    }

}

module.exports = AItem;