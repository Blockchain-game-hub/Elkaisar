"use strict";

var webSocketServer = require('websocket').server;
var Http = require('http');
const QueryString = require('querystring');
Elkaisar.URL = require('url');
var MySql = require('mysql');
Elkaisar.ZLib = require('zlib');
Elkaisar.Event = require('events');
Elkaisar.Cron = require('node-cron');





const startTime = Date.now();

Elkaisar.Mysql = MySql.createPool({
    connectionLimit: 100,
    host: "localhost",
    user: Elkaisar.CONST.DBUserName,
    password: Elkaisar.CONST.DBPassWord,
    database: Elkaisar.CONST.DBName,
    charset: 'utf8mb4',
    multipleStatements: true
});


Elkaisar.MysqlBattelReplay = MySql.createPool({
    connectionLimit: 100,
    host: "localhost",
    user: Elkaisar.CONST.DBUserName,
    password: Elkaisar.CONST.DBPassWord,
    database: "elkaisar_battel_replay",
    charset: 'utf8mb4',
    multipleStatements: true
});



Elkaisar.Arr = {};
Elkaisar.data = {};
Elkaisar.Arr.Players = {};
Elkaisar.Arr.BattelWatchList = {};
Elkaisar.DB = {};
Elkaisar.Config = {};
Elkaisar.AllWorldCity = [];
Elkaisar.AllWorldCityColonized = [];
Elkaisar.Helper = {};
Elkaisar.API = {};

Elkaisar.World = {};
Elkaisar.Battel = {
    BattelList: {}
};

Elkaisar.Equip = {
    EquipPower: {}
};

Elkaisar.OnEvent = new Elkaisar.Event();


Elkaisar.WsLib = {};
Elkaisar.Lib = {};
Elkaisar.CP = {};

Elkaisar.Base = require('./modules/lib/base');
Elkaisar.data = require('./modules/util/world/unitData');

require("./ImportLib")

Elkaisar.Config.CHero = require('./Config/CHero');
Elkaisar.Config.CArmy = require('./Config/CArmy');
Elkaisar.Config.CPlayer = require('./Config/CPlayer');

require("./ImportWsLib");
require("./ImportApiLib");
require("./ImportApi");
require("./ImportCp");



/*Elkaisar.WsLib.WS_Guild           = require('./modules/lib/guild');
 Elkaisar.WsLib.WS_GuildReq        = require('./modules/lib/guildReq');
 Elkaisar.WsLib.WS_Mail            = require('./modules/lib/mail');*/




/* inf Loops */
require("./ImportLoop");



Elkaisar.Base.Request.postReq(
        {
            server: Elkaisar.CONST.SERVER_ID
        },
        `${Elkaisar.CONST.BASE_URL}/ws/api/AServer/getServerData`,
        function (data) {

            var serverData = Elkaisar.Base.isJson(data);
            if (!serverData)
                return console.log(data);

            Elkaisar.Base.ServerData = serverData;

            if (parseInt(serverData.open_status) === 0) {
                console.log("Server is Closed So You cant start");
                process.exit(0);
            }


            if (parseInt(serverData.under_main) === 1) {
                console.log("Server is Under maintain So You cant start")
                process.exit(0);
            }


        }
);

Elkaisar.Base.Request.postReq(
        {
            SERVER_JUST_OPPENED: true,
            server: Elkaisar.CONST.SERVER_ID
        },
        `${Elkaisar.CONST.BASE_URL}/ws/api/AServer/serverJustOppend`,
        function (data) {
            //console.log(data);
        }
);



var BusyPlayers = {};
setInterval(function () {
    BusyPlayers = {};
}, 60 * 1000);

Elkaisar.Base.HandleReq = async function(Path, Parm){
    
    var Res  = ""; 
    if (BusyPlayers[Parm.idPlayer]) {
        Res = JSON.stringify({state: "SysBusy"});
    } else {
        BusyPlayers[Parm.idPlayer] = true;
        
        if (Path[1] == "api") {
            
            const Player = await Elkaisar.DB.ASelectFrom("id_player", "player_auth", "auth_token = ?", [Parm.token]);
            if(!Player.length)
                return console.log(Path, Parm);
            Res = JSON.stringify(await (new Elkaisar.API[Path[2]](Player[0].id_player, Parm))[Path[3]]());

        } else if(Path[1] == "cp") {
            Res = JSON.stringify(await (new Elkaisar.CP[Path[2]](Parm))[Path[3]]());
        }


        BusyPlayers[Parm.idPlayer] = false;
    }
    
    return Res;
};


var server = Http.createServer(async function (request, response) {

    response.setHeader('Access-Control-Allow-Origin', '*');
    response.setHeader("Content-Type", "text/plain");
    var data = "";
    
    const Url = Elkaisar.URL.parse(request.url, true);
    const Path = Url.pathname.split("/");
    const Method = request.method;
    
    if(Method == "POST"){
        request.on('data', chunk => {
            data += chunk;
        });
        request.on('end', async () => {
            const PostPar = QueryString.parse(data);
            response.end(await Elkaisar.Base.HandleReq(Path, PostPar));
        });
        
        return ;
    }
    
    
    const Parm = Url.query;
    response.end(await Elkaisar.Base.HandleReq(Path, Parm));
   
   


});

server.listen(Elkaisar.CONST.ServerPort, function () { });




var wsServer = new webSocketServer({httpServer: server});


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
        delete BusyPlayers[connection.idPlayer];
    });
});










process.on('uncaughtException', (err, origin) => {
    console.error("uncaughtException");
    console.log(err);
    console.log(origin);
});


process.on('beforeExit', (code) => {
    console.log('Process beforeExit event with code: ', code, `after ${startTime - Date.now()}`);

});
