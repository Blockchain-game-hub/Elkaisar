

ElkaisarCp.WsLib.WorldUnitPrize = {};



$(document).on("change", "#WORLD_UNIT_TYPE", function () {



    var lvls = WORLD_UNIT_LVL[$(this).val()];

    var selectList = "";

    for (var lvl in lvls) {
        selectList += `<option data-unit-type="${lvls[lvl].type}" data-unit-lvl="${lvls[lvl].lvl}">${lvls[lvl].ar_title}</option>`;
    }
    $("#WORLD_UNIT_WITH_LVL").html(selectList);
    $("#WORLD_UNIT_WITH_LVL").trigger("change");

});

$(document).on("change", "#WORLD_UNIT_WITH_LVL", function () {

    var unitType = $("#WORLD_UNIT_WITH_LVL option:selected").attr("data-unit-type");
    var unitLvl = $("#WORLD_UNIT_WITH_LVL option:selected").attr("data-unit-lvl");


    $.ajax({

        url: `${BASE_URL}/cp/AWorldUnitPrize/getWorldUnitPrize`,
        data: {
            unitType: unitType,
            unitLvl: unitLvl
        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

            if (isJson(data)) {
                var jsonData = JSON.parse(data);
            } else {

                alert(data);

            }

            var prizeList = " فوز <hr>";

            for (var iii in jsonData.W) {

                prizeList += `  <div class="matrial-unit" data-id-prize="${jsonData.W[iii].id_prize}">
                                        <img src="${BASE_URL}/${ElkaisarCp.Items[jsonData.W[iii].prize].image}"/>
                                        <div class="amount">${jsonData.W[iii].amount_min}-${jsonData.W[iii].amount_max}</div>
                                        <div class="win_rate">${jsonData.W[iii].win_rate}</div>
                                        <div class="name">
                                            <input  type="checkbox" name="matrial" class="remove-matrial-prize" prize-type="matrial"  data-prize-for="Win" data-id-prize="${jsonData.W[iii].id_prize}"/>
                                            <span>${ ElkaisarCp.Items[jsonData.W[iii].prize].name}</span>
                                        </div>
                                    </div>`;

            }

            prizeList += `<div style="clear: both;">خسارة</div><hr style="clear: both;">`;
            for (var iii in jsonData.L) {

                prizeList += `  <div class="matrial-unit" data-id-prize="${jsonData.L[iii].id_prize}">
                                        <img src="${BASE_URL}/${ElkaisarCp.Items[jsonData.L[iii].prize].image}"/>
                                        <div class="amount">${jsonData.L[iii].amount_min}-${jsonData.L[iii].amount_max}</div>
                                        <div class="win_rate">${jsonData.L[iii].win_rate}</div>
                                        <div class="name">
                                            <input  type="checkbox" name="matrial" class="remove-matrial-prize" data-prize-for="Lose" prize-type="matrial" data-id-prize="${jsonData.L[iii].id_prize}"/>
                                            <span>${ ElkaisarCp.Items[jsonData.L[iii].prize].name}</span>
                                        </div>
                                    </div>`;

            }
            prizeList += `خاصة<hr style="clear: both;">`;
            for (var iii in jsonData.S) {

                prizeList += `  <div class="matrial-unit" data-id-prize="${jsonData.S[iii].id_prize}">
                                        <img src="${BASE_URL}/${ElkaisarCp.Items[jsonData.S[iii].prize].image}"/>
                                        <div class="amount">${jsonData.S[iii].amount_min}-${jsonData.S[iii].amount_max}</div>
                                        <div class="win_rate">${jsonData.S[iii].win_rate}</div>
                                        <div class="name">
                                            <input  type="checkbox" name="matrial" class="remove-matrial-prize" data-prize-for="Sp" prize-type="matrial" data-id-prize="${jsonData.S[iii].id_prize}"/>
                                            <span>${ ElkaisarCp.Items[jsonData.S[iii].prize].name}</span>
                                        </div>
                                    </div>`;

            }

            prizeList += `غنيمة<hr style="clear: both;">`;
            for (var iii in jsonData.P) {

                prizeList += `  <div class="matrial-unit" data-id-prize="${jsonData.P[iii].id_prize}">
                                        <img src="${BASE_URL}/${ElkaisarCp.Items[jsonData.P[iii].prize].image}"/>
                                        <div class="amount">${jsonData.P[iii].amount_min}-${jsonData.P[iii].amount_max}</div>
                                        <div class="win_rate">${jsonData.P[iii].win_rate}</div>
                                        <div class="name">
                                            <input  type="checkbox" name="matrial" class="remove-matrial-prize" data-prize-for="Plunder" prize-type="matrial" data-id-prize="${jsonData.P[iii].id_prize}"/>
                                            <span>${ ElkaisarCp.Items[jsonData.P[iii].prize].name}</span>
                                        </div>
                                    </div>`;

            }

            $("#player-list").html(prizeList);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }

    });

});


