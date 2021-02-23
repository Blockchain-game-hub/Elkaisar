


var MenuPages = {};

MenuPages.searchPlayer = function (seg)
{
    return $.ajax({
       
        url: "../api/player.php",
        data:{
            SEARCH_PLAYER_BY_NAME: true,
            name_seg: seg
        },
        type: 'GET',
        beforeSend: function (xhr) {
            
        },
        success: function (data, textStatus, jqXHR) {
            console.log(data)
        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown);
        }
        
    });
};




$(document).on("keyup", "#p-pan-SearchOnePlayerVal", function (){
   
    
    var seg= $(this).val();
    
    MenuPages.searchPlayer(seg).done(function (data){
        
        if(!isJson(data)){
            alert(data);
            return ;
        }
        
        
        var jsonData = JSON.parse(data);
        var list = "";
        
        for(var iii in jsonData){
            list += `<li class="p-pann-selectUserToShow" data-id-player="${jsonData[iii].id_player}">${jsonData[iii].name} (${pormotion[jsonData[iii].porm].ar_title})</li>`;
        }
        
        $("#drop-down ul").html(list);
    });
    
});