var ALL_EQUIP;
function getEquip(){
    
    return  $.ajax({
        url:"../js"+JS_VERSION+"/json/equipment.json",
        data: {get_matrial: true},
        type: 'POST',
        dataType: 'JSON',
        success: function (data, textStatus, jqXHR) {
            ALL_EQUIP = data;
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(textStatus);
        }
    });
    
}
getEquip();

function showEquipForPrize(){
    
    var allList = "";
    for(var iii in ALL_EQUIP){

        for(var jjj in ALL_EQUIP[iii]){
            
            if(jjj === "sec"){
                
                for(var kkk in ALL_EQUIP[iii][jjj] ){
                    
                    for(var lvl in ALL_EQUIP[iii][jjj][kkk]){
                        
                        allList += `<div class="matrial-unit change-equip-power" 
                                        data-name="${ALL_EQUIP[iii][jjj][kkk][lvl].name}" 
                                        data-type="equip" data-equip="${iii}" data-part="${kkk}"
                                        data-lvl="${lvl}">
                                        <img src="../${ALL_EQUIP[iii][jjj][kkk][lvl].image}"/>
                                        <div class="amount"></div>
                                        <div class="name">
                                           <span>${ALL_EQUIP[iii][jjj][kkk][lvl].name} م ${Number(lvl)+1}</span>
                                        </div>
                                    </div>`;
                        
                    }
                    
                }
                
            }else{
                
                allList += `<div class="matrial-unit change-equip-power" data-name="${ALL_EQUIP[iii][jjj].name}" data-type="equip" data-equip="${iii}" data-part="${jjj}" data-lvl="1">
                            <img src="../${ALL_EQUIP[iii][jjj].image}"/>
                            <div class="amount"></div>
                            <div class="name">
                                <span>${ALL_EQUIP[iii][jjj].name}</span>
                            </div>
                        </div>`;
                
            }
            
            
            
        }

    }

    $("#matrial-list").html(allList);
    
}

 getEquip().done(showEquipForPrize);


$(document).on("click", ".change-equip-power", function (){
    
    
    var equip = $(this).attr("data-equip");
    var part  = $(this).attr("data-part");
    var lvl   = $(this).attr("data-lvl");
    $.ajax({
       
        url:"api/equip.php",
        data:{
            GET_EQUIP_DATA:true,
            lvl:lvl,
            part:part,
            equip:equip
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            var jsonData = JSON.parse(data);
            alertBox.confirmDialog(
                    `هجوم:  <input id="point-attack" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.attack}"/>
                    دفاع:   <input id="point-defence" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.defence}"/>
                    حيوية:  <input id="point-vitality" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.vitality}"/>
                    انجراح: <input id="point-damage" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.damage}"/>
                    اجتياح: <input id="point-break" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.break}"/>
                    تصدى:   <input id="point-anti-break" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.anti_break}"/>
                    تدمير:  <input id="point-strike" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.strike}"/>
                    حصانة:  <input id="point-immunity" style="width: 64px; padding: 3px; margin: 2px 6px;" type="text" value="${jsonData.immunity}"/>`,
                    function (){
                        
                        $.ajax({
                           
                            url:"api/equip.php",
                            data:{
                                UPDATE_EQUIP_POINTS: true,
                                lvl       :lvl,
                                part      :part,
                                equip     :equip,
                                attack    :$("#point-attack").val(),
                                defence   :$("#point-defence").val(),
                                vitality  :$("#point-vitality").val(),
                                damage    :$("#point-damage").val(),
                                break     :$("#point-break").val(),
                                anti_break:$("#point-anti-break").val(),
                                strike    :$("#point-strike").val(),
                                immunity  :$("#point-immunity").val()
                                
                            },
                            type: 'POST',
                            beforeSend: function (xhr) {
                                
                            },
                            success: function (data, textStatus, jqXHR) {
                               
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

