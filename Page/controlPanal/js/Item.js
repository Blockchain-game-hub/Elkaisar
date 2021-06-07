



ElkaisarCp.Item = {};
ElkaisarCp.Item.getItemData = function () {

    return $.ajax({
        url: `${BASE_URL}/cp/AItem/getItemData`,
        type: 'GET',
        success: function (data, textStatus, jqXHR) {
            var jsonObject = JSON.parse(data);
            console.log(jsonObject)
            for (var iii in jsonObject) {
                var Item = jsonObject[iii];
                if (!ElkaisarCp.Items[Item.id_item])
                    continue;

                ElkaisarCp.Items[Item.id_item].ItemData = jsonObject[iii];

            }
        }
    });

};


ElkaisarCp.Item.getPlayerItemRank = function (idItem) {
    return $.ajax({
        url: `${BASE_URL}/cp/AItem/getItemPlayerRank`,
        type: 'GET',
        data:{
            idItem: idItem
        },
        success: function (data, textStatus, jqXHR) {
            var jsonObject = JSON.parse(data);

            var Table = ``;
            for (var iii of jsonObject) {
                Table += `<tr data-id-player="${iii.id_player}">
                                <td class="first" data-id-player="${iii.id_player}">${iii.name} &nbsp; &nbsp; &nbsp;(${iii.amount})</td>
                            </tr> `;
            }

            $("#right-column").html(`<h3>ترتيب</h3> 
                                    <div style="width: 142px; margin-top: 15px;"> 
                                        <table id="user-table" class="listing" style="width: 100%;" cellspacing="0" cellpadding="0">
                                            <tbody>
                                                <tr>
                                                    <th class="first" style="text-align: center">ترتيب</th>
                                                </tr>
                                                ${Table}        
                                            </tbody>
                                        </table>

                                    </div>`);
        }
    });
};

ElkaisarCp.Item.getItemBoxPrize = function () {

    return $.ajax({
        url: `${BASE_URL}/cp/AItem/getItemBoxOpen`,
        type: 'GET',
        success: function (data, textStatus, jqXHR) {
            var jsonObject = JSON.parse(data);
            for (var ii in ElkaisarCp.Items)
                ElkaisarCp.Items[ii].PrizeList = {};

            for (var iii in jsonObject) {
                var Item = jsonObject[iii];
                if (!ElkaisarCp.Items[Item.id_item])
                    continue;
                if (!ElkaisarCp.Items[Item.id_item].PrizeList)
                    ElkaisarCp.Items[Item.id_item].PrizeList = {};

                ElkaisarCp.Items[Item.id_item].PrizeList[Item.id_prize] = jsonObject[iii];

            }
        }
    });

};


ElkaisarCp.Item.showAllItemList = function () {
    var List = "";
    for (var iii in ElkaisarCp.Items) {
        var Item = ElkaisarCp.Items[iii];
        List += `<div class="matrial-unit select-item-to-edit" ${Item.use == "Box" ? `style="background: #ccc"` : ""} data-name="${Item.name}" data-type="matrial" data-id-item="${iii}">
                <img src="../${Item.image}">
                <div class="name"><span>${Item.name}</span></div>
            </div>`;
    }
    $("#matrial-list").html(List);

};

ElkaisarCp.Item.showAllItemListForP = function () {
    var List = "";
    for (var iii in ElkaisarCp.Items) {
        var Item = ElkaisarCp.Items[iii];
        List += `<div class="matrial-unit select-item-to-add" data-name="${Item.name}" data-type="matrial" data-id-item="${iii}">
                <img src="../${Item.image}">
                <div class="name"><span>${Item.name}</span></div>
            </div>`;
    }
    $("#item-box-reword").html(List);

};

ElkaisarCp.Item.showAllEquipListForP = function () {
    var List = "";
    for (var iii in ElkaisarCp.Equips) {
        var Equip = ElkaisarCp.Equips[iii];
        List += `<div class="matrial-unit select-equip-to-add" data-name="${Equip.name}" data-type="matrial" data-id-equip="${iii}">
                <img src="../${Equip.image}">
                <div class="name"><span>${Equip.name}</span></div>
            </div>`;
    }
    $("#item-box-reword").html(List);

};

