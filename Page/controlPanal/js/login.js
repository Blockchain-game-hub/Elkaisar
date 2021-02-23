$(document).ready(function (){
    
    $('#log-in-btn').click(function (){
        
        var user_name = $("#user-name").val();;
        var password  = $('#password').val();
        var server    = $("#server-id").val();
        
        
     
            $.ajax({
                url: BASE_URL + "/home/HControlPanal/logIn",
                data:{
                    UserName:user_name,
                    password: password
                },
                type: 'POST',
                beforeSend: function (xhr) {
                    
                },
                success: function (data, textStatus, jqXHR) {
                    
                    
                    if(!isJson(data))
                        alert(data);
                    
                    var jsonData = JSON.parse(data);
                    console.log(jsonData)
                    alert(data)
                    if(jsonData.state === "ok"){
                        window.location.replace(`User?server=${server}&AdminToken=${jsonData.AdminToken}`);
                    }else{
                        
                        alert("لست عضو فى الادارة" + data);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    
                }
            });
    });
    
});

