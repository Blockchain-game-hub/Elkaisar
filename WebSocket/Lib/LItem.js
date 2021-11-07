
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
                Elkaisar.DB.SelectFrom("*", "item", "1", [], function(Items){
                    Items.forEach(function(OneItem){
                        if(LItem.ItemList[OneItem.id_item])
                            LItem.ItemList[OneItem.id_item].gold = OneItem.gold;
                    });
                })
                if (callBack)
                    callBack();
            }
        );
    }

    static addItem(idPlayer, idItem, amount = 1){
        Elkaisar.DB.Update(
            "amount = amount + ?",
             "player_item",
             "id_player = ? AND id_item = ?", [amount, idPlayer, idItem]);
    }

    static addEquip(idPlayer, idEquip, amount = 1){
        const EP = idEquip.split("_");
        for(var iii = 0; iii < amount; iii ++){
            Elkaisar.DB.Insert("equip", "id_player = ?, type = ?, part = ?, cat = 'main', lvl = ?", [idPlayer,EP[0],EP[1], EP[2]]  )

        }
    }

}

LItem.getItemData();

module.exports = LItem;