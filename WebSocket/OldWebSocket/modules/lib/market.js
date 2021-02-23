


exports.DealsDone = function (dealType, msgObj) {

    Elkaisar.Base.Request.postReq(
            {
                "DEALS_DONE": true,
                "buyers": JSON.stringify(msgObj.traders),
                "server": SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/ws_server/api/player.php`,
            function (data) {
                
                var playersArr = Elkaisar.Base.isJson(data);
                
                if(!playersArr)
                    return ;
                
                var ii;
                var player;
                
                for(ii in playersArr){
                    
                    player = Elkaisar.Base.getPlayer(playersArr[ii].id_player);
                    if(!player)
                        return ;
                    
                    player.connection.sendUTF(JSON.stringify({
                        "classPath"     : dealType,
                        "city_resource" : playersArr[ii].city_resource,
                        "id_city"       : playersArr[ii].id_city
                    }));
                    
                }
                
            }
    );

};


exports.buyerDealDone = function (con , msgObj){
    
    module.exports.DealsDone("Market.buyerDealDone", msgObj);
};

exports.sellerDealDone = function (con , msgObj){
    
    module.exports.DealsDone("Market.sellerDealDone", msgObj);
};

