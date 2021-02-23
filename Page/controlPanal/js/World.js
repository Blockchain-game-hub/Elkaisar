

var World = {};


World.getUnitData = function (){
    
    return $.ajax({
       
        url: BASE_URL + "/js/json/worldUnitData.json",
        type: "GET",
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            
        },
        error: function (jqXHR, textStatus, errorThrown) {
            
        }
       
   }); 
   
};


$(document).ready(function (){
    
   World.getUnitData().done(function(data){
       
       
     
       
        console.log(data)
        var List = ``;
        
        for (var ii in data)
        {
            
            List += `<option value="${ii}" data-max-lvl="${data[ii].maxLvl}">${data[ii].Title}</option>`;
            
        }
       
       
       $(".allWorldUnitSelectFrom").html(List);
       
   });
    
    
});



$(document).on("click", "#changeLvlByType", function (){
    
    var unitType = $("#unitTypeToChangeLvl").val();
    
    
    alertBox.confirmInputDialog("تاكيد تغير مستوى", function (){
        
        var lvl = $("#alert-input").val();
        
        $.ajax({
           
            url: BASE_URL + "/cp/AWorld/changeLvlByType",
            type: "POST",
            data:{
               unitType: unitType,
               lvl: lvl
            },
            beforeSend: function (xhr) {
                
            },
            success: function (data, textStatus, jqXHR) {
                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم تعديل المستوى  بنجاح");
                    
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
                
            },
            error: function (jqXHR, textStatus, errorThrown) {
                
            }
            
        });
    });
    
});
