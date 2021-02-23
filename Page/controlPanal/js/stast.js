

$(document).on("change" , "#RESOURCE_STAT_OPTION" , function (){
   
    var option = $(this).val();
    
    $.ajax({
        url: "api/player.php",
        data: {
            GET_TOP_BY_RESOURCE: true,
            order_by: option
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            var jsonData = JSON.parse(data);
            
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
            
            for(var iii in jsonData){
                
                table += `<tr data-p-name="${jsonData[iii].name}" data-id-user="${jsonData[iii].id_player}" data-id-city="${jsonData[iii].id_city}">
                            <td>${jsonData[iii].name}</td>
                            <td class="update-resource-player" data-resource="food" data-amount="${jsonData[iii].food}">${jsonData[iii].food}</td>
                            <td class="update-resource-player" data-resource="wood" data-amount="${jsonData[iii].wood}">${jsonData[iii].wood}</td>
                            <td class="update-resource-player" data-resource="stone" data-amount="${jsonData[iii].stone}">${jsonData[iii].stone}</td>
                            <td class="update-resource-player" data-resource="metal" data-amount="${jsonData[iii].metal}">${jsonData[iii].metal}</td>
                            <td class="update-resource-player" data-resource="coin" data-amount="${jsonData[iii].coin}">${jsonData[iii].coin}</td>
                            <td class="update-resource-player" data-resource="gold" data-amount="${jsonData[iii].gold}">${jsonData[iii].gold}</td>
                            <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData[iii].id_player}&server=${SERVER_ID}"><img src="img/add-icon.gif" alt="add" width="16" height="16"></a></td>
                        </tr>`;
                
                
            }
            
            $("#statstic").html(table);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
    });
    
});


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
                
                $("#RESOURCE_STAT_OPTION").trigger("change");
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
    
    
});

$(document).on("change" , "#ARMY_STAT_OPTION" , function (){
   
    var option = $(this).val();
    
    $.ajax({
        url: "api/player.php",
        data: {
            GET_TOP_BY_ARMY: true,
            order_by: option
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            
            var jsonData = JSON.parse(data);
            
            var table = `<tr>
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
            
            for(var iii in jsonData){
                
                table += `<tr data-p-name="${jsonData[iii].name}" data-id-user="${jsonData[iii].id_player}" data-id-city="${jsonData[iii].id_city}">
                            <td>${jsonData[iii].name}</td>
                            <td class="update-army-player" data-army="army_a" data-amount="${jsonData[iii].army_a}">${jsonData[iii].army_a}</td>
                            <td class="update-army-player" data-army="army_b" data-amount="${jsonData[iii].army_b}">${jsonData[iii].army_b}</td>
                            <td class="update-army-player" data-army="army_c" data-amount="${jsonData[iii].army_c}">${jsonData[iii].army_c}</td>
                            <td class="update-army-player" data-army="army_d" data-amount="${jsonData[iii].army_d}">${jsonData[iii].army_d}</td>
                            <td class="update-army-player" data-army="army_e" data-amount="${jsonData[iii].army_e}">${jsonData[iii].army_e}</td>
                            <td class="update-army-player" data-army="army_f" data-amount="${jsonData[iii].army_f}">${jsonData[iii].army_f}</td>
                            <td class="update-army-player" data-army="spies"  data-amount="${jsonData[iii].spies}">${jsonData[iii].spies}</td>
                            <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData[iii].id_player}&server=${SERVER_ID}"><img src="img/add-icon.gif" alt="add" width="16" height="16"></a></td>
                        </tr>`;
                
                
            }
            
            $("#statstic").html(table);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
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
                
                $("#ARMY_STAT_OPTION").trigger("change");
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
    
    
});