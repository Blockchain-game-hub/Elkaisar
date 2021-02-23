
var EXCHANGE_REWORD = null;
var EXCHANGE_REQ = [];
var EXCHANGE_UNIT = {};


ElkaisarCp.Exchange = {
    CurrentExchange: {}
};

ElkaisarCp.Exchange.showAllMatrial = function () {
    var allList = "";
    for (var iii in ElkaisarCp.Items) {

        allList += `<div class="matrial-unit add-exchange-reword" data-name="${ElkaisarCp.Items[iii].name}" data-type="matrial" data-matrial="${iii}">
                        <img src="../${ElkaisarCp.Items[iii].image}"/>
                        <div class="amount"></div>
                        <div class="name"><span>${ElkaisarCp.Items[iii].name}</span></div>
                    </div>`;

    }

    $("#matrial-list").html(allList);
};

ElkaisarCp.Exchange.showEquipForExchange = function () {

    var allList = "";
    for (var iii in ElkaisarCp.Equips) {

        allList += `<div class="matrial-unit add-exchange-reword" data-name="${ElkaisarCp.Equips[iii].name}" data-type="equip" data-equip="${iii}" data-cat="main">
                        <img src="../${ElkaisarCp.Equips[iii].image}"/>
                        <div class="amount"></div>
                        <div class="name"><span>${ElkaisarCp.Equips[iii].name}</span></div>
                    </div>`;

    }

    $("#matrial-list").html(allList);

};


ElkaisarCp.Exchange.getCurrentExchange = function () {

    $.ajax({
        url: `${BASE_URL}/cp/AExchange/getCurrentExchange`,
        type: 'POST',
        data: {},
        success: function (data, textStatus, jqXHR) {

            if (!isJson(data))
                alert(data);


            ElkaisarCp.Exchange.CurrentExchange = JSON.parse(data);
            var List = "";
            var Equip = {};
            for (var Index of ElkaisarCp.Exchange.CurrentExchange) {

                var reword = JSON.parse(Index.reword);
                if (reword.type === "matrial") {

                    List += `<li>
                            <div class="unit-exchange remove-exchange" data-id-ex="${Index.id_ex}">
                                <img src="../${ElkaisarCp.Items[reword.matrial].image}"/>
                                <label>${ElkaisarCp.Items[reword.matrial].name}</label>
                            </div>
                        </li>`;

                } else if (reword.type === "equip") {

                    Equip = ElkaisarCp.Equips[`${reword.idEquip}`];
                    if (Equip) {
                        List += `<li>
                            <div class="unit-exchange remove-exchange" data-id-ex="${Index.id_ex}">
                                <img src="../${Equip.image}"/>
                                <label>${Equip.name}</label>
                            </div>
                        </li>`;
                    }

                }
            }

            $("#current-exchange ul").html(List);

        }
    });
};


$(document).on("change", '#select-prize-type', function () {

    var value = $(this).val();

    if (value === "matrial") {

        ElkaisarCp.Exchange.showAllMatrial();

    } else if (value === "equip") {

        ElkaisarCp.Exchange.showEquipForExchange();

    }

});



