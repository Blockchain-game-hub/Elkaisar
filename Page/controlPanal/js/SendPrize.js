GUILD_TO_SEND_PRIZE = {};

function promotionOptionList(){

   var list = "";
   for(var iii in pormotion){

       list += `
              <option value="${iii}">${pormotion[iii].ar_title}</option>`;

   }
   return list;

}

ElkaisarCp.SendPrize = {};
ElkaisarCp.getEquip();

$(document).on("click", ".close-alert", function () {
    $("#over_lay_alert").remove();
});



$(document).on('change', '.add-matrial', function () {

    var self = $(this);

    if (this.checked) {

        var type = $(this).attr('prize-type');

        var name = "";
        if (type === "matrial") {

            var matrial = $(this).attr("data-matrial");
            name = ElkaisarCp.Items[matrial].name;

        } else if (type === "equip") {

            var idEquip = $(this).attr("data-equip-id");
            name = ElkaisarCp.Equips[idEquip].name;
        }

        var msg = `تأكيد اضافة  ${name} لقائمة الارسال 
                <input id="amount-to-add" type="text" placeholder="ادخل الكمية المراد اضافتها"/>`;
        alertBox.confirmDialog(msg, function () {

            var amount = Number($("#amount-to-add").val());
            if (!amount || amount <= 0)
                return;
            $(self).parent(".name").prev(".amount").html(amount);

            if (type === "matrial") {
                PRIZE_TO_SEND.push({
                    type: "matrial",
                    matrial: matrial,
                    amount: amount
                });
            } else if (type === "equip") {

                PRIZE_TO_SEND.push({
                    type: "equip",
                    idEquip: idEquip,
                    amount: amount
                });
            }

            showMatrialToSend();
        }, this);


    } else {


        /*  remove from matrial to send */
        var matrial = $(this).attr("data-matrial");
        for (var ii in PRIZE_TO_SEND) {

            if (PRIZE_TO_SEND[ii].matrial === matrial) {
                PRIZE_TO_SEND.splice(ii, 1);
            }

        }
        $(self).parent(".name").prev(".amount").html("");
        showMatrialToSend();
    }


});


function showPlayerToSend()
{
    var allList = "";
    for (var iii in PLYAERS_TO_SEND) {

        allList += `<li class="delete-player"
                            data-id-player="${PLYAERS_TO_SEND[iii].id_player}"
                            data-player-name="${PLYAERS_TO_SEND[iii].name}"
                            data-player-porm="${PLYAERS_TO_SEND[iii].porm}"
                     style="direction:  rtl"> ${PLYAERS_TO_SEND[iii].name} (${pormotion[PLYAERS_TO_SEND[iii].porm].ar_title}) (لاعب)</li>`;

    }


    if(GUILD_TO_SEND_PRIZE.idGuild){
        allList += `<li class="delete-guild"
                            data-id-guild="${GUILD_TO_SEND_PRIZE.id_guild}"
                            data-guild-name="${GUILD_TO_SEND_PRIZE.GuildName}"
                     style="direction:  rtl"> ${GUILD_TO_SEND_PRIZE.GuildName} (حلف)</li>`;
    }

    $("#player-to-send").html(allList);
}

function showMatrialToSend()
{
    var allList = "";
    for (var iii in PRIZE_TO_SEND) {

        if (PRIZE_TO_SEND[iii].type === "matrial") {

            allList += `<div class="matrial-unit" prize-type="matrial" data-matrial="${iii}">
                            <img src="../${ElkaisarCp.Items[PRIZE_TO_SEND[iii].matrial].image}"/>
                            <div class="amount">${PRIZE_TO_SEND[iii].amount}</div>
                            <div class="name"><span>${ElkaisarCp.Items[[PRIZE_TO_SEND[iii].matrial]].name}</span></div>
                        </div>`;

        } else if (PRIZE_TO_SEND[iii].type === "equip") {

            if (ElkaisarCp.Equips[PRIZE_TO_SEND[iii].idEquip]) {

                allList += `<div class="matrial-unit" prize-type="equip" data-equip-id="${PRIZE_TO_SEND[iii].idEquip}">
                            <img src="../${ElkaisarCp.Equips[PRIZE_TO_SEND[iii].idEquip].image}"/>
                            <div class="amount">${PRIZE_TO_SEND[iii].amount}</div>
                            <div class="name"><span>${ElkaisarCp.Equips[PRIZE_TO_SEND[iii].idEquip].name}</span></div>
                        </div>`;

            }
        }


    }

    $("#matrial-to-send").html(allList);
}




