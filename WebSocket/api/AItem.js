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
                const Item   = Elkaisar.Lib.LItem.ItemList[idItem];
                if (amount <= 0)
                        return { "state": "error_0" };
                if (!Item)
                        return { "state": "error_1" };
                if (Item.gold <= 0)
                        return { "state": "error_2" };
                if (!(await Elkaisar.Lib.LPlayer.takePlayerGold(this.idPlayer, Item.gold*amount)))
                        return { "state": "error_3" };
                Elkaisar.Lib.LItem.addItem(this.idPlayer, idItem, amount);
                return {
                        state : "ok",
                        "PlayerItem" : await Elkaisar.DB.ASelectFrom("*", "player_item", "id_player = ?", [this.idPlayer])
                }
               

        }

}

module.exports = AItem;