$(document).on("click", '.remove-exchange', function () {

    var idExchange = $(this).data('id-ex');

    alertBox.confirm("تاكيد ازالة التبادل", function () {

        $.ajax({
            url: `${BASE_URL}/cp/AExchange/removeExchange`,
            data: {
                idEx: idExchange
            },
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                ElkaisarCp.Exchange.getCurrentExchange();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

    });

});



function reviewRewordExchange() {

    if (EXCHANGE_REWORD === null)
        return;

    var allList = "";
    var Equip = {};

    if (EXCHANGE_REWORD.type === "matrial") {

        allList += `
                        <div class="matrial-unit" data-name="${ElkaisarCp.Items[EXCHANGE_REWORD.matrial].name}" data-type="matrial" data-matrial="${EXCHANGE_REWORD.matrial}">
                            <img src="../${ElkaisarCp.Items[EXCHANGE_REWORD.matrial].image}"/>
                            <div class="amount"></div>
                            <div class="name"><span>${ElkaisarCp.Items[EXCHANGE_REWORD.matrial].name}</span></div>
                        </div>
                    `;


    } else if (EXCHANGE_REWORD.type === "equip") {
        Equip = ElkaisarCp.Equips[`${EXCHANGE_REWORD.idEquip}`];
        allList += `<div class="matrial-unit" data-name="${Equip.name}" data-type="equip" data-equip="${EXCHANGE_REWORD.idEquip}">
                        <img src="../${Equip.image}"/>
                        <div class="amount"></div>
                        <div class="name"><span>${Equip.name}</span></div>
                    </div>`;


    }
    $("#exchange-reword").html(allList);
}


function reviewReqExchange() {



    var allList = "";
    var Equip = {};

    for (var iii in EXCHANGE_REQ) {

        if (EXCHANGE_REQ[iii].type === "matrial") {

            allList += `
                            <div class="matrial-unit" data-name="${ElkaisarCp.Items[EXCHANGE_REQ[iii].matrial].name}" data-type="matrial" data-matrial="${EXCHANGE_REQ[iii].matrial}">
                                <img src="../${ElkaisarCp.Items[EXCHANGE_REQ[iii].matrial].image}"/>
                                <div class="amount">${EXCHANGE_REQ[iii].amount}</div>
                                <div class="name"><span>${ElkaisarCp.Items[EXCHANGE_REQ[iii].matrial].name}</span></div>
                            </div>
                        `;


        } else if (EXCHANGE_REQ[iii].type === "equip") {

            Equip = ElkaisarCp.Equips[`${EXCHANGE_REQ[iii].idEquip}`];
            allList += `<div class="matrial-unit" data-name="${Equip.name}" data-type="equip" data-equip="${EXCHANGE_REQ[iii].idEquip}">
                            <img src="../${Equip.image}"/>
                            <div class="amount"></div>
                            <div class="name"><span>${Equip.name}</span></div>
                        </div>`;



        }

    }

    if (Number($("#coin").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/style/coin.png" style="width: 30%"/> ${$("#coin").val()}  </div>`;

    }
    if (Number($("#food").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/style/food.png" style="width: 30%"/> ${$("#food").val()}  </div>`;

    }
    if (Number($("#wood").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/style/wood.png" style="width: 30%"/> ${$("#wood").val()} </div>`;

    }
    if (Number($("#stone").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/style/stone.png" style="width: 30%"/> ${$("#stone").val()} </div>`;

    }
    if (Number($("#metal").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/style/iron.png" style="width: 30%"/> ${$("#metal").val()}  </div>`;

    }
    if (Number($("#gold").val()) > 0) {
        allList += `<div  class="line-req matrial-unit"> <img src="../images/icons/resource/gold.png" style="width: 30%"/> ${$("#gold").val()}  </div>`;

    }

    EXCHANGE_UNIT.player_porm = $("#required-prom option:selected").val();
    EXCHANGE_UNIT.player_max = $("#player-max-trade input").val() || 10;
    EXCHANGE_UNIT.server_max = $("#server_max input").val() || 0;
    EXCHANGE_UNIT.max_to_have = $("#player-max input").val() || 1;

    allList += ` <div><span>الترقية</span> (${$("#required-prom option:selected").text()})</div>`;
    allList += ` <div><span>اقصى عدد لللاعب</span> (${EXCHANGE_UNIT.player_max})</div>`;
    allList += ` <div><span>اقصى عدد للسيرفر</span> (${EXCHANGE_UNIT.server_max > 0 ? EXCHANGE_UNIT.server_max : "غير محدد"})</div>`;
    allList += ` <div><span>اقصى عدد يمكن امتلاكة</span> (${ EXCHANGE_UNIT.max_to_have })</div>`;



    $("#exchange-req").html(allList);
}

$(document).on("focusout", '#gold', function () {

    if (Number($("#gold").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "gold",
            "amount": $("#gold").val()
        });
    }
    reviewRewordExchange();
    reviewReqExchange();

});

$(document).on("focusout", '#coin', function () {

    if (Number($("#coin").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "resource",
            "amount": $("#coin").val(),
            "resource_type": "coin"
        });
    }
    reviewRewordExchange();
    reviewReqExchange();

});
$(document).on("focusout", '#food', function () {

    if (Number($("#food").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "resource",
            "amount": $("#food").val(),
            "resource_type": "food"
        });
    }
    reviewRewordExchange();
    reviewReqExchange();

});
$(document).on("focusout", '#wood', function () {

    if (Number($("#wood").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "resource",
            "amount": $("#wood").val(),
            "resource_type": "wood"
        });
    }
    reviewRewordExchange();
    reviewReqExchange();

});
$(document).on("focusout", '#stone', function () {

    if (Number($("#stone").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "resource",
            "amount": $("#stone").val(),
            "resource_type": "stone"
        });
        reviewRewordExchange();
        reviewReqExchange();
    }

});
$(document).on("focusout", '#metal', function () {

    if (Number($("#metal").val()) > 0) {

        EXCHANGE_REQ.push({
            "type": "resource",
            "amount": $("#metal").val(),
            "resource_type": "metal"
        });
        reviewRewordExchange();
        reviewReqExchange();
    }

});


$(document).on("click", ".add-exchange-reword", function () {

    var name = $(this).data("name");
    var type = $(this).data("type");
    var self = $(this);
    var EquipData = [];

    if (EXCHANGE_REWORD === null) {


        alertBox.confirm(`تأكيد اضافة (${name})  الى الجائزة
                            <br> <input id="PrizeAmount" value="1" placeholder="كمية الجوائز">`, function () {

            EXCHANGE_REWORD = {};

            EXCHANGE_REWORD.type = type;
            if (type === "matrial") {

                EXCHANGE_REWORD.matrial = self.data("matrial");

            } else if (type === "equip") {

                EXCHANGE_REWORD.idEquip = self.data("equip");
                EquipData = EXCHANGE_REWORD.idEquip.split("_");
                EXCHANGE_REWORD.Equip = EquipData[0];
                EXCHANGE_REWORD.Part  = EquipData[1];
                EXCHANGE_REWORD.lvl   = EquipData[2];

            }
            EXCHANGE_REWORD.amount = $("#PrizeAmount").val();
            reviewRewordExchange();
            reviewReqExchange();
        });


    } else {

        alertBox.confirmDialog(`تأكيد اضافة (${name}) الى المتطلبات
                        <div><input id="amount-to-add" placeholder="الكمية المطلوبة"/></div>`, function () {


            var amount = $("#amount-to-add").val();
            
            var req = {
                type: type,
                amount: amount
            };

            if (type === "matrial") {

                req.matrial = self.data("matrial");

            } else if (type === "equip") {
                req.idEquip = self.data("equip");
                EquipData = req.idEquip.split("_");
                req.Equip = EquipData[0];
                req.Part  = EquipData[1];
                req.lvl   = EquipData[2];
            }

            EXCHANGE_REQ.push(req);

            reviewRewordExchange();
            reviewReqExchange();
        });

    }



});



/*      ADD  UNIT EXCHNAGE*/


$(document).on("click", "#ADD_EXHANGE_UNIT button", function () {

    reviewRewordExchange();
    reviewReqExchange();

    if (EXCHANGE_REWORD === null) {
        alertBox.confirm("اختار المكافئة اولا ");
        return;
    }

    if (EXCHANGE_REQ.length <= 0) {
        alertBox.confirm("يجب انت تكون هناك متطلبات أولا");
        return;
    }

    alertBox.confirm("تاكيد اضافة المادة", function () {


        EXCHANGE_UNIT.player_porm = $("#required-prom option:selected").val();
        EXCHANGE_UNIT.player_max = $("#player-max-trade input").val() || 10;
        EXCHANGE_UNIT.server_max = $("#server_max input").val() || 0;
        EXCHANGE_UNIT.max_to_have = $("#player-max input").val() || 1;

        $.ajax({
            url: `${BASE_URL}/cp/AExchange/addExchangeItem`,
            data: {
                player_porm: EXCHANGE_UNIT.player_porm,
                player_max: EXCHANGE_UNIT.player_max,
                server_max: EXCHANGE_UNIT.server_max,
                max_to_have: EXCHANGE_UNIT.max_to_have,
                reword: JSON.stringify(EXCHANGE_REWORD),
                req: JSON.stringify(EXCHANGE_REQ),
                cat: $("#exchange-cat option:selected").val()
            },
            type: 'POST',
            beforeSend: function (xhr) {
                $("#ADD_EXHANGE_UNIT button").attr("disabled", "disabled");
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data)
                $("#ADD_EXHANGE_UNIT button").removeAttr("disabled");
                ElkaisarCp.Exchange.getCurrentExchange();
                EXCHANGE_UNIT = {};
                EXCHANGE_REWORD = null;
                EXCHANGE_REQ = [];
                reviewRewordExchange();
                reviewReqExchange();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

    });

});




ElkaisarCp.getItem().done(function () {
    ElkaisarCp.getEquip().done(function () {
        ElkaisarCp.Exchange.getCurrentExchange();
        if ($("#select-prize-type").val() == "equip") {
            ElkaisarCp.Exchange.showEquipForExchange();
        }else{
            ElkaisarCp.Exchange.showAllMatrial();
        }
    });
});