$(document).on("click", "#start-search-player-prize", function () {

    var searchval = $("#search-val").val();

    $.ajax({
        url: BASE_URL + "/cp/APlayer/searchByName",
        data: {
            seg: searchval
        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

            $("#start-search").removeAttr("disabled");


            if (isJson(data)) {

                var json_data = JSON.parse(data);

            } else {
                alert(data);
            }

            if (json_data.length < 1) {
                alert("لا يوجد لاعب يحمل هذا الاسم");
                return;
            }


            var list = "";

            for (var iii in json_data) {

                list += `<li class="add-player-prize"
                            data-id-player="${json_data[iii].id_player}"
                            data-player-name="${json_data[iii].name}"
                            data-player-porm="${json_data[iii].porm}"
                     style="direction:  rtl"> ${json_data[iii].name} (${pormotion[json_data[iii].porm].ar_title})</li>`;

            }
            $("#search-result ul").html(list);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });


});


$(document).on("click", "#start-search-guild-prize", function () {

    var searchval = $("#search-val").val();

    $.ajax({
        url: `http://${WS_HOST}:${WS_PORT}/cp/CPSendPrize/searchByGuildName`,
        data: {
            seg: searchval
        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

            $("#start-search").removeAttr("disabled");


            if (isJson(data)) {

                var json_data = JSON.parse(data);

            } else {
                alert(data);
            }

            if (json_data.length < 1) {
                alert("لا يوجد حلف يحمل هذا الاسم");
                return;
            }


            var list = "";

            for (var iii in json_data) {

                list += `<li class="add-guild-prize"
                            data-id-guild="${json_data[iii].id_guild}"
                            data-guild-name="${json_data[iii].name}"
                     style="direction:  rtl"> ${json_data[iii].name} (${json_data[iii].mem_num}) </li>`;

            }
            $("#search-result ul").html(list);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });


});



$(document).on("click", ".add-player-prize", function () {

    var id_player = $(this).attr("data-id-player");
    var name = $(this).attr("data-player-name");
    var porm = $(this).attr("data-player-porm");

    $(this).remove();
    PLYAERS_TO_SEND.push({
        id_player: id_player,
        name: name,
        porm: porm
    });

    

    showPlayerToSend();
});


$(document).on("click", ".add-guild-prize", function () {

    var idGuild = $(this).attr("data-id-guild");
    var GuildName = $(this).attr("data-guild-name");
    $(this).remove();

    GUILD_TO_SEND_PRIZE = {
        idGuild: idGuild,
        GuildName: GuildName
    }
   


    showPlayerToSend();
});



$(document).on("click", ".delete-player", function () {

    var id_player = $(this).attr("data-id-player");
    var name = $(this).attr("data-player-name");
    var porm = $(this).attr("data-player-porm");

    $(this).remove();


    for (var jjj in PLYAERS_TO_SEND) {

        if (Number(PLYAERS_TO_SEND[jjj].id_player) === Number(id_player)) {

            PLYAERS_TO_SEND.splice(jjj, 1);
        }

    }


    showPlayerToSend();


});


$(document).on("click", ".delete-guild", function () {

    var id_player = $(this).attr("data-id-guild");
    var name = $(this).attr("data-guild-name");
    $(this).remove();

    GUILD_TO_SEND_PRIZE = {};

    showPlayerToSend();


});



$(document).on("click", "#SEND_MATRIAL button", function () {

    var sendTo = $(this).attr("data-send-to");

    if (PRIZE_TO_SEND.length <= 0) {
        alert(" لا يوجد جوائز لارسالها");
        return;
    }

    if (sendTo === "player") {



        if (PLYAERS_TO_SEND.length < 1) {
            alert(" لا يوجد لاعبين لارسال الجوائز لهم");
            return;
        }

        if (confirm(`تأكيد أرسال ${PRIZE_TO_SEND.length}  مادة الى ${PLYAERS_TO_SEND.length } لاعب`)) {


            $.ajax({
                url: `${BASE_URL}/cp/ASendPrize/sendPrizeToPlayer`,
                data: {
                    Players: JSON.stringify(PLYAERS_TO_SEND),
                    Prizes: JSON.stringify(PRIZE_TO_SEND)

                },
                type: 'POST',
                beforeSend: function (xhr) {

                },
                success: function (data, textStatus, jqXHR) {

                    if (data !== "done") {
                        alert(data);
                    }

                    PLYAERS_TO_SEND = [];
                    PRIZE_TO_SEND = [];
                    $(".add-matrial").prop("checked", false);
                    $(".amount").html("");
                    showMatrialToSend();
                    showPlayerToSend();

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });

        }


    } else if( sendTo === "guild"){

        if(!GUILD_TO_SEND_PRIZE.idGuild)
        return alertBox.confirmDialog(" عليك إختيار الحلف أولاً");

        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPSendPrize/sendPrizeToGuild`,
            type: "GET",
            data:{
                idPlayer :1,
                token: OuthToken,
                idGuild: GUILD_TO_SEND_PRIZE.idGuild,
                DistBy: $("#DistGuildPrizeBy").val(),
                Prizes: JSON.stringify(PRIZE_TO_SEND)
            },
            beforeSend:function(){},
            success: function(data){

                PLYAERS_TO_SEND = [];
                PRIZE_TO_SEND = [];
                $(".add-matrial").prop("checked", false);
                $(".amount").html("");
                showMatrialToSend();
                showPlayerToSend();
            }
        });


    } else if (sendTo === "server") {

        var pormStart = $("#send-from-porm").val() || -1;
        var pormEnd = $("#send-till-porm").val() || -1;


        var prestigeStart = $("#send-from-prestige").val() || -1;
        var prestigeEnd = $("#send-till-prestige").val() || -1;



        if (confirm(`تأكيد أرسال ${PRIZE_TO_SEND.length}  مادة الى  السيرفر بالكامل`)) {


            $.ajax({
                url: `${BASE_URL}/cp/ASendPrize/sendPrizeToServer`,
                data: {
                    prizes: JSON.stringify(PRIZE_TO_SEND),
                    pormStart: pormStart,
                    pormEnd: pormEnd,
                    prestigeStart: prestigeStart,
                    prestigeEnd: prestigeEnd

                },
                type: 'POST',
                beforeSend: function (xhr) {
                    console.log(this.data);
                },
                success: function (data, textStatus, jqXHR) {


                    alert(data);

                    var jsonData = JSON.parse(data);

                    if (jsonData.state !== "done") {
                        alert(data);
                    }


                    alertBox.confirm(`تم ارسال الجوائز الى ${jsonData.count} لاعب`);

                    PRIZE_TO_SEND = [];
                    $(".add-matrial").prop("checked", false);
                    $(".amount").html("");
                    showMatrialToSend();

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });

        }

    }
    
    else if(sendTo == "online"){
        
       alertBox.confirm("تاكيد ارسال الجوائو للمتصلين", function (){
          
          $.ajax({
                url: `${BASE_URL}/cp/ASendPrize/sendPrizeToOnline`,
                data: {
                    Prizes: JSON.stringify(PRIZE_TO_SEND)
                },
                type: 'POST',
                beforeSend: function (xhr) {

                },
                success: function (data, textStatus, jqXHR) {

                    PLYAERS_TO_SEND = [];
                    PRIZE_TO_SEND = [];
                    $(".add-matrial").prop("checked", false);
                    $(".amount").html("");
                    showMatrialToSend();
                    showPlayerToSend();
                    console.log(data)
                    var JsonObject = JSON.parse(data);
                    
                    if(JsonObject.state == "ok"){
                        alert(`تم ارسال الهدايا الى ${JsonObject.PlayerCount} لاعب`)
                    }

                },
                error: function (jqXHR, textStatus, errorThrown) {

                }
            });
           
       });
    }


});




$(document).on("change", '#select-grou-to-send', function () {

    var value = $(this).val();

    if (value === "player") {

        $("#player-list").html(`<label style="margin-top:15px; display: block ">
                                    <input id="search-val" type="text" name="textfield" placeholder="أدخل اسم اللاعب ">
                                </label>
                                <label>
                                    <input id="start-search-player-prize" type="submit" name="Submit" value="Search">
                                </label>

                                <div id="search-result">
                                    <ul>

                                    </ul>
                                </div>`);



    } else if (value === "server") {

        $("#player-list").html(`<label style="margin-top:15px; display: block ">الترقية</label>
                                    <label style="margin-top:15px; display: block ">
                                        <select id="send-from-porm">
                                            <option selected value="-1">لا يوجد</option>
                                            ${promotionOptionList()}
                                        </select> : من
                                    </label>
                                    <label style="margin-top:7px; display: block ">
                                        <select id="send-till-porm">
                                            <option selected value="-1">لا يوجد</option>
                                            ${promotionOptionList()}
                                        </select> : الى
                                    </label>
                                    
                                    <label style="margin-top:15px; display: block ">البرستيج</label>
                                    
                                    <label style="margin-top:7px; display: block ">
                                        <input id="send-from-prestige" type="text" placeholder="من"/>
                                        <input id="send-till-prestige" type="text" placeholder="الى"/>
                                    </label>`);


    }else if(value == "online"){
        $("#player-list").html("");
    }else if(value == "guild"){

        $("#player-list").html(`<label style="margin-top:15px; display: block ">
                                    <input id="search-val" type="text" name="textfield" placeholder="أدخل اسم الحلف ">
                                </label>
                                <label>
                                    <input id="start-search-guild-prize" type="submit" name="Submit" value="Search">
                                </label>
                                <div id="search-result"><ul></ul></div>
                                <label style="margin-top:15px; display: block ">توزيع النسبة حسب</label>
                                <label style="margin-top:15px; display: block ">
                                    <select id="DistGuildPrizeBy">
                                        <option selected value="Equally"> توزيع النسبة بالتساوى</option>
                                        <option value="Manually">توزيع  حسب نسبة كل لاعب بالحلف</option>
                                    </select> 
                                </label>`);
    }
    $("#SEND_MATRIAL button").attr("data-send-to", value);

});



function TradeBlackList() {


    return  $.ajax({
        url: "api/tradeCenter.php",
        data: {

            GET_BLACK_LIST: true

        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

}

//getEquip();

function showEquipForPrize() {

    var allList = "";
    for (var iii in ElkaisarCp.Equips) {


        allList += `<div class="matrial-unit add-exchange-reword" data-name="${ElkaisarCp.Equips[iii].name}" data-type="equip" data-equip-id="${iii}">
                            <img src="../${ElkaisarCp.Equips[iii].image}"/>
                            <div class="amount"></div>
                            <div class="name">
                                <input type="checkbox" name="matrial" class="add-matrial" prize-type="equip" data-equip-id="${iii}"/><span>${ElkaisarCp.Equips[iii].name}</span>
                            </div>
                        </div>`;



    }

    $("#matrial-list").html(allList);

}



function showMatrialForPrize() {
    ElkaisarCp.getItem().done(function () {
        var allList = "";
        for (var iii in  ElkaisarCp.Items) {

            allList += `<div class="matrial-unit" data-matrial="${iii}">
                        <img src="../${ ElkaisarCp.Items[iii].image}"/>
                        <div class="amount"></div>
                        <div class="name"><input type="checkbox" name="matrial" class="add-matrial" prize-type="matrial" data-matrial="${iii}"/><span>${ ElkaisarCp.Items[iii].name}</span></div>
                    </div>`;

        }
        ;
        $("#matrial-list").html(allList);
    });

}
showMatrialForPrize();





$(document).on("change", "#select-prize-type-to-send", function () {


    var value = $(this).val();

    if (value === "matrial") {

        showMatrialForPrize();

    } else if (value === "equip") {

        showEquipForPrize();

    }
});