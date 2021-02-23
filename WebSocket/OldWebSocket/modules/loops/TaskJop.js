

var JopTaskOnWork = {};


setInterval(function () {

    var Now = Date.now() / 1000;

    Elkaisar.DB.SelectFrom("*", "city_jop_hiring", "time_end <= ?", [Now], function (Tasks) {
        Elkaisar.DB.Delete("city_jop_hiring", "time_end <= ?", [Now]);

        Tasks.forEach(function (Task, Index) {
            if(JopTaskOnWork[Task.id])
                return ;
            JopTaskOnWork[Task.id] = true;
            var Prestige = Task["num"];
            Elkaisar.DB.Update("prestige = prestige + ?", "player", "id_player = ?", [Prestige, Task.id_player]);
            Elkaisar.DB.Update("exp = exp + ?", "hero", "id_hero = (SELECT console FROM city WHERE id_city = ?)", [Prestige * 2, Task["id_city"]]);

            var Produce = Elkaisar.Config.JopReq[Task["jop_place"]]["produce"];

            Elkaisar.DB.Update("`" + Produce + "` = `" + Produce + "` + ?", "city_jop", "id_city = ?", [Task["num"], Task["id_city"]], function (){
                Elkaisar.Lib.LSaveState.afterCityColonizer(Task.id_player, Task.id_city);
            });
            


            var Player = Elkaisar.Base.getPlayer(Task.id_player);
            if (!Player)
                return ;
            Player.connection.sendUTF(JSON.stringify({
                classPath : "TimedTask.Jop",
                Task      : Task,
                prestige  : Prestige
            }));
        });
    });

}, 1000);


setInterval(function (){ArmyTaskOnWork = {};}, 41*60*1000);