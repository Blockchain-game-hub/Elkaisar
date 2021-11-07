
var UserLag = {};
UserLag.language = "ar";
Translate = {};

ElkaisarCp = {};

var PLAYER_GROUP = {

    0: "لاعب",
    1: "لاعب",
    2: "لاعب",
    3: "مراقب",
    4: "مشرف",
    5: "مسؤل كادر",
    6: "ادارة مطلقة"

};

ElkaisarCp.getItem = function ()
{
    return  $.ajax({
        url: `${BASE_URL}/js/json/ItemLang/ar.json`,
        success: function (data, textStatus, jqXHR) {
            ElkaisarCp.Items = data;
        }
    });
};


ElkaisarCp.getEquip = function ()
{
    return  $.ajax({
        url: `${BASE_URL}/js/json/equipment/ar.json`,
        success: function (data, textStatus, jqXHR) {
            ElkaisarCp.Equips = data;
        }
    });
};

ElkaisarCp.getItem();
ElkaisarCp.getEquip();
ElkaisarCp.WsLib = {};

ElkaisarCp.BaseData = {};
ElkaisarCp.BaseData.HeroAvatar = [
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
    "images/hero/faceB9.jpg"
];


ElkaisarCp.BaseData.ArmyImage = [
    "no_army.png",
    "soldier01.jpg",
    "soldier02.jpg",
    "soldier03.jpg",
    "soldier04.jpg",
    "soldier05.jpg",
    "soldier06.jpg"
];

var pormotion = [
    {
        ar_title: "مواطن"
    },
    {
        ar_title: "عريف"
    },
    {
        ar_title: "رقيب"
    },
    {
        ar_title: "قسطور"
    },
    {
        ar_title: "قسطور اعلى"
    },
    {
        ar_title: "نائب"
    },
    {
        ar_title: "قاضى"
    },
    {
        ar_title: "موفد"
    },
    {
        ar_title: "ديكتاتور"
    },
    {
        ar_title: "قائد الفيلق الخامس"
    },
    {
        ar_title: "اقئد الفيلق الرابع"
    },
    {
        ar_title: "قائد الفيلق الثالث"
    },
    {
        ar_title: "قائد الفيلق الثانى"
    },
    {
        ar_title: "قائد الفيلق الاول"
    },
    {
        ar_title: "لواء"
    },
    {
        ar_title: "فريق"
    },
    {
        ar_title: "فريق درجة 1"
    },
    {
        ar_title: "فريق درجة 2"
    },
    {
        ar_title: "فريق درجة 3"
    },
    {
        ar_title: "مارشال"
    },
    {
        ar_title: "رقيب درجة 9"
    },
    {
        ar_title: "رقيب درجة 8"
    },
    {
        ar_title: "رقيب درجة 7"
    },
    {
        ar_title: "رقيب درجة 6"
    },
    {
        ar_title: "رقيب درجة 5"
    },
    {
        ar_title: "رقيب درجة 4"
    },
    {
        ar_title: "رقيب درجة 3"
    },
    {
        ar_title: "رقيب درجة 2"
    },
    {
        ar_title: "رقيب درجة 1"
    },
    {
        ar_title: "قيصر"
    }
];


ElkaisarCp.BaseData.getPormSelectList = function (SelectedIdex = 0){
    var List = "";
    for(var  iii in pormotion){
        List += `<option value="${iii}" ${SelectedIdex == iii ? `selected="selected"` : ""}>${pormotion[iii].ar_title}</option>`
    }
    return List;
}



$.ajaxSetup({
    data: {
        server: SERVER_ID
    }
});



function isJson(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
}


