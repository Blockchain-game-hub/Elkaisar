"use strict";

var webSocketServer = require('websocket').server;
var Http            = require('http');
var MySql           = require('mysql');
Elkaisar.Event      = require('events');
Elkaisar.Cron       = require('node-cron');


const startTime = Date.now();

Elkaisar.Mysql  = MySql.createPool({
    connectionLimit    : 100,
    host               : "localhost",
    user               : Elkaisar.CONST.DBUserName,
    password           : Elkaisar.CONST.DBPassWord,
    database           : Elkaisar.CONST.DBName,
    charset            : 'utf8mb4',
    multipleStatements : true
});


Elkaisar.MysqlBattelReplay  = MySql.createPool({
    connectionLimit    : 100,
    host               : "localhost",
    user               : Elkaisar.CONST.DBUserName,
    password           : Elkaisar.CONST.DBPassWord,
    database           : "elkaisar_battel_replay",
    charset            : 'utf8mb4',
    multipleStatements : true
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

Elkaisar.World = {};
Elkaisar.Battel = {
    BattelList: {}
};

Elkaisar.Equip = {
    EquipPower: {}
};

Elkaisar.OnEvent = new Elkaisar.Event();


Elkaisar.WsLib = {};
Elkaisar.Lib   = {};
Elkaisar.CP    = {};

Elkaisar.Base                     = require('./modules/lib/base');
Elkaisar.data                     = require('./modules/util/world/unitData');

Elkaisar.Lib.LConfig              = require('./Lib/LConfig');
Elkaisar.Lib.LBase                = require('./Lib/LBase');
Elkaisar.Lib.LBattel              = require('./Lib/LBattel');
Elkaisar.Lib.LFight               = require('./Lib/LFight');
Elkaisar.Lib.LFightRound          = require('./Lib/LFightRound');
Elkaisar.Lib.LFightRecord         = require('./Lib/LFightRecord');
Elkaisar.Lib.LHero                = require('./Lib/LHero');
Elkaisar.Lib.LPlayer              = require('./Lib/LPlayer');
Elkaisar.Lib.LWorld               = require('./Lib/LWorld');
Elkaisar.Lib.LWorldUnit           = require('./Lib/LWorldUnit');
Elkaisar.Lib.LArmy                = require('./Lib/LArmy');
Elkaisar.Lib.LSaveState           = require('./Lib/LSaveState');
Elkaisar.Lib.LPrize               = require('./Lib/LPrize');
Elkaisar.Lib.LAfterFight          = require('./Lib/LAfterFight');
Elkaisar.Lib.LBattelReport        = require('./Lib/LBattelReport');
Elkaisar.Lib.LCity                = require('./Lib/LCity');
Elkaisar.Lib.LSchadular           = require('./Lib/LSchadular');
Elkaisar.Lib.LItem                = require('./Lib/LItem');

Elkaisar.Config.CHero             = require('./Config/CHero');
Elkaisar.Config.CArmy             = require('./Config/CArmy');
Elkaisar.Config.CPlayer           = require('./Config/CPlayer');


Elkaisar.WsLib.Player             = require('./modules/lib/Player');
Elkaisar.WsLib.Base               = require('./modules/lib/BaseLib');
Elkaisar.WsLib.Battel             = require('./modules/lib/Battel');
Elkaisar.WsLib.BattelWatchList    = require('./modules/lib/battelWatchList');
Elkaisar.WsLib.Chat               = require('./modules/lib/chat');
Elkaisar.WsLib.Guild              = require('./modules/lib/Guild');
Elkaisar.WsLib.World              = require('./modules/lib/World');
Elkaisar.WsLib.Ban                = require('./modules/lib/ban');

Elkaisar.WsLib.ServerAnnounce     = require('./modules/lib/serverAnnounce');



Elkaisar.WsLib.CSendMail          = require('./CPanal/CSendMail');

/*Elkaisar.WsLib.WS_Guild           = require('./modules/lib/guild');
Elkaisar.WsLib.WS_GuildReq        = require('./modules/lib/guildReq');
Elkaisar.WsLib.WS_Mail            = require('./modules/lib/mail');*/




/* inf Loops */
require('./modules/loops/TaskBuilding');
require('./modules/loops/TaskArmy');
require('./modules/loops/TaskJop');
require('./modules/loops/TaskStudy');
require('./modules/loops/BattelEnd');
require('./modules/loops/HeroBack');
require('./modules/loops/HeroPower');
require('./modules/loops/CityPop');
require('./modules/loops/CityLoy');
require('./modules/loops/MarketTransport');
require('./modules/loops/marketBuyTransport');
require('./modules/loops/SpyEnd');

require('./Support/SWorldCity');
require('./Support/SBackUpDB');
require('./Lib/LServerSchadular');



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
            
            Elkaisar.Base.getAllWorldCity();

            if (parseInt(serverData.open_status) === 0)
                process.exit(0);

            if (parseInt(serverData.under_main) === 1)
                process.exit(0);

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






var server = Http.createServer(function (request, response) {
    console.log((new Date()) + ' Received request for ' + request.url);
    response.writeHead(404);
    response.end();
});

server.listen(Elkaisar.CONST.ServerPort, function () { });

var wsServer = new webSocketServer({httpServer: server});


wsServer.on('request', function (request) {
    
    var connection            = request.accept(null, request.origin);
    connection.idPlayer       = request.resourceURL.query.idPlayer;
    connection.idGameServer   = request.resourceURL.query.server;
    connection.ComeFrom       = request.origin;
    connection.ComeFromHeader = request.httpRequest.headers;
    connection.token          = request.resourceURL.query.token;
    connection.ip             = request.remoteAddress;
    


   
    
    connection.on('message', function (message) {
       
        
        if (message.type === 'utf8') {
            var msg = JSON.parse(message.utf8Data);
           
            if (!msg.url && !msg.uRL) {
                console.log(msg);
            }else if(!msg.uRL){
                
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
        if(connection.idPlayer && connection.idPlayer > 0)
            Elkaisar.WsLib.Player.offline(connection);
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
