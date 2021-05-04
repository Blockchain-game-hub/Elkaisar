
Elkaisar.BattelReplay.BattelReport = {};


Elkaisar.Items = {};
Elkaisar.BattelReplay.BattelReport.BATTEL_SIDE_ATT = 1;
Elkaisar.BattelReplay.BattelReport.BATTEL_SIDE_DEF = 0;
Elkaisar.HeroAvatar = [
    "images/hero/faceA1.jpg",
    "images/hero/faceA2.jpg",
    "images/hero/faceA3.jpg",
    "images/hero/faceA4.jpg",
    "images/hero/faceA5.jpg",
    "images/hero/faceA6.jpg",
    "images/hero/faceA7.jpg",
    "images/hero/faceA8.jpg",
    "images/hero/faceA9.jpg",
    "images/hero/faceA10.jpg",
    "images/hero/faceB1.jpg",
    "images/hero/faceB2.jpg",
    "images/hero/faceB3.jpg",
    "images/hero/faceB4.jpg",
    "images/hero/faceB5.jpg",
    "images/hero/faceB6.jpg",
    "images/hero/faceB7.jpg",
    "images/hero/faceB8.jpg",
    "images/hero/faceB9.jpg",
    "images/hero/faceB9.jpg",
    "images/hero/faceB9.jpg"
];
Elkaisar.HeroAvatarBR = [
    "HeroFaceA1",
    "HeroFaceA2",
    "HeroFaceA3",
    "HeroFaceA4",
    "HeroFaceA5",
    "HeroFaceA6",
    "HeroFaceA7",
    "HeroFaceA8",
    "HeroFaceA9",
    "HeroFaceA10",
    "HeroFaceB1",
    "HeroFaceB2",
    "HeroFaceB3",
    "HeroFaceB4",
    "HeroFaceB5",
    "HeroFaceB6",
    "HeroFaceB7",
    "HeroFaceB8",
    "HeroFaceB9",
    "HeroFaceB9"
];

Elkaisar.ArmyAvatar = {
    "0": "",
    "1": 'images/tech/soldier_1.jpg"',
    "2": 'images/tech/soldier_2.jpg"',
    "3": 'images/tech/soldier_3.jpg"',
    "4": 'images/tech/soldier_4.jpg"',
    "5": 'images/tech/soldier_5.jpg"',
    "6": 'images/tech/soldier_6.jpg"',
    "10": 'images/tech/defense01.jpg"',
    "11": 'images/tech/defense02.jpg"',
    "12": 'images/tech/defense03.jpg"'
};

Elkaisar.BattelReplay.BattelReport.getItemData = function () {

    $.ajax({
        url: `../js${JSVersion}/json/ItemLang/ar.json`,
        success: function (data, textStatus, jqXHR) {
            Elkaisar.Items = data;
        },
        error: function (jqXHR, textStatus, errorThrown) {
        }
    })

};
Elkaisar.BattelReplay.BattelReport.getItemData();

Elkaisar.BattelReplay.BattelReport.PrizeList = function () {

    var List = "";
    var ItemPrize = {};
    for (var idPlayer in BattelReplayData.Players) {
        for (var iii in BattelReplayData.Players[idPlayer].ItemPrize) {
            if (!ItemPrize[BattelReplayData.Players[idPlayer].ItemPrize[iii].Item])
                ItemPrize[BattelReplayData.Players[idPlayer].ItemPrize[iii].Item] = 0;

            ItemPrize[BattelReplayData.Players[idPlayer].ItemPrize[iii].Item] += BattelReplayData.Players[idPlayer].ItemPrize[iii].amount;
        }
    }

    var Count = 0;
    for (var Item in ItemPrize) {
        if (Count > 13)
            return;

        List += `<li>
                    <img src="../${Elkaisar.Items[Item].image}">
                    <div class="amount stroke">${ItemPrize[Item]}</div>
                </li> `;
        Count++;
    }
    return List;
};

Elkaisar.BattelReplay.BattelReport.getHeroName = function (idPlayer) {
    for (var idp in BattelReplayData.Players) {
        if (idp == idPlayer)
            return BattelReplayData.Players[idp].Player.PlayerName;
    }
    return  "النظام";
};