ElkaisarCp.Item.showAllItemPrizeList = function (idItem) {

    var PrizeList = ElkaisarCp.Items[idItem].PrizeList;
    var List = "";

    if (!PrizeList)
        PrizeList = {};

    for (var ii in PrizeList) {
        var Prize = PrizeList[ii];
        if (Prize.prize_type == "I") {
            List += `<li style="text-align: center;">
                    <div class="unit-exchange remove-item-prize" data-id-prize="${Prize.id_prize}">
                        <img src="../${ElkaisarCp.Items[Prize.id_item_prize].image}">
                        <div class="amount">${Prize.amount_min}-${Prize.amount_max}</div>
                        <div class="win_rate">${Prize.win_rate}</div>
                        <label>${ElkaisarCp.Items[Prize.id_item_prize].name}</label>
                    </div>
                </li>`;
        } else {

            List += `<li style="text-align: center;">
                    <div class="unit-exchange remove-item-prize" data-id-prize="${Prize.id_prize}">
                        <img src="../${ElkaisarCp.Equips[Prize.id_item_prize].image}">
                        <div class="amount">${Prize.amount_min}-${Prize.amount_max}</div>
                        <div class="win_rate">${Prize.win_rate}</div>
                        <label>${ElkaisarCp.Equips[Prize.id_item_prize].name}</label>
                    </div>
                </li>`;

        }


    }

    $("#item-prize-list").html(List);
};





ElkaisarCp.Item.ChooseItem = function (idItem) {
    var Item = ElkaisarCp.Items[idItem];

    $("#itemPreview").html(`<div class="matrial-unit" style="width: 80%; margin: auto">
                                <img src="../${Item.image}">
                                <div class="amount"></div>
                                <div class="name"><span>${Item.name}</span></div>
                            </div>`);
    $("#itemPreview").attr("data-id-item", idItem);


    $("#item-base-data").html(` <label>ذهب</label><input id="ItemPriceGold" type="text" value="${Item.ItemData.gold}" style="margin-top: 3px"/>
                                <label>الكمية عند نزول اللاعب</label><input id="ItemStartingAmount" type="text" value="${Item.ItemData.startingAmount}" style="margin-top:3px"/>
                                <label>المكان</label>
                                <select id="ItemSelectPlace">
                                    <option value="main"       ${Item.ItemData.tab == "main" ? `selected="selected"` : ""}>الرئيسية</option>
                                    <option value="speed_up"   ${Item.ItemData.tab == "speed_up" ? `selected="selected"` : ""}>تسريع</option>
                                    <option value="production" ${Item.ItemData.tab == "main" ? `selected="selected"` : ""}>انتاج</option>
                                    <option value="chest"      ${Item.ItemData.tab == "production" ? `selected="selected"` : ""}>صندوق</option>
                                    <option value="luxury"     ${Item.ItemData.tab == "luxury" ? `selected="selected"` : ""}>رفاهية</option>
                                    <option value="event_zone" ${Item.ItemData.tab == "event_zone" ? `selected="selected"` : ""}>ساحة الحدث</option>
                                </select>
                                <label>اقصى عدد جوايز</label><input id="ItemMaxPrize" type="text" value="${Item.ItemData.prizeLimit}"  style="margin-top: 3px"/>
                                <hr><hr><hr>
                                 <label>تحديد الكمية فى السيرفر</label><input id="SetAmounyForServer" type="text" value="${Item.ItemData.prizeLimit}"  style="margin-top: 3px"/>
                                 <button id="SetAmounyForServerBtn">تحديد للسيرفر</button>
                                `);

    if (Item.use == "Box") {
        if ($("#select-item-prize-type").val() == "I")
            ElkaisarCp.Item.showAllItemListForP();
        else
            ElkaisarCp.Item.showAllEquipListForP();
    } else {
        $("#item-box-reword").html("");
    }
    ElkaisarCp.Item.showAllItemPrizeList(idItem);
    ElkaisarCp.Item.getPlayerItemRank(idItem);
};

$(document).on("click", ".select-item-to-edit", function () {

    var idItem = $(this).attr("data-id-item");
    ElkaisarCp.Item.ChooseItem(idItem);

});


$(document).on("click", "#SaveItemProp button", function () {

    var idItem = $("#itemPreview").attr("data-id-item");
    alertBox.confirmDialog("تأكيد حفظ خواص المادة", function () {

        $.ajax({
            url: `${BASE_URL}/cp/AItem/saveItemProp`,
            type: 'POST',
            data: {
                ItemPrice: $("#ItemPriceGold").val(),
                ItemStartingAmount: $("#ItemStartingAmount").val(),
                ItemSelectPlace: $("#ItemSelectPlace").val(),
                ItemMaxPrize: $("#ItemMaxPrize").val(),
                idItem: idItem
            },
            success: function (data, textStatus, jqXHR) {
                ElkaisarCp.Item.getItemData().done(function () {
                    ElkaisarCp.Item.getItemBoxPrize().done(function () {
                        ElkaisarCp.Item.ChooseItem(idItem);
                    });
                });


            }
        });

    });

});