ElkaisarCp.WsLib.WorldUnitPrize.prizeChanged = function (data) {
    $("#WORLD_UNIT_WITH_LVL").trigger("change");
};



$(document).on("change", ".add-matrial-prize", function () {

    var prize = $(this).parents(".matrial-unit").attr("data-matrial");
    var msg = `
                <input id="percent" type="text" placeholder="النسبة"/>
                <br>
                <input id="amount_max" type="text" placeholder="اقصى عدد"/>
                <br>
                <input id="amount_min" type="text" placeholder="اقل عدد"/>
                <br>
                <input type="checkbox" id="isWinPrize" checked><label for="vehicle1"> فوز</label>
                <input type="checkbox" id="isLosePrize"><label for="vehicle1"> خسارة</label>
                <input type="checkbox" id="isSpPrize"><label for="vehicle1"> خاصة</label>
                <input type="checkbox" id="isPlunderPrize"><label for="vehicle1"> غنيمة</label>
                <br>
    `;



    alertBox.confirmDialog(msg, function () {


        var unitType = $("#WORLD_UNIT_WITH_LVL option:selected").attr("data-unit-type");
        var unitLvl = $("#WORLD_UNIT_WITH_LVL option:selected").attr("data-unit-lvl");

        ws.send(JSON.stringify({
            url: "World/updateWorldPrize",
            data: {
                unitType: unitType,
                unitLvl: unitLvl,
                amountMin: $("#amount_min").val(),
                amountMax: $("#amount_max").val(),
                winRate: $("#percent").val(),
                Item: prize,
                isWin: $("#isWinPrize").is(':checked'),
                isLose: $("#isLosePrize").is(':checked'),
                isSp: $("#isSpPrize").is(':checked'),
                isPlunder: $("#isPlunderPrize").is(':checked'),
                MoreTypes: $("#MoreUnitTyps").val(),
                MoreLvls: $("#MoreUnitLvls").val()
            }
        }));


    });

});




$(document).on("change", ".remove-matrial-prize", function () {

    var idPrize = $(this).attr("data-id-prize");
    var PrizeFor = $(this).attr("data-prize-for");

    alertBox.confirmDialog("تأكيد ازالة المادة من الجوائز", function () {

        ws.send(JSON.stringify({
            url: "World/removeWorldPrize",
            data: {
                idPrize: idPrize,
                PrizeFor: PrizeFor
            }
        }));

        /*$.ajax({
         
         url: `${BASE_URL}/cp/AWorldUnitPrize/removePrize`,
         data: {
         idPrize: idPrize,
         isSpeicial: isSpeicial
         },
         type: 'POST',
         beforeSend: function (xhr) {
         
         },
         success: function (data, textStatus, jqXHR) {
         $("#WORLD_UNIT_WITH_LVL").trigger("change");
         console.log(data)
         },
         error: function (jqXHR, textStatus, errorThrown) {
         
         }
         
         });*/

    });

});


ElkaisarCp.getItem().done(function () {

    var allList = "";
    for (var iii in ElkaisarCp.Items) {

        allList += `<div class="matrial-unit" data-matrial="${iii}">
                        <img src="${BASE_URL}/${ElkaisarCp.Items[iii].image}"/>
                        <div class="amount"></div>
                        <div class="name"><input type="checkbox" name="matrial" class="add-matrial-prize" prize-type="matrial" data-matrial="${iii}"/><span>${ElkaisarCp.Items[iii].name}</span></div>
                    </div>`;

    }

    $("#matrial-list").html(allList);

});

