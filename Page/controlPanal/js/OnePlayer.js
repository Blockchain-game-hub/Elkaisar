


$(document).on("focusin", "#SearchOnePlayerVal", function () {
    $("#drop-down ul").show();
});

$(document).on("keyup", "#SearchOnePlayerVal", function () {

    var searchVal = $(this).val();

    if (searchVal.length < 2)
        return;

    $("#drop-down ul").html("");
    $.ajax({
        url: `${BASE_URL}/cp/APlayer/searchByName`,
        data: {
            seg: searchVal

        },
        type: 'GET',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {
            $("#drop-down ul").html("");

            var jsonData = JSON.parse(data);
            var list = "";

            for (var iii in jsonData) {

                list += ` <li class="selectUserToShow" data-id-player = "${jsonData[iii].id_player}">${jsonData[iii].name} (${pormotion[jsonData[iii].porm].ar_title})</li>`;

            }


            $("#drop-down ul").html(list);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }

    });

});

$(document).on("click", ".selectUserToShow", function () {

    var idPlayer = $(this).attr("data-id-player");

    $("#drop-down ul").hide();

    ID_PLAYER_TO_SHOW = idPlayer;
    showPlayer(idPlayer);

});



function showPlayer(idPlayer) {


    idPlayer = ID_PLAYER_TO_SHOW;

    $.ajax({

        url: `${BASE_URL}/cp/APlayerState/getPlayerFullData`,
        data: {
            idPlayer: idPlayer
        },
        type: 'POST',
        beforeSend: function (xhr) {

        },
        success: function (data, textStatus, jqXHR) {

            if (!isJson(data))
                return console.log(data, alert(data));
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

            for (var iii in jsonData.City) {

                table += `<tr data-p-name="${jsonData.Player.name}" data-id-user="${jsonData.Player.id_player}" data-id-city="${jsonData.City[iii].id_city}">
                            <td>${jsonData.Player.name}</td>
                            <td class="update-resource-player" data-resource="food" data-amount="${jsonData.City[iii].food}">${jsonData.City[iii].food}</td>
                            <td class="update-resource-player" data-resource="wood" data-amount="${jsonData.City[iii].wood}">${jsonData.City[iii].wood}</td>
                            <td class="update-resource-player" data-resource="stone" data-amount="${jsonData.City[iii].stone}">${jsonData.City[iii].stone}</td>
                            <td class="update-resource-player" data-resource="metal" data-amount="${jsonData.City[iii].metal}">${jsonData.City[iii].metal}</td>
                            <td class="update-resource-player" data-resource="coin" data-amount="${jsonData.City[iii].coin}">${jsonData.City[iii].coin}</td>
                            <td class="update-resource-player" data-resource="gold" data-amount="${jsonData.Player.gold}">${jsonData.Player.gold}</td>
                            <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData.Player.id_player}&server=${SERVER_ID}"><img src="../img/add-icon.gif" alt="add" width="16" height="16"></a></td>
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

            for (var iii in jsonData.City) {

                tableArmy += `<tr data-p-name="${jsonData.Player.name}" data-id-user="${jsonData.Player.id_player}" data-id-city="${jsonData.City[iii].id_city}">
                                    <td>${jsonData.City[iii].name}</td>
                                    <td class="update-army-player" data-army="army_a" data-amount="${jsonData.City[iii].army_a}">${jsonData.City[iii].army_a}</td>
                                    <td class="update-army-player" data-army="army_b" data-amount="${jsonData.City[iii].army_b}">${jsonData.City[iii].army_b}</td>
                                    <td class="update-army-player" data-army="army_c" data-amount="${jsonData.City[iii].army_c}">${jsonData.City[iii].army_c}</td>
                                    <td class="update-army-player" data-army="army_d" data-amount="${jsonData.City[iii].army_d}">${jsonData.City[iii].army_d}</td>
                                    <td class="update-army-player" data-army="army_e" data-amount="${jsonData.City[iii].army_e}">${jsonData.City[iii].army_e}</td>
                                    <td class="update-army-player" data-army="army_f" data-amount="${jsonData.City[iii].army_f}">${jsonData.City[iii].army_f}</td>
                                    <td class="update-army-player" data-army="spies"  data-amount="${jsonData.City[iii].spies}">${jsonData.City[iii].spies}</td>
                                    <td><a target="_blank" href="http://www.elkaisar.com/player-examine-99.php?idPlayer=${jsonData.Player.id_player}&server=${SERVER_ID}"><img src="../img/add-icon.gif" alt="add" width="16" height="16"></a></td>
                                </tr>`;


            }



            $("#playerCityArmy").html(tableArmy);


            var matrialList = "";
            var idItem = "";
            for (var iii in jsonData.Item) {
                idItem = jsonData.Item[iii].id_item;
                if (ElkaisarCp.Items[idItem] && jsonData.Item[iii].amount > 0) {

                    matrialList += `<div class="matrial-unit update-player-matrial" data-matrial="${idItem}"  data-amount = "${jsonData.Item[iii].amount}" prize-type="matrial" data-matrial="0" style="width: 10%; height:50px;">
                                        <img src="../${ElkaisarCp.Items[idItem].image}">
                                        <div class="amount">${jsonData.Item[iii].amount}</div>
                                        <div class="name"><span>${ElkaisarCp.Items[idItem].name}</span></div>
                                    </div>`;

                }

            }

            $("#playerMatrialList").html(matrialList);


            var EquipList = "";
            var UEqui = {};
            var UEquiD = {};
            for (var iii in jsonData.Equip) {
                UEqui = jsonData.Equip[iii];
                UEquiD = ElkaisarCp.Equips[`${UEqui.type}_${UEqui.part}_${UEqui.lvl || 1}`];
                if (UEquiD) {

                    EquipList += `<div class="matrial-unit delete-equip-player" data-id-equip="${UEqui.id_equip}" data-part="${UEqui.part}" data-name="${UEquiD.name}" prize-type="matrial" data-matrial="0" style="width: 10%; height:50px;">
                                        <img src="../${UEquiD.image}">
                                        <div class="amount"></div>
                                        <div class="name"><span>${UEquiD.name}</span></div>
                                    </div>`;

                }

            }

            $("#playerEquipList").html(EquipList);


            var HeroList = "";
            $(`#playerHeros1 ul`).html("");
            $(`#playerHeros2 ul`).html("");
            $(`#playerHeros3 ul`).html("");
            $(`#playerHeros4 ul`).html("");
            $(`#playerHeros5 ul`).html("");
            for (var Hi in jsonData.Heros) {

                $(`#playerHeros${jsonData.Heros[Hi].id_city % 10} ul`).append(
                        `   <div class="matrial-unit view-hero-to-update" data-id-hero="${jsonData.Heros[Hi].id_hero}">
                                    <img src="../${ElkaisarCp.BaseData.HeroAvatar[jsonData.Heros[Hi].avatar % 19]}">
                                    <div class="amount">${jsonData.Heros[Hi].lvl}</div>
                                    <div class="name"><span>${jsonData.Heros[Hi].name}</span></div>
                                    <div class="name">
                                        <span style="color: #008c10; font-weight: bold">${jsonData.Heros[Hi].point_a}</span>
                                        <span style="color: #b43d02; font-weight: bold">${jsonData.Heros[Hi].point_b}</span>
                                        <span style="color: #0065ac; font-weight: bold">${jsonData.Heros[Hi].point_c}</span>
                                        <br>
                                        <span style="color: #008c10; font-weight: bold">${jsonData.Heros[Hi].p_b_a}</span>
                                        <span style="color: #b43d02; font-weight: bold">${jsonData.Heros[Hi].p_b_b}</span>
                                        <span style="color: #0065ac; font-weight: bold">${jsonData.Heros[Hi].p_b_c}</span>
                                    </div>
                                </div>`)

            }


        },
        error: function (jqXHR, textStatus, errorThrown) {

        }

    });

}


$(document).on("click", ".update-resource-player", function () {

    var idPlayer = $(this).parents("tr").attr("data-id-user");
    var idCity = $(this).parents("tr").attr("data-id-city");
    var name = $(this).parents("tr").attr("data-p-name");
    var resource = $(this).attr("data-resource");
    var amount = $(this).attr("data-amount");

    alertBox.confirm(`تعديل الموارد للاعب (${name}) </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>`, function () {

        $.ajax({

            url: `${BASE_URL}/cp/APlayerState/updataCityRes`,
            data: {
                idCity: idCity,
                Res: resource,
                amount: Number($("#newValue").val())
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



$(document).on("click", ".update-army-player", function () {

    var idPlayer = $(this).parents("tr").attr("data-id-user");
    var idCity = $(this).parents("tr").attr("data-id-city");
    var name = $(this).parents("tr").attr("data-p-name");
    var army = $(this).attr("data-army");
    var amount = $(this).attr("data-amount");

    alertBox.confirm(`تعديل القوات للاعب (${name}) </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>`, function () {

        $.ajax({

            url: `${BASE_URL}/cp/APlayerState/updateCityArmy`,
            data: {
                idCity: idCity,
                Army: army,
                amount: Number($("#newValue").val())
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



$(document).on("click", ".update-player-matrial", function () {

    var idPlayer = ID_PLAYER_TO_SHOW;
    var matrial = $(this).attr("data-matrial");
    var amount = $(this).attr("data-amount");


    alertBox.confirm(`تعديل المواد للاعب  </br> </br>
                        <input id="newValue" type="text"  value="${amount}"/>`, function () {

        $.ajax({

            url: `${BASE_URL}/cp/APlayerState/updatePlayerItem`,
            data: {
                idPlayer: idPlayer,
                Item: matrial,
                amount: Number($("#newValue").val())
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



$(document).on("click", ".delete-equip-player", function () {

    var name = $(this).attr("data-name");
    var id_equip = $(this).attr("data-id-equip");
    var part = $(this).attr("data-part");

    alertBox.confirm(`  حذف المعدة ( ${name} ) للاعب  </br> </br> `, function () {

        $.ajax({

            url: `${BASE_URL}/cp/APlayerState/deletePlayerEquip`,
            data: {
                idEquip: id_equip
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


ElkaisarCp.PlayerState = {};

ElkaisarCp.PlayerState.retHeroEquip = function (Equips, part) {


    var EquipList = {
        boot: null, armor: null, shield: null, helmet: null,
        sword: null, belt: null, ring: null, steed: null,
        pendant: null, necklace: null
    };
//
    for (var iii in Equips)
        EquipList[Equips[iii].part] = Equips[iii];
    if (EquipList[part])
        return {
            Image: ElkaisarCp.Equips[`${EquipList[part].type}_${EquipList[part].part}_${EquipList[part].lvl || 1}`].image,
            idEquip: EquipList[part].id_equip
        };

    return {
        Image: "images/tech/no_army.png",
        idEquip: -1
    };


};



ElkaisarCp.PlayerState.getHeroEquipRow = function (JsonData) {
    return `<ul>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'armor').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'armor').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'sword').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'sword').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'boot').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'boot').Image})"></div>
                    </div>
                </li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'shield').idEquip}">
                <li>
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'shield').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'helmet').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'helmet').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'belt').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'belt').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'necklace').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'necklace').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'pendant').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'pendant').Image})"></div>
                    </div>
                </li>
                <li style="margin-left: 25%;" class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'ring').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'ring').Image})"></div>
                    </div>
                </li>
                <li class="remove-hero-equip" data-id-equip="${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'steed').idEquip}">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../${ElkaisarCp.PlayerState.retHeroEquip(JsonData.HeroEquip, 'steed').Image})"></div>
                    </div>
                </li>
            </ul>`;
}


ElkaisarCp.PlayerState.getHeroArmyRow  = function (JsonData){
    
    
    return `<ul>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="f_1">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.f_1_type]})">
                            <div class="amount army-over-10k">${JsonData.HeroArmy.f_1_num}</div>
                        </div>
                    </div>
                </li>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="f_2">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.f_2_type]})">
                            <div class="amount army-over-10k">${JsonData.HeroArmy.f_2_num}</div>
                        </div>
                    </div>
                </li>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="f_3">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.f_3_type]})">
                            <div class="amount army-over-10k">${JsonData.HeroArmy.f_3_num}</div>
                        </div>
                    </div>
                </li>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="b_1">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.b_1_type]})">
                            <div class="amount ">${JsonData.HeroArmy.b_1_num}</div>
                        </div>
                    </div>
                </li>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="b_2">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.b_2_type]})">
                            <div class="amount ">${JsonData.HeroArmy.b_2_num}</div>
                        </div>
                    </div>
                </li>
                <li class="update-hero-army" data-id-hero="${JsonData.HeroArmy.id_hero}" data-army-place="b_3">
                    <div class="wrapper">
                        <div class="img" style="background-image: url(../images/tech/${ElkaisarCp.BaseData.ArmyImage[JsonData.HeroArmy.b_3_type]})">
                            <div class="amount ">${JsonData.HeroArmy.b_3_num}</div>
                        </div>
                    </div>
                </li>
            </ul>`;
};

function showPlayerHero(idHero) {

    $.ajax({
        url: `${BASE_URL}/cp/APlayerState/getHeroDetails`,
        type: 'GET',
        data: {
            idHero: idHeroToView
        },
        beforeSend: function (xhr) {
            $("#over_lay").remove();
        },
        success: function (data, textStatus, jqXHR) {

            if (!isJson(data))
                return console.log(data, alert(data))

            var JsonData = JSON.parse(data);
            var HeroRev = ` 
                    <div id="over_lay">
                        <div id="overview-palace-bg">
                            <div class="header">
                                <div class="right">
                                    <div id="close-over-lay" class="close"></div>
                                </div>
                                <div class="middel">
                                    <div class="title">
                                        عرض البطل
                                    </div>
                                </div>
                                <div class="left">

                                </div>
                            </div>
                            <div class="content">
                                <div id="hero-over-view">
                                    <div class="right">
                                        <div class="equip-review">
                                            ${ElkaisarCp.PlayerState.getHeroEquipRow(JsonData)}
                                        </div>
                                        <div class="row row-3">
                                            <div class="pull-L col-1"> الفيق</div>                       
                                            <div class="pull-L col-2">
                                                <div class="over-text">185186/186200</div>
                                                <div class="colored-bar filak" style="width: 99.45542427497314%"></div>
                                            </div>
                                            <div class="pull-L col-3">
                                            </div>
                                        </div>
                                        <div class="dicor"></div>

                                        <div class="army-review">
                                            ${ElkaisarCp.PlayerState.getHeroArmyRow(JsonData)}
                                        </div>
                                    </div>
                                    <div class="middel">

                                    </div>
                                    <div class="left">
                                        <div class="hero-data">
                                            <div class="name">
                                                <div class="wrapper change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="name">${JsonData.Hero.name}</div>
                                            </div>
                                            <div class="image">
                                                <div class="wrapper">
                                                    <div class="avatar-hero change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="avatar" style="background-image: url(../${ElkaisarCp.BaseData.HeroAvatar[JsonData.Hero.avatar]})">
                                                        <div class="lvl change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="lvl">${JsonData.Hero.lvl}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hero-points">
                                            <table border="1">
                                                <tbody><tr>
                                                        <td>قوة السيطرة</td>
                                                        <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_a">${JsonData.Hero.point_a}</span> + 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_a_plus">${JsonData.Hero.point_a_plus}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>الشجاعة</td>
                                                        <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_b">${JsonData.Hero.point_b}</span> + 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_b_plus">${JsonData.Hero.point_b_plus}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>الدفاع</td>
                                                         <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_c">${JsonData.Hero.point_c}</span> + 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="point_c_plus">${JsonData.Hero.point_c_plus}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <table border="1">
                                                <tbody><tr>
                                                        <td>قوة السيطرة</td>
                                                         <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="p_b_a">${JsonData.Hero.p_b_a}</span> 
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>الشجاعة</td>
                                                        <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="p_b_b">${JsonData.Hero.p_b_b}</span> 
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>الدفاع</td>
                                                        <td> 
                                                            <span style=" color: #008c10;"> 
                                                                <span class="change-hero-att" data-id-hero="${JsonData.Hero.id_hero}" data-att="p_b_c">${JsonData.Hero.p_b_c}</span> 
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>`;

            $("body").append(HeroRev);

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    })




}

var idHeroToView = 0;

$(document).on("click", ".view-hero-to-update", function () {
    idHeroToView = $(this).attr("data-id-hero");
    showPlayerHero();
});


$(document).on("click", ".remove-hero-equip", function () {
    var idEquip = $(this).attr("data-id-equip");
    alertBox.confirm(`تأكيد خلع المعدة من البطل `, function () {


        $.ajax({
            url: `${BASE_URL}/cp/APlayerState/getEquipOffHero`,
            type: 'POST',
            data: {
                idEquip: idEquip
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data)
                showPlayerHero();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

    });
});



$(document).on("click", ".update-hero-army", function (){
    
    
    var idHero = $(this).attr("data-id-hero");
    var ArmyPlace = $(this).attr("data-army-place");
    
    alertBox.confirm(`تأكيد تصفير جيش البطل<br>
                        <select id="ToDoToHeroArmy">
                            <option value="1">إنزال للمدينة</option>
                            <option value="2">حذف </option>
                        </select>`, function () {


        $.ajax({
            url: `${BASE_URL}/cp/APlayerState/changeHeroArmy`,
            type: 'POST',
            data: {
                idHero: idHero,
                ArmyPlace: ArmyPlace,
                ToDo: $("#ToDoToHeroArmy").val()
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                console.log(data)
                showPlayerHero();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

    });
});


$(document).on("click", ".change-hero-att", function (){
    
    
    var idHero = $(this).attr("data-id-hero");
    var Att    = $(this).attr("data-att");
    
    alertBox.confirm(`تأكيد تغير قيمة البطل(${Att})<br>
                        <input id="ValueToChange"></input>`, function () {


        $.ajax({
            url: `${BASE_URL}/cp/APlayerState/changeHeroAtt`,
            type: 'POST',
            data: {
                idHero: idHero,
                Att: Att,
                val: $("#ValueToChange").val()
            },
            beforeSend: function (xhr) {

            },
            success: function (data, textStatus, jqXHR) {
                showPlayerHero();
            },
            error: function (jqXHR, textStatus, errorThrown) {

            }
        });

    });
});


$(document).on("click", "#close-over-lay", function (){
    $("#over_lay").remove();
});