alertBox = {

    confirmDialog: function (msg, yesCallBack, obj) {


        var contet = `<div id="over_lay_alert">  
                        <div id="alert_container">     
                            <div id="alert_head">       
                                <div>               
                                    <img src="../images/panner/king_name.png">   
                                </div>     
                                <div id="alert-title" class="ellipsis">تأكيد</div>  
                                <img src="../images/btns/close_b.png" class="img-sml close-alert">     
                            </div> 
                            <div id="alert_box" class="for_battel">        
                                <div class="row-2">
                                    <div class="msg">${msg}</div>
                                </div>    
                                <div class="row-3">        
                                    <div class="confim-btn">            
                                        <button class="full-btn full-btn-3x pull-R enter" id="btn-confirm-yes">تأكيد</button>    
                                        <button class="full-btn full-btn-3x pull-L" id="btn-confirm-no">الغاء</button>  
                                    </div>    
                                </div>
                            </div>   
                        </div>
                    </div>`;
        $("body").append(contet);

        $("#btn-confirm-yes").click(function () {
            yesCallBack();
            $("#over_lay_alert").remove();

        });

        $("#btn-confirm-no , .close-alert ").click(function () {
            $("#over_lay_alert").remove();
        });


    },
    confirmInputDialog: function (msg, yesCallBack, obj) {


        var contet = `<div id="over_lay_alert">  
                        <div id="alert_container">     
                            <div id="alert_head">       
                                <div>               
                                    <img src="../images/panner/king_name.png">   
                                </div>     
                                <div id="alert-title" class="ellipsis">تاكيد</div>  
                                <img src="../images/btns/close_b.png" class="img-sml close-alert">     
                            </div> 
                            <div id="alert_box" class="for_battel">        
                                <div class="row-2">
                                    <div class="msg">${msg}</div>
                                    <div class="input">
                                        <input type="text" id="alert-input"/>
                                    </div>
                                </div>    
                                <div class="row-3">        
                                    <div class="confim-btn">            
                                        <button class="full-btn full-btn-3x pull-R enter" id="btn-confirm-yes">تاكيد</button>    
                                        <button class="full-btn full-btn-3x pull-L" id="btn-confirm-no">الغاء</button>  
                                    </div>    
                                </div>
                            </div>   
                        </div>
                    </div>`;
        $("body").append(contet);

        $("#btn-confirm-yes").click(function () {
            yesCallBack();
            $("#over_lay_alert").remove();

        });

        $("#btn-confirm-no , .close-alert ").click(function () {
            $("#over_lay_alert").remove();
        });


    },

    close: function () {
        $('.close-alert').trigger('click');
    },
    confirm: function (msg, yesCallBack) {


        var contet = `<div id="over_lay_alert">  
                        <div id="alert_container">     
                            <div id="alert_head">       
                                <div>               
                                    <img src="../images/panner/king_name.png">   
                                </div>     
                                <div id="alert-title">تأكيد</div>  
                                <img src="../images/btns/close_b.png" class="img-sml close-alert">     
                            </div> 
                            <div id="alert_box" class="for_battel">        
                                <div class="row-2">
                                    <div class="msg">${msg}</div>
                                </div>    
                                <div class="row-3">        
                                    <div class="confim-btn">            
                                        <button class="full-btn full-btn-3x pull-R enter" id="btn-confirm-yes">تأكيد</button>    
                                        <button class="full-btn full-btn-3x pull-L" id="btn-confirm-no">الغاء</button>  
                                    </div>    
                                </div>
                            </div>   
                        </div>
                    </div>`;
        $("body").append(contet);

        $("#btn-confirm-yes").click(function () {


            if (typeof yesCallBack === "function") {

                yesCallBack();



            }

            $("#over_lay_alert").remove();

        });




    },
    close: function () {
        $('.close-alert').trigger('click');
    }

};

$(document).on("click", ".close-alert", function (){
    $("#over_lay_alert").remove();
});

ElkaisarCp.Ws = {};


ElkaisarCp.Ws.onopen = function (){
    if(ElkaisarCp.ServerAnnounce){
        ElkaisarCp.ServerAnnounce.getServerAnnounceScaduler();
    }
    
};

ElkaisarCp.Ws.onmessage =  function (e){
    
    console.log(e.data);
    
    if(isJson(e.data)){
      var data = JSON.parse(e.data);  
    }else{
        
        alert(e.data);
        console.log(e);
    }
    
    var classPath = data.classPath.split(".");
   
    if(!$.isArray(classPath)){
        alert("برجاء تصوير الرسالة وارسالها الى فريق الدعم" + e.data);
    }
    
    if(classPath.length === 2)
        ElkaisarCp.WsLib[classPath[0]][classPath[1]](data);
    else if(classPath.length === 3)
        ElkaisarCp.WsLib[classPath[0]][classPath[1]][classPath[2]](data);
    
};

ElkaisarCp.Ws.onclose = function (event) {};

var ws;
ElkaisarCp.Ws.connect = function (){
   
    ws =  new WebSocket(`ws://${WS_HOST}:${WS_PORT}?server=${SERVER_ID}&token=${OuthToken}`);
    ws.onopen    = ElkaisarCp.Ws.onopen;
    ws.onmessage = ElkaisarCp.Ws.onmessage;
    ws.onclose   = ElkaisarCp.Ws.onclose;
    console.log("connected");
};

ElkaisarCp.Ws.connect();



$(document).on("click", ".examinPlayer", function (){
    var idPlayer = $(this).attr("data-id-player");
    var win = window.open(`http://web.elkaisar.com/ExaminPlayer.php?AdminToken=${OuthToken}&server=${SERVER_ID}&idPlayer=${idPlayer}`, '_blank');
    win.focus();
});