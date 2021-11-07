

ElkaisarCp.GodGate = {};

ElkaisarCp.GodGate.getGeneralData = function () {

    $.ajax({
        url: `http://${WS_HOST}:${WS_PORT}/cp/CPGodGate/getGodGateGeneralData`,
        type: 'GET',
        data: {},
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {
            if (!isJson(data))
                return alert(data);

            var JsonObject = JSON.parse(data);

            var GeneralData = `<label>متطلبات (النقاط) لفتح البوابة</label>
                                <input value="${JsonObject.Req[0].god_gate_1_points}" id="god-gate-1-req-points"/> <hr>
                                <label>متطلبات (الترقية) لفتح البوابة</label> <br>
                                <select  id="god-gate-1-req-porm">${ElkaisarCp.BaseData.getPormSelectList(JsonObject.Req[0].god_gate_1_porm)}</select>
                                <br>
                                <label>متطلبات (النقاط) لفتح البوابة</label>
                                <input value="${JsonObject.Req[0].god_gate_2_points}" id="god-gate-2-req-points"/> <hr>
                                <label>متطلبات (الترقية) لفتح البوابة</label> <br>
                                <select  id="god-gate-2-req-porm">${ElkaisarCp.BaseData.getPormSelectList(JsonObject.Req[0].god_gate_2_porm)}</select>
                                <br>
                                <label>متطلبات (النقاط) لفتح البوابة</label>
                                <input value="${JsonObject.Req[0].god_gate_3_points}" id="god-gate-3-req-points"/> <hr>
                                <label>متطلبات (الترقية) لفتح البوابة</label> <br>
                                <select  id="god-gate-3-req-porm">${ElkaisarCp.BaseData.getPormSelectList(JsonObject.Req[0].god_gate_3_porm)}</select>
                                <br>
                                <label>متطلبات (النقاط) لفتح البوابة</label>
                                <input value="${JsonObject.Req[0].god_gate_4_points}" id="god-gate-4-req-points"/> <hr>
                                <label>متطلبات (الترقية) لفتح البوابة</label> <br>
                                <select  id="god-gate-4-req-porm">${ElkaisarCp.BaseData.getPormSelectList(JsonObject.Req[0].god_gate_4_porm)}</select>`;
            
            var MaxPoint = `<label>أقصى نقاط هجوم يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].attack}" id="god-gate-max-attack"/> <hr>
                            <label>أقصى نقاط حيوية يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].vit}" id="god-gate-max-vit"/> <hr>
                            <label>أقصى نقاط انجراح يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].damage}" id="god-gate-max-damage"/> <hr>
                            <label>أقصى نقاط دفاع يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].defence}" id="god-gate-max-defence"/> <hr>
                            <label>أقصى نقاط اجتياح يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].break}" id="god-gate-max-break"/> <hr>
                            <label>أقصى نقاط تصدى يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].anti_break}" id="god-gate-max-anti_break"/> <hr>
                            <label>أقصى نقاط تدمير يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].strike}" id="god-gate-max-strike"/> <hr>
                            <label>أقصى نقاط حصانة يمكن الحصول عليها</label>
                            <input value="${JsonObject.Max[0].immunity}" id="god-gate-max-immunity"/> <hr>`;

            $("#GodGateGeneralData").html(GeneralData);
            $("#GodGateMaxPoint").html(MaxPoint);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

};



ElkaisarCp.GodGate.getGateRankEffect = function (Gate){
    
    $.ajax({
        url: `http://${WS_HOST}:${WS_PORT}/cp/CPGodGate/getGateRankEffect`,
        type: 'GET',
        data: {
            Gate: Gate
        },
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {
       
            if(!isJson(data))
                return alert(data);
            var JsonData = JSON.parse(data);
            
            var List =  `<li style="display: flex; justify-content: space-around;">
                            <div>تصنيف</div>
                            <div>هجوم</div>
                            <div>دفاع</div>
                            <div>حيوية</div>
                             <div>انجراح</div>
                        </li>`;
            
            for(var ii in JsonData){
                List += `<li style="display: flex; justify-content: space-around; margin: 10px">
                            <div>${Number(ii) + 1}</div>
                            <div class="changeRankValue" data-gate="${Gate}" data-rank="${ii}" data-point="attack">${JsonData[ii].attack}</div>
                            <div class="changeRankValue" data-gate="${Gate}" data-rank="${ii}" data-point="defence">${JsonData[ii].defence}</div>
                            <div class="changeRankValue" data-gate="${Gate}" data-rank="${ii}" data-point="vit">${JsonData[ii].vit}</div>
                            <div class="changeRankValue" data-gate="${Gate}" data-rank="${ii}" data-point="damage">${JsonData[ii].damage}</div>
                        </li>`;
            }
            
            
            
            $("#rank-plus-point ul").html(List);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
};


$(document).on("click", "#SaveGodGateGenralData", function () {

    alertBox.confirm("تأكيد تعديل البانات الأساسية للبوبات", function () {

        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPGodGate/changeGodGateGeneralData`,
            data: {
                Gate_1_Points: $("#god-gate-1-req-points").val(),
                Gate_1_Porm  : $("#god-gate-1-req-porm").val(),
                Gate_2_Points: $("#god-gate-2-req-points").val(),
                Gate_2_Porm  : $("#god-gate-2-req-porm").val(),
                Gate_3_Points: $("#god-gate-3-req-points").val(),
                Gate_3_Porm  : $("#god-gate-3-req-porm").val(),
                Gate_4_Points: $("#god-gate-4-req-points").val(),
                Gate_4_Porm  : $("#god-gate-4-req-porm").val()
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                $("#select-god-gate").trigger("change");
            }
        });

    });

});


$(document).on("click", "#SaveGodGateMaxPoint", function () {

    alertBox.confirm("تأكيد تعديل أقصى قوة  للبوبات", function () {

        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPGodGate/changeGodGateMaxVal`,
            data: {
                attack: $("#god-gate-max-attack").val(),
                defence  : $("#god-gate-max-defence").val(),
                vit: $("#god-gate-max-vit").val(),
                damage  : $("#god-gate-max-damage").val(),
                "break": $("#god-gate-max-break").val(),
                anti_break  : $("#god-gate-max-anti_break").val(),
                strike: $("#god-gate-max-strike").val(),
                immunity  : $("#god-gate-max-immunity").val()
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                $("#select-god-gate").trigger("change");
            }
        });

    });

});

$(document).on("change", "#select-god-gate", function () {

    var Gate = $(this).val();

    ElkaisarCp.GodGate.getGeneralData();
    ElkaisarCp.GodGate.getGateRankEffect(Gate);
});



$(document).on("click", ".changeRankValue", function (){
    var self = $(this);
    
    /*<div class="changeRankValue" data-gate="${Gate}" data-rank="${ii}" data-point="attack">${JsonData[ii].attack}</div>*/
    alertBox.confirm(`تأكيد تعديل النقط
        <input value="0" id="NewRankVal"/>`, function (){
        $.ajax({
            url: `http://${WS_HOST}:${WS_PORT}/cp/CPGodGate/changeRankEff`,
            data: {
                Gate: self.attr("data-gate"),
                Rank: self.attr("data-rank"),
                Point: self.attr("data-point"),
                Val : $("#NewRankVal").val()
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                $("#select-god-gate").trigger("change");
            }
        });
    });
});