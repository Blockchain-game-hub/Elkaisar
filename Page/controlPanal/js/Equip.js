ElkaisarCp.Equip = {};


ElkaisarCp.getEquip().done(function (data){
    showEquipForPrize();
});

function showEquipForPrize(){
    
    var allList = "";
    for(var idEquip in ElkaisarCp.Equips){
        
        allList += `<div class="matrial-unit change-equip-power" data-name="${ElkaisarCp.Equips[idEquip].name}" data-id-equip="${idEquip}">
                            <img src="../${ElkaisarCp.Equips[idEquip].image}"/>
                            <div class="amount"></div>
                            <div class="name">
                                <span>${ElkaisarCp.Equips[idEquip].name}</span>
                            </div>
                        </div>`;
        
    }
    $("#matrial-list").html(allList);
    
}




$(document).on("click", ".change-equip-power", function (){
    
    
    var idEquip = $(this).attr("data-id-equip");

    $.ajax({
       
        url: `${BASE_URL}/cp/AEquip/getEquipPower`,
        data:{
            idEquip : idEquip
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            if(!isJson(data))
                return console.log(data, alert(data));
            
            var jsonData = JSON.parse(data)[0];
            alertBox.confirmDialog(
                    `هجوم:  <input id="point-attack" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.attack}"/>
                    دفاع:   <input id="point-defence" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.defence}"/>
                    حيوية:  <input id="point-vitality" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.vitality}"/>
                    انجراح: <input id="point-damage" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.damage}"/>
                    اجتياح: <input id="point-break" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.break}"/>
                    تصدى :   <input id="point-anti-break" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.anti_break}"/>
                    تدمير:  <input id="point-strike" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.strike}"/>
                    حصانة:  <input id="point-immunity" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.immunity}"/>
                    خواص :  <select id="point-sp_attr">
                                <option ${jsonData.sp_attr == 0 ? "selected" : ""} value="0">لا يوجد</option>
                                <option ${jsonData.sp_attr == 1 ? "selected" : ""} value="1">وابل السهام الامامى</option>
                                <option ${jsonData.sp_attr == 2 ? "selected" : ""} value="2">وابل السهام الخلفى</option>
                                <option ${jsonData.sp_attr == 3 ? "selected" : ""} value="3">الدرع</option>
                            </select>
                    `,
                    function (){
                        
                        $.ajax({
                           
                            url:`http://${WS_HOST}:${WS_PORT}/cp/CPEquip/changeEquipPower`,
                            data:{
                                idEquip   : idEquip,
                                attack    :$("#point-attack").val(),
                                defence   :$("#point-defence").val(),
                                vitality  :$("#point-vitality").val(),
                                damage    :$("#point-damage").val(),
                                break     :$("#point-break").val(),
                                anti_break:$("#point-anti-break").val(),
                                strike    :$("#point-strike").val(),
                                immunity  :$("#point-immunity").val(),
                                sp_attr   : $("#point-sp_attr").val()
                                
                            },
                            beforeSend: function (xhr) {
                                
                            },
                            success: function (data, textStatus, jqXHR) {
                               console.log(data);
                            },
                            error: function (jqXHR, textStatus, errorThrown) {
                                
                            }
                            
                        });
                        
                    }
                    );
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
        
    });
    
});