$(document).on("click", ".select-item-to-add", function () {

    var idItem = $("#itemPreview").attr("data-id-item");
    var idItemPrize = $(this).attr("data-id-item");

    var msg = `
                <input id="winRate" type="text" placeholder="النسبة"/>
                <br>
                <input id="itemAmountMax" type="text" placeholder="اقصى عدد"/>
                <br>
                <input id="itemAmountMin" type="text" placeholder="اقل عدد"/>
                <br>
    `;
    alertBox.confirmDialog(msg, function () {

        $.ajax({
            url: `${BASE_URL}/cp/AItem/addPrizeToItem`,
            type: 'POST',
            data: {
                idItem: idItem,
                idItemPrize: idItemPrize,
                winRate: $("#winRate").val(),
                itemAmountMax: $("#itemAmountMax").val(),
                itemAmountMin: $("#itemAmountMin").val()
            },
            success: function (data, textStatus, jqXHR) {

                ElkaisarCp.Item.getItemData().done(function () {
                    ElkaisarCp.Item.getItemBoxPrize().done(function () {
                        ElkaisarCp.Item.ChooseItem(idItem);
                    });
                });


            }
        });

    });
});


$(document).on("click", ".select-equip-to-add", function () {

    var idItem = $("#itemPreview").attr("data-id-item");
    var idEquipPrize = $(this).attr("data-id-equip");

    var msg = `
                <input id="winRate" type="text" placeholder="النسبة"/>
                <br>
                <input id="itemAmountMax" type="text" placeholder="اقصى عدد"/>
                <br>
                <input id="itemAmountMin" type="text" placeholder="اقل عدد"/>
                <br>
    `;
    alertBox.confirmDialog(msg, function () {

        $.ajax({
            url: `${BASE_URL}/cp/AItem/addPrizeEquipToItem`,
            type: 'POST',
            data: {
                idItem: idItem,
                idEquipPrize: idEquipPrize,
                winRate: $("#winRate").val(),
                itemAmountMax: $("#itemAmountMax").val(),
                itemAmountMin: $("#itemAmountMin").val()
            },
            success: function (data, textStatus, jqXHR) {
                ElkaisarCp.Item.getItemData().done(function () {
                    ElkaisarCp.Item.getItemBoxPrize().done(function () {
                        ElkaisarCp.Item.ChooseItem(idItem);
                    });
                });


            }
        });

    });
});





$(document).on("click", ".remove-item-prize", function () {

    var idPrize = $(this).attr("data-id-prize");
    var idItem = $("#itemPreview").attr("data-id-item");

    alertBox.confirmDialog("تأكيد حذف المادة", function () {

        $.ajax({
            url: `${BASE_URL}/cp/AItem/removePrize`,
            type: 'POST',
            data: {
                idPrize: idPrize
            },
            success: function (data, textStatus, jqXHR) {

                ElkaisarCp.Item.getItemData().done(function () {
                    ElkaisarCp.Item.getItemBoxPrize().done(function () {
                        ElkaisarCp.Item.ChooseItem(idItem);
                    });
                });


            }
        });

    });

});


$(document).on("change", "#select-item-prize-type", function () {
    ElkaisarCp.Item.ChooseItem($("#itemPreview").attr("data-id-item"));
});


$(document).on("click", "#SetAmounyForServerBtn", function (){
    
    var Amount =  $("#SetAmounyForServer").val();
    var idItem = $("#itemPreview").attr("data-id-item");
    
    alertBox.confirmDialog("تأكيد تحديد الكمية للسيرفر", function () {

        $.ajax({
            url: `${BASE_URL}/cp/AItem/setServerAmount`,
            type: 'POST',
            data: {
                idItem: idItem,
                amount: Amount
            },
            success: function (data, textStatus, jqXHR) {
                

                ElkaisarCp.Item.getItemData().done(function () {
                    ElkaisarCp.Item.getItemBoxPrize().done(function () {
                        ElkaisarCp.Item.ChooseItem(idItem);
                    });
                });


            }
        });

    });
    
    
});