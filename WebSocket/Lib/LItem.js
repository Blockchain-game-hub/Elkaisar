
class LItem {
    static ItemList = {};

    static getItemData(callBack) {
        Elkaisar.Base.Request.getReq(
                {
                    server: Elkaisar.CONST.SERVER_ID
                },
                `${Elkaisar.CONST.BASE_URL}/js/json/ItemLang/ar.json`,
                function (data) {
                    LItem.ItemList = JSON.parse(data);
                    Elkaisar.DB.SelectFrom("*", "item", "1", [], function (Items) {
                        Items.forEach(function (OneItem) {
                            if (LItem.ItemList[OneItem.id_item])
                                LItem.ItemList[OneItem.id_item].gold = OneItem.gold;
                        });
                    })
                    if (callBack)
                        callBack();
                }
        );
    }

    static addItem(idPlayer, idItem, amount = 1) {
        Elkaisar.DB.Update(
                "amount = amount + ?",
                "player_item",
                "id_player = ? AND id_item = ?", [amount, idPlayer, idItem]);
    }

    static addEquip(idPlayer, idEquip, amount = 1) {
        const EP = idEquip.split("_");
        for (var iii = 0; iii < amount; iii++) {
            Elkaisar.DB.Insert("id_player = ?, type = ?, part = ?, cat = 'main', lvl = ?", "equip", [idPlayer, EP[0], EP[1], EP[2]])

    }
    }

    static async isEnough(idPlayer, idItem, amount) {


        if (amount <= 0)
            return false;

        const Item = await Elkaisar.DB.ASelectFrom("*", "player_item", "id_player = ? AND id_item = ?", [idPlayer, idItem]);

        if (!Item.length)
            return false;
        if (Item[0].amount < amount)
            return false;
        if (Item[0].amount >= amount)
            return true;
    }

    static async useItem(idPlayer, idItem, amount) {
        if (amount <= 0)
            return false;
        const Ret = await Elkaisar.DB.AUpdate("amount = amount - ?", "player_item", "id_player = ? AND id_item = ? AND amount >= ?", [amount, idPlayer, idItem, amount]);
        if (Ret.affectedRows == 1)
            return true;
        return  false;
    }

}

LItem.getItemData();

module.exports = LItem;