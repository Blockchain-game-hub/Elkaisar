
Elkaisar.Helper.BackTrans = function (Transmit, CityFrom, CityTo){
        
        var distance = Elkaisar.Lib.LWorldUnit.calDist(CityFrom["x"], CityTo["x"], CityFrom["y"], CityTo["y"]);
        var time_arrive  = Math.floor(Date.now()/1000 + distance/300);
        Elkaisar.DB.Insert(`id_city_to = ${Transmit["id_city_from"]} , id_city_from  = ${Transmit["id_city_to"]}, time_arrive = ${time_arrive}`, "market_transport_back");
   
};


var TransPM = {};

setInterval(function () {

    var Now = Date.now()/1000;
    
    Elkaisar.DB.SelectFrom("*", "market_transport", "time_arrive <= ?", [Now], function (AllTransmit){
        Elkaisar.DB.Delete("market_transport_back", "time_arrive <= ?", [Now]);
        
        AllTransmit.forEach(function (TransMit, Index){
            
            if(TransPM[TransMit["id_trans"]])
                return ;
            
            TransPM[TransMit["id_trans"]] = true;
            
            Elkaisar.DB.SelectFrom("city.x, city.y , city.name , city.id_player, player.porm", "city JOIN player ON player.id_player = city.id_player", `city.id_city = ${TransMit["id_city_from"]}`, [], function (CityFrom){
                Elkaisar.DB.SelectFrom("city.x, city.y, city.id_player, player.porm", "city JOIN player ON player.id_player = city.id_player", `city.id_city = ${TransMit["id_city_to"]}`, [], function (CityTo){
                    Elkaisar.Helper.BackTrans(TransMit, CityFrom[0], CityTo[0]);
                    
                    
                    var Body  = `
                        <div class="req_table_msg">
                            <table class="req_table x-2" border="0">
                                <tbody>
                                    <tr>
                                        <td>
                                            <img src="images/style/food.png"> 
                                            <div class="amount sol-food">${TransMit["food"]}</div>
                                        </td>
                                        <td>
                                            <img src="images/style/stone.png">
                                            <div class="amount sol-stone">${TransMit["stone"]}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="images/style/wood.png">
                                            <div class="amount sol-wood">${TransMit["wood"]}</div>
                                        </td>
                                        <td>
                                            <img src="images/style/iron.png"> 
                                            <div class="amount sol-metal">${TransMit["metal"]}</div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <img src="images/style/coin.png">
                                            <div class="amount sol-coin">${TransMit["coin"]}</div>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="desc-in-msg">تم نقل الموارد من المدينة ${CityFrom[0]["name"]} الى المدينة [${CityFrom[0]["y"]} ,${CityFrom[0]["x"]}] وتم فقد خلال هذة العملية</div>
                    <div id="city-resource">
                        <ul>
                            <li><img src="images/style/food.png"><label> 0</label></li>
                            <li><img src="images/style/wood.png"><label> 0</label></li>
                            <li><img src="images/style/stone.png"><label>0</label></li>
                            <li><img src="images/style/iron.png"><label> 0</label></li>
                            <li><img src="images/style/coin.png"><label> 0</label></li>
                        </ul>.
                    </div>`;
                    Elkaisar.Lib.LSaveState.saveCityState();
                    var Quray = `food = food + ${TransMit["food"]}, wood = wood + ${TransMit["wood"]}, metal = metal + ${TransMit["metal"]}, coin = coin + ${TransMit["coin"]}, stone = stone + ${TransMit["stone"]}`;
            
                    Elkaisar.DB.Update(Quray, "city", `id_city = ${TransMit['id_city_to']}`, []);
                    Elkaisar.DB.Delete("market_transport", `id_trans = ${TransMit["id_trans"]}`, [], function (){
                        if(TransPM[TransMit["id_trans"]])
                            delete(TransPM[TransMit["id_trans"]]);
                    });

                    Elkaisar.DB.Insert(`id_to = ${CityFrom[0]["id_player"]} , head = 'تقرير وصول الموارد'  , body = '${Body}' , time_stamp = ${Now} `, "msg_diff", []);
                    Elkaisar.DB.Insert(`id_to = ${CityTo[0]["id_player"]}  , head = 'تقرير وصول الموارد'  , body = '${Body}' , time_stamp = ${Now} `, "msg_diff", []);
                    
                    var PlayerFrom = Elkaisar.Base.getPlayer(CityFrom[0]["id_player"]);
                    var PlayerTo   = Elkaisar.Base.getPlayer(CityTo[0]["id_player"]);
                    
                    if(PlayerFrom)
                        PlayerFrom.connection.sendUTF(JSON.stringify({
                            "classPath" : "Market.Trans.Arrived",
                            "idCity"    : CityFrom[0]["id_city"]
                        }));
                    
                    if(PlayerTo)
                        PlayerTo.connection.sendUTF(JSON.stringify({
                            "classPath" : "Market.Trans.Arrived",
                            "idCity"    : CityTo[0]["id_city"]
                        }));
                    
                    
                    
                });
            });
           
            
        });
        
    });

    /*Elkaisar.Base.Request.postReq(
            {
                MARKET_TRANSPORT: true,
                server: Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/ws/api/AMarket/marketTrans`,
            function (data) {
                
                var transports = Elkaisar.Base.isJson(data);
                if(!transports)
                    return console.log("marketTrans", data);
                
                var playerFrom;
                var player;
                var ii;
                var transport;
                
                for(ii in transports){
                    
                    transport  = transports[ii];
                    player = Elkaisar.Base.getPlayer(transport.idPlayer);
                    
                    if(player){
                        playerFrom.connection.sendUTF(JSON.stringify({
                            "classPath" : "Market.Trans.Arrived",
                            "idCity"    : transport.idCity
                        }));
                    }
                    
                    
                }
                
            }
    );*/

}, 1000);