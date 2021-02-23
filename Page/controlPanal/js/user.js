
$(document).on("click", ".user-change-pass", function (){

    var idUser = $(this).attr("data-id-user");
    
    alertBox.confirmInputDialog("تاكيد تغير كلمة المرور للاعب", function (){
        
        var newPass = $("#alert-input").val();
        
        $.ajax({
            url: BASE_URL + "/home/HControlPanal/changePassByAdmin",
            data:{
                idUser  : idUser,
                newPass : newPass
            },
            type: 'POST',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم تعديل كلمة المرور بنجاح");
                    
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
        
    });
    
});




$(document).on("click", ".change-user-group", function (){

    var idUser = $(this).attr("data-id-user");
    
    alertBox.confirmInputDialog("تاكيد تغير صلاحيات للاعب", function (){
        
        var newGroup = $("#select-new-user-group").val();
        
        $.ajax({
            url: BASE_URL + "/home/HControlPanal/changeUserGroup",
            data:{
                idUser  : idUser,
                newGroup : newGroup
            },
            type: 'POST',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم تغير صلاحيات العضو بنجاح");
                    
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
        
    });
    
   $("#alert-input").replaceWith(`
                <select id="select-new-user-group">
                    <option value="6">ادارة مطلقة</option>
                    <option value="5">ادارة عادية</option>
                    <option value="4">مشرف</option>
                    <option value="0">لاعب عادى</option>
                </select>
    `);
    
});




$(document).on("click", ".change-user-name", function (){

    var idUser = $(this).attr("data-id-user");
    
    alertBox.confirmInputDialog("تاكيد تغير اسم دخول للاعب", function (){
        
        var newUserName = $("#alert-input").val();
        
        $.ajax({
            url: BASE_URL + "/home/HControlPanal/changeUserName",
            data:{
                idUser      : idUser,
                newUserName : newUserName
            },
            type: 'POST',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم تعديل  بنجاح");
                    
                }else if(jsonData.state === "error_2"){
                    alert("يوجد عضو بنفس اسم الدخول");
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
        
    });
    
});



$(document).on("click", ".change-user-email", function (){

    var idUser = $(this).attr("data-id-user");
    
    alertBox.confirmInputDialog("تاكيد تغير  بريد للاعب", function (){
        
        var newUserEmail = $("#alert-input").val();
        
        $.ajax({
            url: BASE_URL + "/home/HControlPanal/changeUserEmail",
            data:{
                idUser      : idUser,
                newUserEmail : newUserEmail
            },
            type: 'POST',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم تعديل  بنجاح");
                    
                }else if(jsonData.state === "error_2"){
                    alert("يوجد عضو بنفس اسم الاميل");
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
        
    });
    
});



$(document).on("click", ".pannUser", function (){

    var idUser = $(this).attr("data-id-user");
    
    alertBox.confirmInputDialog("تاكيد حظر للاعب", function (){
        
        var duration = $("#alert-input").val();
        
        $.ajax({
            url: BASE_URL + "/home/HControlPanal/panneUser",
            data:{
                idUser      : idUser,
                duration    : duration
            },
            type: 'POST',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

                if(jsonData.state === "ok"){
                    
                    $(".close-alert").click();
                    alert("تم الحظر  بنجاح");
                    
                }else{

                    alert("لست عضو فى الادارة" + data);
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
        
    });
    
});

$(document).on("click", "#searchByUserName", function (){
    
    var name = $("#UserSearchInput").val();
    
    $.ajax({
            url: BASE_URL + "/home/HControlPanal/searchByUserName",
            data:{
                seg    : name
            },
            type: 'GET',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

               var userTable = `<tr>
                                    <th class="first" width="177"> الاعضاء</th>
                                    <th>تغير كلمة المرور</th>
                                    <th>تغير الصلاحيات</th>
                                    <th>تغيرالاسم</th>
                                    <th>تغير البريد</th>
                                    <th class="last">حظر</th>
                                </tr>`;
                
                for(var ii in jsonData){
                    
                    userTable += `  <tr data-id-user="${jsonData[ii].id_user}">
                                        <td class="first">- ${jsonData[ii].user_name} (${jsonData[ii].email}) (${jsonData[ii].user_group})</td>
                                        <td class="user-change-pass" data-id-user="${jsonData[ii].id_user}">    <img src="../img/add-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-group" data-id-user="${jsonData[ii].id_user}">   <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-name" data-id-user="${jsonData[ii].id_user}">    <img src="../img/save-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-email" data-id-user="${jsonData[ii].id_user}">   <img src="../img/edit-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="pannUser" data-id-user="${jsonData[ii].id_user}">            <img src="../img/add-icon.gif" width="16" height="16" alt="add" /></td>
                                    </tr>`;
                    
                }
                
                $("#user-table").html(userTable);
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    
    
});

$(document).on("click", "#searchByUserEmail", function (){
    
    var name = $("#UserSearchInput").val();
    
    $.ajax({
            url: BASE_URL + "/home/HControlPanal/searchByUserEmail",
            data:{
                seg    : name
            },
            type: 'GET',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

               var userTable = `<tr>
                                    <th class="first" width="177"> الاعضاء</th>
                                    <th>تغير كلمة المرور</th>
                                    <th>تغير الصلاحيات</th>
                                    <th>تغيرالاسم</th>
                                    <th>تغير البريد</th>
                                    <th class="last">حظر</th>
                                </tr>`;
                
                for(var ii in jsonData){
                    
                    userTable += `  <tr data-id-user="${jsonData[ii].id_user}">
                                        <td class="first">- ${jsonData[ii].user_name} (${jsonData[ii].email}) (${jsonData[ii].user_group})</td>
                                        <td class="user-change-pass" data-id-user="${jsonData[ii].id_user}">    <img src="../img/add-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-group" data-id-user="${jsonData[ii].id_user}">   <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-name" data-id-user="${jsonData[ii].id_user}">    <img src="../img/save-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-email" data-id-user="${jsonData[ii].id_user}">   <img src="../img/edit-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="pannUser" data-id-user="${jsonData[ii].id_user}">            <img src="../img/add-icon.gif" width="16" height="16" alt="add" /></td>
                                    </tr>`;
                    
                }
                
                $("#user-table").html(userTable);
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    
    
});

$(document).on("click", "#searchForAdmins", function (){
    
    var name = $("#UserSearchInput").val();
    
    $.ajax({
            url: BASE_URL + "/home/HControlPanal/searchForAdmin",
            data:{
                seg    : name
            },
            type: 'GET',
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {


                if(!isJson(data))
                    alert(data);

                var jsonData = JSON.parse(data);

               var userTable = `<tr>
                                    <th class="first" width="177"> الاعضاء</th>
                                    <th>تغير كلمة المرور</th>
                                    <th>تغير الصلاحيات</th>
                                    <th>تغيرالاسم</th>
                                    <th>تغير البريد</th>
                                    <th class="last">حظر</th>
                                </tr>`;
                
                for(var ii in jsonData){
                    
                    userTable += `  <tr data-id-user="${jsonData[ii].id_user}">
                                        <td class="first">- ${jsonData[ii].user_name} (${jsonData[ii].email}) (${jsonData[ii].user_group})</td>
                                        <td class="user-change-pass" data-id-user="${jsonData[ii].id_user}">    <img src="../img/add-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-group" data-id-user="${jsonData[ii].id_user}">   <img src="../img/hr.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-name" data-id-user="${jsonData[ii].id_user}">    <img src="../img/save-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="change-user-email" data-id-user="${jsonData[ii].id_user}">   <img src="../img/edit-icon.gif" width="16" height="16" alt="" /></td>
                                        <td class="pannUser" data-id-user="${jsonData[ii].id_user}">            <img src="../img/add-icon.gif" width="16" height="16" alt="add" /></td>
                                    </tr>`;
                    
                }
                
                $("#user-table").html(userTable);
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });
    
    
});