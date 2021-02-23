
setInterval(function () {

    Elkaisar.Base.Request.postReq(
            {
                server: Elkaisar.CONST.SERVER_ID
            },
            `${Elkaisar.CONST.BASE_URL}/ws/api/ASpyFinish/Finish`,
            function (data) {
               
                var spyArr = Elkaisar.Base.isJson(data);
                if(!spyArr)
                    return console.log("SpyCheck", data);
                
                var spy;
                var ii;
                var player;
                var msg = JSON.stringify({
                    "classPath" : "Battel.Spy.Notif"
                });
                
                for(ii in spyArr.Players)
                {
                    player = Elkaisar.Base.getPlayer(spyArr.Players[ii]);
                    if(player)
                        player.connection.sendUTF(msg);
                }
                

            }
    );


}, 1000);
