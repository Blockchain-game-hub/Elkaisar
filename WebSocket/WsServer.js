const webSocketServer = require('websocket').server;

var wsServer = new webSocketServer({httpServer: Elkaisar.HttpServer});

wsServer.on('request', function (request) {

    var connection = request.accept(null, request.origin);
    connection.idPlayer = request.resourceURL.query.idPlayer;
    connection.idGameServer = request.resourceURL.query.server;
    connection.ComeFrom = request.origin;
    connection.ComeFromHeader = request.httpRequest.headers;
    connection.token = request.resourceURL.query.token;
    connection.ip = request.remoteAddress;





    connection.on('message', function (message) {


        if (message.type === 'utf8') {
            var msg = JSON.parse(message.utf8Data);

            if (!msg.url && !msg.uRL) {
                console.log(msg);
            } else if (!msg.uRL) {

            }
            var url = (msg.url || msg.uRL).split("/");

            try {
                Elkaisar.WsLib[url[0]][url[1]](connection, msg.data);
            } catch (e) {
                console.log(message);
                console.log(e);
            }
        }


    });

    connection.on('close', function (code) {
        if (connection.idPlayer && connection.idPlayer > 0)
            Elkaisar.WsLib.Player.offline(connection);
        if(Elkaisar.BusyPlayers[connection.idPlayer])
            delete Elkaisar.BusyPlayers[connection.idPlayer];
    });
});
console.log(wsServer)



