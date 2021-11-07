class LPlayer {

    /*
     * 
     * @type type
     
     */


    static godP = {
        "gate_1": "attack",
        "gate_2": "defence",
        "gate_3": "vit",
        "gate_4": "damage"
    };

    /*static  RANK_POINT_PLUSE = {
        "gate_1": {
//  1     2     3     4     5     6     7     8    9      10
            "attack": [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        },
        "gate_2": {
            "defence": [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75
            ]
        },
        "gate_3": {
            "vit": [
                750, 700, 700, 700, 600, 600, 600, 450, 450, 450,
                300, 300, 300, 300, 300, 250, 250, 250, 250, 250,
                150, 150, 150, 150, 150, 100, 100, 100, 100, 100
            ]
        },
        "gate_4": {
            "damage": [
                200, 200, 200, 150, 150, 150, 150, 150, 140, 140,
                140, 130, 130, 130, 120, 120, 120, 110, 110, 100,
                100, 80, 80, 80, 80, 50, 50, 50, 50, 50
            ]
        }
    };*/
    static RANK_POINT_PLUSE = {
        "gate_1": {
            "attack": [
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75]
        },
        "gate_2": {
            "defence": [ 
                300, 300, 300, 200, 200, 200, 200, 200, 150, 150,
                145, 145, 140, 140, 130, 130, 120, 120, 110, 110,
                100, 100, 100, 100, 100, 75, 75, 75, 75, 75]
        },
        "gate_3": {
            "vit": [
                750, 700, 700, 700, 600, 600, 600, 450, 450, 450,
                300, 300, 300, 300, 300, 250, 250, 250, 250, 250,
                150, 150, 150, 150, 150, 100, 100, 100, 100, 100
            ]
        },
        "gate_4": {
            "damage": [
                200, 200, 200, 150, 150, 150, 150, 150, 140, 140,
                140, 130, 130, 130, 120, 120, 120, 110, 110, 100,
                100, 80, 80, 80, 80, 50, 50, 50, 50, 50
            ]
        }
    }
    static getPlayerdata(Player, callBack) {
        Elkaisar.DB.SelectFrom(
            "name , id_player, guild, id_guild, porm, avatar",
            "player", "id_player = ?", [Player.idPlayer], function (Res) {
                if (Res[0])
                    Player.Player = {
                        idGuild: Res[0].id_guild,
                        Porm: Res[0].porm,
                        GuildName: Res[0].guild,
                        PlayerName: Res[0].name,
                        PlayerAvatar: Res[0].avatar,
                        idPlayer: Res[0].id_player
                    };
                if (callBack)
                    callBack();
            });
    }

    static getPlayerStudy(Player, callBack) {
        Elkaisar.DB.SelectFrom(
            "infantry , riding, army, safe, medicine",
            "player_edu", "id_player = ?", [Player.idPlayer], function (Res) {

                if (Res[0])
                    Player.Study = Res[0];

                if (callBack)
                    callBack();
            });

    }
    static getPlayerState(Player, callBack) {
        Elkaisar.DB.SelectFrom(
            "attack_10 , defence_10, medical",
            "player_stat", "id_player = ?", [Player.idPlayer], function (Res) {
                if (Res[0])
                    Player.State = Res[0];
                if (callBack)
                    callBack();
            });
    }

    /* for ($iii = 1; $iii < 4; $iii++) {
     
     if (is_null($playerGate[("gate_" . $iii)])) {
     continue;
     }
     
     $gate = selectFromTable("*", "god_gate_" . $iii, "id_player = :idp", ["idp" => $idPlayer])[0];
     $rank = selectFromTable(" FIND_IN_SET( gate_1, "
     . "( SELECT GROUP_CONCAT( gate_1 ORDER BY gate_1 DESC ) FROM god_gate )"
     . " ) AS rank ",
     "god_gate", "id_player = :idp", ["idp" => $idPlayer])[0]["rank"];
     
     if (!is_numeric($rank) && $rank <= 30) {
     
     $effect[$pluseEffect[$godP[("gate_" . $iii)]]] += static::$RANK_POINT_PLUSE[("gate_" . $iii)][("gate_" . $iii)][$rank];
     }
     
     for ($jjj = 1; $jjj < 4; $jjj++) {
     $type = $gate[("cell_" . $jjj . "_type")];
     $score = $gate[("cell_" . $jjj . "_score")];
     
     $effect[$type] += $score;
     }
     }
     */
    static getPlayerGodGate(Player, Battel, callBack = function () { }) {
        var Unit = Elkaisar.World.getUnit(Battel.x_coord, Battel.y_coord);
        var effect = {
            "vit": 0,
            "damage": 0,
            "attack": 0,
            "defence": 0
        };
        Player.GodGate = effect;
        if (!Player || !Player.idPlayer)
            return callBack();
        if (!Elkaisar.Lib.LWorldUnit.isGodGateEffective(Unit.ut))
            return callBack();

        Elkaisar.DB.SelectFrom("*", "god_gate", "id_player = ?", [Player.idPlayer], function (Res) {

            var PlayerGate = Res[0];
            if (!PlayerGate)
                return;
            Player.GodGate.Gate = PlayerGate;
            LPlayer.getGodGateBaseEff(Player, 1);
            LPlayer.getGodGateBaseEff(Player, 2);
            LPlayer.getGodGateBaseEff(Player, 3);
            LPlayer.getGodGateBaseEff(Player, 4);
            if (callBack)
                callBack();
        });
    }

    static getGodGateBaseEff(Player, GateIndex, callBack) {
        Elkaisar.DB.SelectFrom("*", `god_gate_${GateIndex}`, "id_player = ?", [Player.idPlayer], function (Res) {
            var Gate = Res[0];
            if (!Gate)
                return;

            Player.GodGate[Gate.cell_1_type] += Gate.cell_1_score;
            Player.GodGate[Gate.cell_2_type] += Gate.cell_2_score;
            Player.GodGate[Gate.cell_3_type] += Gate.cell_3_score;

            Elkaisar.DB.SelectFrom(
                `FIND_IN_SET(gate_${GateIndex}, ( SELECT GROUP_CONCAT( gate_${GateIndex} ORDER BY gate_${GateIndex} DESC ) FROM god_gate ) ) AS rank`,
                "god_gate", "id_player = ?", [Player.idPlayer], function (Res) {

                    var GateRank = Res[0];

                    if (!GateRank)
                        return;

                    Player.GodGate[LPlayer.godP[`gate_${GateIndex}`]] += LPlayer.RANK_POINT_PLUSE[`gate_${GateIndex}`][LPlayer.godP[`gate_${GateIndex}`]][GateRank.rank] || 0;

                    if (callBack)
                        callBack();

                });
        });
    }

    static async  takePlayerGold(idPlayer, amount) {
        const GoldAmount = await Elkaisar.DB.ASelectFrom("*", "player", "id_player = ?", [idPlayer]);
        if (!GoldAmount)
            return false;
        if (GoldAmount.legnth <= 0)
            return false;
        if (GoldAmount[0].gold < amount)
            return false;
        Elkaisar.DB.Update("gold = gold - ?", "player", "id_player = ?", [amount, idPlayer]);
        return true;

    }

}



module.exports = LPlayer;
