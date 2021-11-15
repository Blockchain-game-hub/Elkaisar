


$(document).on("focusin" , "#SearchOnePlayerVal" , function (){
    $("#drop-down ul").show();
});

$(document).on("keyup" ,"#SearchOnePlayerVal" ,function (){
    
    var searchVal = $(this).val();
   
    if(searchVal.length < 2)
         return ;
     
    $("#drop-down ul").html("");
    $.ajax({
        url: "api/onePlayer.php",
        data:{
            
            SEARCHE_FOR_PLAYER: true,
            val: searchVal
            
        },
        type: 'GET',
        beforeSend: function (xhr) {
           
        },
        success: function (data, textStatus, jqXHR) {
            $("#drop-down ul").html("");
          
            var jsonData =  JSON.parse(data);
            var list = "";
            
            for(var iii in jsonData){
                
                list += ` <li class="selectUserToShow" data-id-player = "${jsonData[iii].id_player}">${jsonData[iii].name} (${pormotion[jsonData[iii].porm].ar_title})</li>`;
                
            }
            
            
            $("#drop-down ul").html(list);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }

    });
    
});

$(document).on("click" , ".selectUserToShow" , function (){
    
    var idPlayer = $(this).attr("data-id-player");
    
    $("#drop-down ul").hide();
    
   ID_PLAYER_TO_SHOW = idPlayer;
    showPlayer(idPlayer);
    
});



