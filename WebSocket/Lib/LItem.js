
exports.ItemList = {};
exports.getItemData = function (callBack)
{
    Elkaisar.Base.Request.getReq(
            {
                server: Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/js/json/itemBase.json`,
            function (data) {
                exports.ItemList = JSON.parse(data);
                if (callBack)
                    callBack();
            }
    );
};

exports.getItemData();