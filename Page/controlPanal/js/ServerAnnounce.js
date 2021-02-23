ElkaisarCp.ServerAnnounce = {};
ElkaisarCp.WsLib.ServerAnnounce = {};

ElkaisarCp.ServerAnnounce.BoxToSendMail = function () {
    var Box = ` <br />
                    <div class="select-bar">
                        <label>
                            <input id="message-title" type="text" placeholder="عنوان الرسالة" name="textfield"/>
                        </label>
                        <label>
                            <button id="send-msg" style="vertical-align: top">ارسال</button>
                        </label>
                    </div>
                    <div class="table"> <img src="img/bg-th-left.gif" width="8" height="7" alt="" class="left" /> <img src="img/bg-th-right.gif" width="7" height="7" alt="" class="right" />


                        <textarea id="message-body" placeholder="نص الرسالة"></textarea>
                    </div>`;

    $("#center-column").html(Box);
};

ElkaisarCp.ServerAnnounce.BoxToSendServerAnnounce = function () {
    var Box = ` <div class="select-bar">
                        <label>
                            <input id="ServerAnnounceMsg" type="text" placeholder="نص التنبيه" name="textfield"/>
                        </label>
                        <label>
                            <select id="ServerAnnounceRank">
                                <option value="0" style="font-size: 14px; font-weight: bold; color: #e30c0c">تنبية 1</option>
                                <option value="1" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 2</option>
                                <option value="2" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 3</option>
                                <option value="3" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 4</option>
                                <option value="4" style="font-size: 14px; font-weight: bold; color: #000080">تنبية 5</option>
                                <option value="5" style="font-size: 14px; font-weight: bold; color: #800080">تنبية 6</option>
                                <option value="6" style="font-size: 14px; font-weight: bold; color: #880000">تنبية 7</option>
                            </select>
                        </label>
                        <label>
                            <button id="SendServerAnnounceMsg" style="vertical-align: top">ارسال تنبيه</button>
                        </label>
                    </div>`;

    $("#center-column").html(Box);
};
ElkaisarCp.ServerAnnounce.BoxToSchadualServerAnnounce = function () {
    var Box = ` <div class="select-bar">
                        <label>
                            <input id="ServerAnnounceMsg" type="text" placeholder="نص التنبيه" name="textfield"/>
                        </label>
                        <label>
                            <select id="ServerAnnounceRank">
                                <option value="0" style="font-size: 14px; font-weight: bold; color: #e30c0c">تنبية 1</option>
                                <option value="1" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 2</option>
                                <option value="2" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 3</option>
                                <option value="3" style="font-size: 14px; font-weight: bold; color: #8B4513">تنبية 4</option>
                                <option value="4" style="font-size: 14px; font-weight: bold; color: #000080">تنبية 5</option>
                                <option value="5" style="font-size: 14px; font-weight: bold; color: #800080">تنبية 6</option>
                                <option value="6" style="font-size: 14px; font-weight: bold; color: #880000">تنبية 7</option>
                            </select>
                        </label>
                        <label>
                            <button id="SendServerAnnounceMsgEvery" style="vertical-align: top">ارسال تنبيه</button>
                        </label>
                    </div>
                    <table id="ServerAnnounceSchaduler" class="listing" cellspacing="0" cellpadding="0"></table>`;

    $("#center-column").html(Box);
};

ElkaisarCp.ServerAnnounce.BoxToSendMail();


$(document).on("click", "#showSendMail", function (){
    ElkaisarCp.ServerAnnounce.BoxToSendMail();
});
$(document).on("click", "#showSendAnnounce", function (){
    ElkaisarCp.ServerAnnounce.BoxToSendServerAnnounce();
});
$(document).on("click", "#showSendSchadAnn", function (){
    ElkaisarCp.ServerAnnounce.BoxToSchadualServerAnnounce();
    ElkaisarCp.ServerAnnounce.getServerAnnounceScaduler();
});


$(document).on("click", "#send-msg", function () {

    var msgBody = $("#message-body").val();
    var Title = $("#message-title").val();

    ws.send(JSON.stringify({
        url: "CSendMail/SendMailToServer",
        data: {
            MsgBody: msgBody,
            Title: Title
        }
    }));

});




$(document).on("click", "#SendServerAnnounceMsg", function () {

    var Announce = $("#ServerAnnounceMsg").val();
    var AnnounceRank = $("#ServerAnnounceRank").val();
    ws.send(JSON.stringify({
        url: "CSendMail/SendServerAnnounce",
        data: {
            Announce: Announce,
            AnnounceRank: AnnounceRank
        }
    }));
});



$(document).on("click", "#SendServerAnnounceMsgEvery", function () {

    var Announce = $("#ServerAnnounceMsg").val();
    var AnnounceRank = $("#ServerAnnounceRank").val();

    alertBox.confirmInputDialog("تاكيد اضافة  رسائل مجدولة بالدقيقة", function () {

        var Every = $("#alert-input").val();
        ws.send(JSON.stringify({
            url: "CSendMail/MakeSchadularAnnounce",
            data: {
                Announce: Announce,
                AnnounceRank: AnnounceRank,
                Every: Every
            }
        }));

    });


});




ElkaisarCp.WsLib.ServerAnnounce.newMailSent = function (data) {
    alertBox.confirm("تم الارسال");
};



ElkaisarCp.WsLib.ServerAnnounce.NewServerAnnounce = function (data) {
    alertBox.confirm("تم الارسال");

};


ElkaisarCp.WsLib.ServerAnnounce.showServerAnnounceScaduler = function (data) {

    var List = "";

    for (var iii in data.Schadule) {
        List += `<tr>
                                <td class="first"><button class="RemoveAnnounceFromSchaduler" data-index="${iii}">الغاء</button></td>
                                <td class="first">${data.Schadule[iii].Every}</td>
                                <td class="">
                                    <div style="font-weight: bold" class="server-announce-${data.Schadule[iii].AnnounceRank}">${data.Schadule[iii].Announce}</div>
                                </td>
                                <td>${(new Date(data.Schadule[iii].TimeStamp)).toLocaleDateString('en-US')}</td>
                            </tr>`;
    }

    var Table = `<table id="ServerAnnounceSchaduler" class="listing" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <th class="first" > الغاء</th>
                                <th>تكرار</th>
                                <th>النص</th>
                                <th class="last">تاريخ</th>
                            </tr>
                            ${List}
                        </tbody>
                    </table>`;
    $("#ServerAnnounceSchaduler").replaceWith(Table);
};

ElkaisarCp.ServerAnnounce.getServerAnnounceScaduler = function () {
    ws.send(JSON.stringify({
        url: "CSendMail/getSchaduler",
        data: {}
    }));
};



$(document).on("click", ".RemoveAnnounceFromSchaduler", function () {

    var Index = $(this).attr("data-index");
    ws.send(JSON.stringify({
        url: "CSendMail/removeFromSchaduler",
        data: {
            Index: Index
        }
    }));
});


