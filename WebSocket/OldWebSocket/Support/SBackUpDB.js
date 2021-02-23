var spawn = require('child_process').exec;


Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 7 * * *`, function (){
    var Options  = `--password=${Elkaisar.CONST.DBPassWord}  --user=${Elkaisar.CONST.DBUserName}  --no-create-info  ${Elkaisar.CONST.DBName}`;
    var FileName = `/home/elkaisar/WebSocket/backup/City-Server${Elkaisar.CONST.SERVER_ID}-${(new Date()).getMonth()}-${(new Date()).getDate()}-${(new Date()).getHours()}.sql`;
    var Table    = `city city_building city_building_lvl city_jop city_wounded edu_acad edu_uni equip`;
    spawn(`mysqldump ${Options} ${Table} > ${FileName}`);
},{
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 6 * * *`, function (){
    var Options  = `--password=${Elkaisar.CONST.DBPassWord}  --user=${Elkaisar.CONST.DBUserName}  --no-create-info  ${Elkaisar.CONST.DBName}`;
    var FileName = `/home/elkaisar/WebSocket/backup/Player-Server${Elkaisar.CONST.SERVER_ID}-${(new Date()).getMonth()}-${(new Date()).getDate()}-${(new Date()).getHours()}.sql`;
    var Table    = `player god_gate god_gate_1 god_gate_2 god_gate_3 god_gate_4 guild  guild_inv  guild_member guild_relation`;
    spawn(`mysqldump ${Options} ${Table} > ${FileName}`);
},{
    scheduled: true,
    timezone: "Etc/UTC"
});



Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 8 * * *`, function (){
    var Options  = `--password=${Elkaisar.CONST.DBPassWord}  --user=${Elkaisar.CONST.DBUserName}  --no-create-info  ${Elkaisar.CONST.DBName}`;
    var FileName = `/home/elkaisar/WebSocket/backup/Hero-Server${Elkaisar.CONST.SERVER_ID}-${(new Date()).getMonth()}-${(new Date()).getDate()}-${(new Date()).getHours()}.sql`;
    var Table    = `hero hero_army`;
    spawn(`mysqldump ${Options} ${Table} > ${FileName}`);
},{
    scheduled: true,
    timezone: "Etc/UTC"
});


Elkaisar.Cron.schedule(`${Math.floor(Math.random() * 59)} 8 * * *`, function (){
    var Options  = `--password=${Elkaisar.CONST.DBPassWord}  --user=${Elkaisar.CONST.DBUserName}  --no-create-info  ${Elkaisar.CONST.DBName}`;
    var FileName = `/home/elkaisar/WebSocket/backup/Item-Server${Elkaisar.CONST.SERVER_ID}-${(new Date()).getMonth()}-${(new Date()).getDate()}-${(new Date()).getHours()}.sql`;
    var Table    = `player_item`;
    spawn(`mysqldump ${Options} ${Table} > ${FileName}`);
},{
    scheduled: true,
    timezone: "Etc/UTC"
});