Elkaisar.BattelReplay.BattelReport.Hero = function (Hero) {

    var ArmyList = "";
    for (var ii in Hero.pre) {
        if (Hero.pre[ii] > 0) {
            ArmyList += `<li>
                            <img src="../${Elkaisar.ArmyAvatar[Hero.type[ii]]}">
                            <div class="pre-amount stroke">${Hero.pre[ii]}</div>
                            <div class="post-amount">${Hero.post[ii]}</div>
                        </li>`;
        }
    }

    console.log(Hero)
    var HH = `
                <li>
                    <div class="hero">
                        <div class="name ellipsis">
                            ${Hero.Hero.HeroName}(${Elkaisar.BattelReplay.BattelReport.getHeroName(Hero.Hero.idPlayer)})
                        </div>
                        <div class="image">
                            <img src="../${Elkaisar.HeroAvatar [(Hero.Hero.avatar || 0) % 20]}">
                            <div class="xp stroke">+${Hero.gainXp || 0}</div>
                        </div>
                    </div>
                    <div class="army">
                        <ol>${ArmyList}</ol>
                    </div>
                </li>`;

    return HH;
};


Elkaisar.BattelReplay.BattelReport.getHeroList = function (Side) {


    var HeroList = "";
    for (var iii in BattelReplayData.Heros) {
        if (BattelReplayData.Heros[iii].Hero.side == Side) {
            HeroList += Elkaisar.BattelReplay.BattelReport.Hero(BattelReplayData.Heros[iii]);
        }
    }
    return HeroList;

};

Elkaisar.BattelReplay.BattelReport.Box = function () {
    return `
        <div id="dialg_box" style="top: 125px;">
            <div class="head_bar">
                <img src="../images/style/head_bar.png">
                <div class="title">تقرير الهجوم</div>
            </div>
            <div class="nav_bar">
                <div class="left-nav">
                </div>
                <div class="right-nav">
                    <div class="nav_icon">
                        <img class="close_dialog" src="../images/btns/close_b.png">
                    </div>
                </div>
               
            </div> 
            <div class="box_content for_msg for_Br " id="battel-report-msg">
                <div class="left-content full">
                    <div id="battel_r_upper">
                        <div class="header">
                            <div class="pull-R th" style="direction: rtl;">
                                انشئ الملك ${BattelReplayData.Battel.PlayerData} غزو الى  [ ${BattelReplayData.Battel.x_coord} , ${BattelReplayData.Battel.y_coord}  ] 
                            </div>
                            <div class="pull-L th">${(new Date(BattelReplayData.Battel.time_end * 1000)).toDateString()}</div>
                        </div>
                        <p style="clear: both"></p>
                        <div class="result-icon">
                        </div>
                        <div class="battel-desc">

                        </div>
                        <div class="resource-row">
                            <ul>
                                <li><img ondragstart="return false;" src="../images/style/food.png"><span> 0</span></li>
                                <li><img ondragstart="return false;" src="../images/style/wood.png"><span> 0</span></li>
                                <li><img ondragstart="return false;" src="../images/style/stone.png"><span>0</span></li>
                                <li><img ondragstart="return false;" src="../images/style/iron.png"><span> 0</span></li>
                                <li><img ondragstart="return false;" src="../images/style/coin.png"><span> 0</span></li>
                            </ul>
                        </div><div class="prize-row">
                            <ul>
                                ${Elkaisar.BattelReplay.BattelReport.PrizeList()}
                            </ul>
                            <p> انتهت المعركة فى 0 جولة وكانت نتيجة الهجوم بالنصر ! والحصول على 0 نقطة شرف </p>
                        </div>
                    </div>
                    <div id="battel-detail" style="overflow: hidden; outline: currentcolor none medium;" tabindex="1">
                        <div class="your_side">
                            <ul>
                                ${Elkaisar.BattelReplay.BattelReport.getHeroList(Elkaisar.BattelReplay.BattelReport.BATTEL_SIDE_ATT)}
                            </ul>
                        </div>
                        <div class="enemy_side">
                            <ul>
                                ${Elkaisar.BattelReplay.BattelReport.getHeroList(Elkaisar.BattelReplay.BattelReport.BATTEL_SIDE_DEF)}                         
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;
};


$(document).on("click", "#ShowBattelReport", function () {
    $("body").append(Elkaisar.BattelReplay.BattelReport.Box());
    $("#battel-detail").niceScroll();
});


$(document).on("click", ".close_dialog", function () {
    $("#dialg_box").remove();
});