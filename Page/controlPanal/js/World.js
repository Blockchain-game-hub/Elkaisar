

var World = {};


World.getUnitData = function () {

    return $.ajax({

        url: BASE_URL + "/js/json/worldUnitData.json",
        type: "GET",
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {
            World.UnitData = data;
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }

    });

};


$(document).ready(function () {

    World.getUnitData().done(function (data) {

        var List = ``;

        for (var ii in data) {

            List += `<option value="${ii}" data-max-lvl="${data[ii].maxLvl}">${data[ii].Title}</option>`;

        }


        $(".allWorldUnitSelectFrom").html(List);

    });


});


$(document).on("click", "#searchByCoord", function () {

    const xCoord = $("#xCoordInput1").val();
    const yCoord = $("#yCoordInput1").val();

    $.ajax({

        url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/getUnit`,
        data: {
            xCoord: xCoord,
            yCoord: yCoord,
            token: OuthToken,
            idPlayer: 1
        },
        success: function (data) {
            if (!isJson(data))
                return console.log(data, alert("Error"));
            const JsonObject = JSON.parse(data);
            const UnitData = World.UnitData[JsonObject.Unit[0].ut]
            let DominantList = "";
            for (var ii in JsonObject.Rank) {

                if (!JsonObject.Rank[ii])
                    continue;
                DominantList += `<div>
                                    <span>${JsonObject.Rank[ii].PlayerName || JsonObject.Rank[ii].GuildName}</span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button id="DeleteWorldUnitRankById" data-id-round="${JsonObject.Rank[ii].id_round}">حذف</button>
                                </div>`
            }
            let AttackQueList = "";
            for (var iii in JsonObject.AtttackQue) {

                if (!JsonObject.AtttackQue[iii])
                    continue;
                AttackQueList += `<div>
                                    <span>${JsonObject.AtttackQue[iii].PlayerName || JsonObject.AtttackQue[iii].GuildName}</span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button id="DeleteWorldUnitQueById" data-id-que="${JsonObject.AtttackQue[iii].id}">حذف</button>
                                </div>`
            }
            const WorldDesc = ` <img src="../images/world/${UnitData.WSnapShoot}">
                                <div>${UnitData.Title} مستوى ${JsonObject.Unit[0].l}</div>
                                <div>الحالة : ${JsonObject.Unit[0].lo == 0 ? "مفتوح" : "مغلق"}</div>
                                <div>[${JsonObject.Unit[0].x} , ${JsonObject.Unit[0].y}]</div>
                                ${JsonObject.Rank.legnth ?
                    `<div><button> إلغاء السيطرة</button>&nbsp;&nbsp;&nbsp;&nbsp;<span>تحت سيطرة  : ${JsonObject.Rank[0].PlayerName} لمدة ${JsonObject.Rank[0].duration} ثانية </span></div>`
                    : ""}
                                ${JsonObject.BarColonizer.legnth ?
                    ` <div><button> إلغاء الإستعمار</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span>تحت إستعمار  :  ${JsonObject.BarColonizer[0].PlayerName} </span></div>`
                    : ""}
                                
                               
                                <div>
                                    <button id="ReAssignWorldUnit" data-x-coord="${xCoord}" data-y-coord="${yCoord}">إعادة تعيين</button>
                                </div>
                                <hr>
                                <button id="LockWorldUnit" data-x-coord="${xCoord}" data-y-coord="${yCoord}">إغلاق</button>
                                <button id="UnLockWorldUnit" data-x-coord="${xCoord}" data-y-coord="${yCoord}">فتح</button>
                                <button id="FinishWorldUnitRound" data-x-coord="${xCoord}" data-y-coord="${yCoord}">إنهاء الجولة</button>
                                <button id="StartWorldUnitRound" data-x-coord="${xCoord}" data-y-coord="${yCoord}">بدء الجولة</button>
                                <hr>
                                ${DominantList}
                                <button id="ClearWorldUnitRank" data-x-coord="${xCoord}" data-y-coord="${yCoord}">مسح المسيطرين</button>
                                <hr>
                                ${AttackQueList}
                                <button id="ClearWorldUnitQue" data-x-coord="${xCoord}" data-y-coord="${yCoord}">مسح أدوار الهجوم</button>
                                `;
            $("#LeftWorldUnitDesc").html(WorldDesc);
        }

    })
});





$(document).on("click", "#ReAssignWorldUnit", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog(`تأكيد إعادة تعين الوحدة <br> <input id="newLvlForUnits" value="1"/>`, function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ReAssignLvl`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                lvlTo: $("#newLvlForUnits").val(),
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {

                if (!isJson(data))
                    return console.log(data);

                if (data.state == "error_1") {
                    alertBox.confirmDialog("لا يمكن تعديل مستوى الوحدة");
                }
                $("#searchByCoord").click();
            },
            error() { }
        })
    });
});


