

$(document).on("click" , "#black-list .matrial-unit" , function (){
   
   var matrial = $(this).data("matrial");
   
    alertBox.confirm("تأكيد ازالة المادة من قائمة المواد لمحظورة" , function (){
        
        $.ajax({
           
            url: "api/tradeCenter.php",
            data:{
                
                REMOVE_FROM_BLACKLIST: true,
                matrial: matrial
                
            },
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                if(data === "done"){
                    
                    blackListRefresh();
                    
                }else{
                    
                    
                    alert("error 504");
                }
               
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            },
            beforeSend: function (xhr) {
                
            }
            
        });
        
    });
    
});


$(document).on("click" , ".add-to-BL" , function (){
   
    var matrial =  $(this).data("matrial");
    
    alertBox.confirm(`تاكيد اضافة (${Elkaisar.BaseData.Items[matrial].name}) الى قائمة المواد المحظورة`  , function (){
        
        $.ajax({
           
            url: "api/tradeCenter.php",
            data:{
                
                ADD_MATRIAL_BLACK_LIST: true,
                matrial: matrial
                
            },
            type: 'POST',
            success: function (data, textStatus, jqXHR) {
                
                if(data === "done"){
                    
                    blackListRefresh();
                    
                }else{
                    alert(data);
                    
                }
               
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            },
            beforeSend: function (xhr) {
                
            }
            
        });
        
    });
    
    
});

function blackListRefresh(){
    
     showAllMatrial().done(function (data){
                    
            TradeBlackList().done(function (blackList){

                var jsonData = JSON.parse(blackList);
                 console.log(jsonData)
                var listBlack = "";
                var allList = "";
                for(var iii in Elkaisar.BaseData.Items){

                    if(jsonData.indexOf(iii) < 0){

                        allList += `<div class="matrial-unit add-to-BL" data-matrial="${iii}">
                                        <img src="../${Elkaisar.BaseData.Items[iii].image}"/>
                                        <div class="amount"></div>
                                        <div class="name"><span>${Elkaisar.BaseData.Items[iii].name}</span></div>
                                    </div>`;

                    }else{

                        listBlack += `<div class="matrial-unit" data-matrial="${iii}" style="width: 50%">
                                            <img src="../${Elkaisar.BaseData.Items[iii].image}"/>
                                            <div class="amount"></div>
                                            <div class="name"><span>${Elkaisar.BaseData.Items[iii].name}</span></div>
                                        </div>`;

                    }
                }

                $("#matrial-list").html(allList);
                $("#black-list").html(listBlack);
            });

        });
    
}