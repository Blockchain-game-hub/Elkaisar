

var TransIdList = {};

setInterval(function () {


    var Now = Date.now() / 1000;


    Elkaisar.DB.SelectFrom("*", "market_buy_transmit", `time_arrive <= ${Now}`, [], function (AllTransmit) {
        AllTransmit.forEach(function (Transmit, Index) {
            if(TransIdList[Transmit["id_deal"]])
                return ;
            TransIdList[Transmit["id_deal"]] = true;
            Elkaisar.Lib.LSaveState.saveCityState(Transmit["id_city_to"]);
            Elkaisar.DB.Delete("market_buy_transmit", `id_deal = ${Transmit["id_deal"]}`, [], function (){
                if(TransIdList[Transmit["id_deal"]])
                    delete(TransIdList[Transmit["id_deal"]]);
            });
            Elkaisar.DB.Update(`${Transmit["resource"]} = ${Transmit["resource"]} + ${Transmit["amount"]}`, "city", `id_city = ${Transmit["id_city_to"]}`);
            
            var Player = Elkaisar.Base.getPlayer(Transmit["id_player_to"]);
            if (!Player)
                    return ;

            Player.connection.sendUTF(JSON.stringify({
                "classPath": "Market.Buy.TransmitDone",
                "idCity": Transmit["id_city_to"]
            }));
        });
    });

    /*Elkaisar.Base.Request.postReq(
     {
     MARKET_BUY_TRANSPORT: true,
     server: Elkaisar.CONST.SERVER_ID
     },
     `${Elkaisar.CONST.BASE_URL}/ws/api/AMarket/marketBuyTrans`,
     function (data) {
     
     var players = Elkaisar.Base.isJson(data);
     if(!players)
     return console.log("Market Buy: ", data);
     
     var player; 
     var ii; 
     for(ii in players){
     player = Elkaisar.Base.getPlayer(players[ii].id_player);
     if(!player)
     continue;
     
     player.connection.sendUTF(JSON.stringify({
     "classPath"   :  "Market.Buy.TransmitDone",
     "idCity"      :  players[ii]["idCity"]
     }));
     }
     
     }
     );*/

}, 1000);