
ElkaisarCp.getEquip();
ElkaisarCp.getItem();

$.ajax({
    url: `${BASE_URL}/cp/ASendPrizeHis/getPageCount`,
    date: {
        server: SERVER_ID
    },
    success: function (data, textStatus, jqXHR) {
        var List = "";
        if (!isJson(data))
            alert(data);
        var JsonObject = JSON.parse(data);

        for (var iii = 0; iii < JsonObject.Count; iii++) {
            $("#SendPrizeHisLis").append(`<a href="#" data-offset="${iii}">${iii} &nbsp;</a>`);
        }

    }
});

$(document).on("click", "#SendPrizeHisLis a", function () {


    var Offest = $(this).attr("data-offset");
    $.ajax({
        url: `${BASE_URL}/cp/ASendPrizeHis/getPage`,
        data: {
            offset: Offest
        },
        success: function (data, textStatus, jqXHR) {


            if (!isJson(data))
                return alert(data);
            var JsonObject = JSON.parse(data);

            var List = "";
            var TablList = "";

            for (var iii in JsonObject) {
                List = "";
                var PrizeList = JSON.parse(JsonObject[iii].prize);

                for (var jjj in PrizeList) {

                    if (PrizeList[jjj].type === "Item") {
                        List += `<div class="matrial-unit" style="width: 10%; height:35px;">
                                        <img src="../${ElkaisarCp.Items[PrizeList[jjj].Item].image}">
                                        <div class="amount" style="margin-top: -10px; font-size:10px">${PrizeList[jjj].old} - ${PrizeList[jjj].new}</div>
                                    </div>`;
                    } else if (PrizeList[jjj].type === "Equip") {
                        List += `<div class="matrial-unit" style="width: 10%; height:35px;">
                                        <img src="../${ElkaisarCp.Equips[PrizeList[jjj].idEquip].image}">
                                        <div class="amount" style="margin-top: -10px; font-size:10px">${PrizeList[jjj].old} - ${PrizeList[jjj].new}</div>
                                    </div>`;
                    }

                }
                
                TablList += `<tr data-id-user="21">
                                <td class="first">${JsonObject[iii].name}</td>
                                <td>
                                    ${List}
                                </td>
                                <td>${JsonObject[iii].time_stamp}</td>
                            </tr>`;

            }

            $("#ShowSendPrizeHis").html(`<tbody>
                            <tr>
                                <th class="first" width="50"> اللاعب</th>
                                <th>جوئز</th>
                                <th class="last">تاريخ</th>
                            </tr>
                            ${TablList}
                        </tbody>`);
        }
    });
});