$(document).on("click", "#LockWorldUnit", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد إغلاق الوحدة", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/LockWorldUnit`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#UnLockWorldUnit", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد إغلاق الوحدة", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/UnLockWorldUnit`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#FinishWorldUnitRound", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد إغلاق الوحدة", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/FinishWorldUnitRound`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#StartWorldUnitRound", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد إغلاق الوحدة", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/StartWorldUnitRound`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#DeleteWorldUnitRankById", function () {

    const idRound = $(this).attr("data-id-round");

    alertBox.confirmDialog("تأكيد  مسح المسيطر", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/DeleteWorldUnitRankById`,
            data: {
                idRound: idRound,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#ClearWorldUnitRank", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد  مسح المسيطرين", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ClearWorldUnitRank`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});



$(document).on("click", "#DeleteWorldUnitQueById", function () {

    const idQue = $(this).attr("data-id-que");

    alertBox.confirmDialog("تأكيد  مسح الدور", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/DeleteWorldUnitQueById`,
            data: {
                idQue: idQue,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});


$(document).on("click", "#ClearWorldUnitQue", function () {

    const xCoord = $(this).attr("data-x-coord");
    const yCoord = $(this).attr("data-y-coord");

    alertBox.confirmDialog("تأكيد  مسح المسيطرين", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ClearWorldUnitQue`,
            data: {
                xCoord: xCoord,
                yCoord: yCoord,
                token: OuthToken,
                idPlayer: 1
            },
            beforeSend() { },
            success(data) {
                $("#searchByCoord").click();
            },
            error() { }
        })
    });

});

$(document).on("click", "#ResetMnawratLvl", function(){
    alertBox.confirmDialog("تعديل مستوى المناورات <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetMnawratLvl`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetCampLvl", function(){
    alertBox.confirmDialog("تعديل مستوى المعسكرات <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetCampLvl`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetMofedLvl", function(){
    alertBox.confirmDialog("تعديل مستوى متمردى قرطاجة <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetMofedLvl`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetGeneralLvl", function(){
    alertBox.confirmDialog("تعديل مستوى القوات الخاصة قرطاجة <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetGeneralLvl`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetMarshelLvl", function(){
    alertBox.confirmDialog("تعديل مستوى عاصمة التمرد <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetMarshelLvl`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetAsiaOne", function(){
    alertBox.confirmDialog("تعديل مستوى قسطور أسيا <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetAsiaOne`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetAsiaTwo", function(){
    alertBox.confirmDialog("تعديل مستوى الفيلق الرابع أسيا <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetAsiaTwo`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetAsiaThree", function(){
    alertBox.confirmDialog("تعديل مستوى رقيب د 9 أسيا <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetAsiaThree`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetAsiaFour", function(){
    alertBox.confirmDialog("تعديل مستوى رقيب د 4 أسيا <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetAsiaFour`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});

$(document).on("click", "#ResetAsiaFive", function(){
    alertBox.confirmDialog("تعديل مستوى قيصر أسيا <br> <input id='ChangeLvlInput' value='1'/>", function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/ResetAsiaFive`,
            data: {
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});


$(document).on("click","#changeLvlByType", function(){
    const unitTypeToChangeLvl = $("#unitTypeToChangeLvl").val();
    alertBox.confirmDialog(`تعديل مستوى  ${World.UnitData[unitTypeToChangeLvl].Title} <br> <input id='ChangeLvlInput' value='1'/>`, function () {
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPWorld/changeLvlByType`,
            data: {
                UnitType: unitTypeToChangeLvl,
                LvlTo: $("#ChangeLvlInput").val(),
                token: OuthToken,
                idPlayer: 1
            }
        })
    });
});