function showPlayer (idPlayer){
    
    
    idPlayer = ID_PLAYER_TO_SHOW;
    
     $.ajax({
        
        url:"api/onePlayer.php",
        data:{
            
            SHOW_PLAYER_DATA:true,
            id_player: idPlayer
            
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
           
           //console.log(data)
            var jsonData = JSON.parse(data);
            console.log(jsonData)
            var table = `<tr>
                            <th class="first"> اللاعب</th>
                            <th>غذاء</th>
                            <th>اخشاب</th>
                            <th>احجار</th>
                            <th>حديد</th>
                            <th>عملات</th>
                            <th>ذهب</th>
                            <th class="last">فحص</th>
                        </tr>`;
            
            for(var iii in jsonData.city){
                
                table += `<tr data-p-name="${jsonData.player.name}" data-id-user="${jsonData.player.id_player}" data-id-city="${jsonData.city[iii].id_city}">
                            <td>${jsonData.player.name}</td>
                            <td class="update-resource-player" data-resource="food" data-amount="${jsonData.city[iii].food}">${jsonData.city[iii].food}</td>
                            <td class="update-resource-player" data-resource="wood" data-amount="${jsonData.city[iii].wood}">${jsonData.city[iii].wood}</td>
                            <td class="update-resource-player" data-resource="stone" data-amount="${jsonData.city[iii].stone}">${jsonData.city[iii].stone}</td>
                            <td class="update-resource-player" data-resource="metal" data-amount="${jsonData.city[iii].metal}">${jsonData.city[iii].metal}</td>
                            <td class="update-resource-player" data-resource="coin" data-amount="${jsonData.city[iii].coin}">${jsonData.city[iii].coin}</td>
                            <td class="update-resource-player" data-resource="gold" data-amount="${jsonData.city[iii].gold}">${jsonData.player.gold}</td>
                            <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData.player.id_player}&server=${SERVER_ID}"><img src="img/add-icon.gif" alt="add" width="16" height="16"></a></td>
                        </tr>`;
                
                
            }
            
           
            
            $("#playerCityResource").html(table);
            
            var tableArmy = `<tr>
                                <th class="first"> اللاعب</th>
                                <th>مشاة</th>
                                <th>فرسان</th>
                                <th>مدرعين</th>
                                <th>رماة</th>
                                <th>مقاليع</th>
                                <th>منجنيق</th>
                                <th>جواسيس</th>
                                <th class="last">فحص</th>
                            </tr>`;
            
            for(var iii in jsonData.city){
                
                tableArmy += `<tr data-p-name="${jsonData.player.name}" data-id-user="${jsonData.player.id_player}" data-id-city="${jsonData.city[iii].id_city}">
                                    <td>${jsonData.player.name}</td>
                                    <td class="update-army-player" data-army="army_a" data-amount="${jsonData.city[iii].army_a}">${jsonData.city[iii].army_a}</td>
                                    <td class="update-army-player" data-army="army_b" data-amount="${jsonData.city[iii].army_b}">${jsonData.city[iii].army_b}</td>
                                    <td class="update-army-player" data-army="army_c" data-amount="${jsonData.city[iii].army_c}">${jsonData.city[iii].army_c}</td>
                                    <td class="update-army-player" data-army="army_d" data-amount="${jsonData.city[iii].army_d}">${jsonData.city[iii].army_d}</td>
                                    <td class="update-army-player" data-army="army_e" data-amount="${jsonData.city[iii].army_e}">${jsonData.city[iii].army_e}</td>
                                    <td class="update-army-player" data-army="army_f" data-amount="${jsonData.city[iii].army_f}">${jsonData.city[iii].army_f}</td>
                                    <td class="update-army-player" data-army="spies"  data-amount="${jsonData.city[iii].spies}">${jsonData.city[iii].spies}</td>
                                    <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData.player.id_player}&server=${SERVER_ID}"><img src="img/add-icon.gif" alt="add" width="16" height="16"></a></td>
                                </tr>`;
                
                
            }
            
           
            
            $("#playerCityArmy").html(tableArmy);
            
            
            var matrialList = "";
            
            for(var iii in jsonData.matrial){
                
                if(Elkaisar.BaseData.Items[iii] && Number(jsonData.matrial[iii]) > 0){
                    
                    matrialList += `<div class="matrial-unit update-player-matrial" data-matrial="${iii}"  data-matrial-table="${Elkaisar.BaseData.Items[iii].db_tab}" data-amount = "${jsonData.matrial[iii]}" prize-type="matrial" data-matrial="0" style="width: 10%; height:50px;">
                                        <img src="../${Elkaisar.BaseData.Items[iii].image}">
                                        <div class="amount">${jsonData.matrial[iii]}</div>
                                        <div class="name"><span>${Elkaisar.BaseData.Items[iii].name}</span></div>
                                    </div>`;
                    
                }
                
            }
            
            $("#playerMatrialList").html(matrialList);
            
            
            var EquipList = "";
            for(var iii in jsonData.equip){
                
                if(ALL_EQUIP[jsonData.equip[iii].type] && ALL_EQUIP[jsonData.equip[iii].type][jsonData.equip[iii].part]){
                    
                    EquipList += `<div class="matrial-unit delete-equip-player" data-id-equip="${jsonData.equip[iii].id_equip}" data-part="${jsonData.equip[iii].part}" data-name="${ALL_EQUIP[jsonData.equip[iii].type][jsonData.equip[iii].part].name}" prize-type="matrial" data-matrial="0" style="width: 10%; height:50px;">
                                        <img src="../${ALL_EQUIP[jsonData.equip[iii].type][jsonData.equip[iii].part].image}">
                                        <div class="amount"></div>
                                        <div class="name"><span>${ALL_EQUIP[jsonData.equip[iii].type][jsonData.equip[iii].part].name}</span></div>
                                    </div>`;
                    
                }
                
            }
            
            $("#playerEquipList").html(EquipList);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
        
    })
    
}


$(document).on("click" ,".update-resource-player", function (){
   
    var idPlayer = $(this).parents("tr").attr("data-id-user");
    var idCity = $(this).parents("tr").attr("data-id-city");
    var name  = $(this).parents("tr").attr("data-p-name");
    var resource = $(this).attr("data-resource");
    var amount  = $(this).attr("data-amount");
    
    alertBox.confirm(`تعديل الموارد للاعب (${name}) </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>` , function (){
        
        $.ajax({
            
            url: "api/player.php",
            data:{
                UPDATE_PLAYER_RESOURCES:true,
                id_player:idPlayer,
                id_city:idCity,
                resource:resource,
                amount:Number($("#newValue").val())
            },
            type: 'POST',
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
                
                showPlayer();
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
    
    
});



$(document).on("click" ,".update-army-player", function (){
   
    var idPlayer = $(this).parents("tr").attr("data-id-user");
    var idCity = $(this).parents("tr").attr("data-id-city");
    var name  = $(this).parents("tr").attr("data-p-name");
    var army = $(this).attr("data-army");
    var amount  = $(this).attr("data-amount");
    
    alertBox.confirm(`تعديل القوات للاعب (${name}) </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>` , function (){
        
        $.ajax({
            
            url: "api/player.php",
            data:{
                UPDATE_PLAYER_ARMY:true,
                id_player:idPlayer,
                id_city:idCity,
                army:army,
                amount:Number($("#newValue").val())
            },
            type: 'POST',
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
                
                showPlayer();
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
    
    
});



$(document).on("click" ,".update-player-matrial" , function (){
    
   var idPlayer = ID_PLAYER_TO_SHOW;
   var matrial  =  $(this).attr("data-matrial");
   var table    = $(this).attr("data-matrial-table");
   var amount    = $(this).attr("data-amount");
   
   
   alertBox.confirm(`تعديل المواد للاعب  </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>` , function (){
        
        $.ajax({
            
            url: "api/onePlayer.php",
            data:{
                UPDATE_PLAYER_MATRIAL:true,
                id_player:idPlayer,
                mat_table:table,
                matrial:matrial,
                amount:Number($("#newValue").val())
            },
            type: 'POST',
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
               
                showPlayer();
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
   
    
});



$(document).on("click" ,".delete-equip-player" , function (){
    
    var name     = $(this).attr("data-name");
    var id_equip = $(this).attr("data-id-equip");
    var part = $(this).attr("data-part");
    
    alertBox.confirm(`  حذف المعدة ( ${name} ) للاعب  </br> </br> ` , function (){
        
        $.ajax({
            
            url: "api/onePlayer.php",
            data:{
                DELETE_EQUIP:true,
                id_player:ID_PLAYER_TO_SHOW,
                part: part,
                id_equip: id_equip
            },
            type: 'POST',
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
                console.log(data)
                showPlayer();
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
});