

$(document).on("keyup", "#SearchOnePlayerValIp", function () {

    var searchVal = $(this).val();

    if (searchVal.length < 2)
        return;

    $("#drop-down ul").html("");



    $.ajax({
        url: BASE_URL + "/cp/APlayer/searchByName",
        data: {
            seg: searchVal
        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

            $("#drop-down ul").html("");
            if (!isJson(data))
                alert(data);

            var jsonData = JSON.parse(data);

            var userTable = ``;

            for (var ii in jsonData) {
                userTable += ` <li class="selectUserToShowIp" data-id-player = "${jsonData[ii].id_player}">${jsonData[ii].name} (${pormotion[Math.min(Math.max(jsonData[ii].porm, 0), 29)].ar_title})</li>`;
               
            }

             $("#drop-down ul").html(userTable);
        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });

});

function checkPlayerIp(idPlayer) {

    $.ajax({
        url: `${BASE_URL}/cp/AIp/getPlayerIps`,
        data: {
            idPlayer: idPlayer
        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

          if(!isJson(data))
                alert(data);
            var jsonData = JSON.parse(data);
            
            console.log(jsonData)

            var table = `<tr>
                            <th class="first" width="177">اللاعب</th>
                            <th>الرتية</th>
                            <th>البلد</th>
                            <th>ip</th>
                            <th>تاريخ</th>
                            <th>عدد مرات</th>
                            <th class="last">فحص ip</th>
                        </tr>`;
            for (var iii in jsonData.users) {

                table += `  <tr data-id-user="${jsonData.users[iii].id_player}">
                                <td class="">${jsonData.users[iii].name}</td>
                                <td class="first ">${pormotion[jsonData.users[iii].porm].ar_title}</td>
                                <td class="">${jsonData.users[iii].coName || "-----"}</td>
                                <td class=""><a target="_blank" href="https://whatismyipaddress.com/ip/${jsonData.users[iii].ipv4}">${jsonData.users[iii].ipv4}</a></td>
                                <td class="">${jsonData.users[iii].time_stamp }</td>
                                <td class="">${jsonData.users[iii].times}</td>
                                <td class="see-ip  last"><img src="../img/add-icon.gif" width="16" height="16" alt="add" class="checkThisIp" data-id-player="${jsonData.users[iii].id_player}"/></td>
                            </tr>`;


            }


            $("#showTable").html(table);


        },
        error: function (jqXHR, textStatus, errorThrown) {
            console.log(errorThrown)
        }
    });

}


$(document).on("click", ".selectUserToShowIp", function () {

    var idPlayer = $(this).attr("data-id-player");
    checkPlayerIp(idPlayer);
});

$(document).on("click", ".checkThisIp", function () {

    var idPlayer = $(this).attr("data-id-player");
    console.log(idPlayer)
    checkPlayerIp(idPlayer);
});

