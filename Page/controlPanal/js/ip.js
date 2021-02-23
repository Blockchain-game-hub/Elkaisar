$(document).on("keyup" ,"#SearchOnePlayerValIp" ,function (){
    
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
                
                list += ` <li class="selectUserToShowIp" data-id-player = "${jsonData[iii].id_player}">${jsonData[iii].name} (${pormotion[jsonData[iii].porm].ar_title})</li>`;
                
            }
            
            
            $("#drop-down ul").html(list);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }

    });
    
});

function checkPlayerIp(idPlayer){
    
    $.ajax({
        url: "api/ip.php",
        data:{
            CHECK_PLAYER_IP:true,
            id_player: idPlayer
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            console.log(data);
            var jsonData = JSON.parse(data);
            
            var table = `<tr>
                            <th class="first" width="177">اللاعب</th>
                            <th>الرتية</th>
                            <th>ip</th>
                            <th>تاريخ</th>
                            <th>عدد مرات</th>
                            <th class="last">فحص ip</th>
                        </tr>`;
            for(var iii in jsonData.other){
                
                table += `  <tr data-id-user="${jsonData.other[iii].id_player}">
                                <td class="how-player-gold">${jsonData.other[iii].name}</td>
                                <td class="first ">${pormotion[jsonData.other[iii].porm].ar_title}</td>
                                <td class="change-pass"><a target="_blank" href="https://whatismyipaddress.com/ip/${jsonData.other[iii].ipv4}">${jsonData.other[iii].ipv4}</a></td>
                                <td class="show-box-mat">${new Date(jsonData.other[iii].time_stamp * 1000)}</td>
                                <td class="show-box-mat">${jsonData.other[iii].times}</td>
                                <td class="see-ip last"><img src="img/add-icon.gif" width="16" height="16" alt="add" class="checkThisIp" data-id-player="${jsonData.other[iii].id_player}"/></td>
                            </tr>`;
                
                
            }
            
            
            $("#showTable").html(table);
            
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown)
        }
    });
    
}


$(document).on("click", ".selectUserToShowIp" , function (){
    
    var idPlayer = $(this).attr("data-id-player");
    checkPlayerIp(idPlayer);
});

$(document).on("click", ".checkThisIp" , function (){
    
    var idPlayer = $(this).attr("data-id-player");
    checkPlayerIp(idPlayer);
});

