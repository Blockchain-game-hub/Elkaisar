

$(document).on("change" , "#offer-select, #offer-order-select, #offer-order-by-select" , function (){
   
    var offer = $("#offer-select").val();    
    var order = $("#offer-order-select").val();    
    var orderBy = $("#offer-order-by-select").val();
    
    $.ajax({
        url: "api/market.php",
        data: {
            GET_ALL_MARKET_OFFERS: true,
            order_by: orderBy,
            order: order,
            offer: offer
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
            
            var jsonData = JSON.parse(data);
            
            var table = `<tr>
                            <th class="first"> اللاعب</th>
                            <th>المدينة</th>
                            <th>المورد</th>
                            <th>العدد</th>
                            <th>تم بيع</th>
                            <th>السعر</th>
                            <th class="last">الغاء</th>
                        </tr>`;
            
            for(var iii in jsonData){
                
                table += `<tr data-p-name="${jsonData[iii].name}" data-id-deal="${jsonData[iii].id_deal}" data-id-user="${jsonData[iii].id_player}" data-id-city="${jsonData[iii].id_city}">
                            <td>${jsonData[iii].name}</td>
                            <td>${jsonData[iii].c_name}</td>
                            <td>${jsonData[iii].resource}</td>
                            <td>${jsonData[iii].amount}</td>
                            <td>${jsonData[iii].done}</td>
                            <td>${jsonData[iii].unit_price}</td>
                            <td class="last remove-deal"><img src="img/add-icon.gif" alt="add" width="16" height="16"></td>
                        </tr>`;
                
                
            }
            
            $("#marketOffers").html(table);
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
    });
    
});


$(document).on("click" ,".remove-deal", function (){
   
    var idPlayer = $(this).parents("tr").attr("data-id-user");
    var idCity   = $(this).parents("tr").attr("data-id-city");
    var name     = $(this).parents("tr").attr("data-p-name");
    var idDeal   = $(this).parents("tr").attr("data-id-deal");
    
    alertBox.confirm(` الغاء العرض  للاعب (${name}) </br> </br>` , function (){
        
        $.ajax({
            
            url: "api/market.php",
            data:{
                CANCEL_PLAYER_OFFER:true,
                id_player:idPlayer,
                id_city:idCity,
                id_deal:idDeal
            },
            type: 'POST',
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
                
                $("#offer-select").trigger("change");
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
        
    });
    
    
    
});



